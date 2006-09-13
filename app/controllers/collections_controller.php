<?php

class CollectionsController extends Kea_Action_Controller
{
	public function __construct()
	{
		$this->attachBeforeFilter(
			new RequireLogin( array( '_edit' => '10' ) )
		);
		
		$this->attachBeforeFilter(
			new RequireLogin( array( '_delete' => '10' ) )
		);
	}
	
	protected function _add()
	{
		if( self::$_request->getProperty( 'collection_add' ) ) {
			if( $this->commitForm() ) {
				$this->redirect( BASE_URI . DS . 'collections' . DS . 'all' );
				return;
			}
		}
		$c = new Collection;
		$c->collection_active = '1';
		return $c;
	}
	
	protected function _edit()
	{
		if( self::$_request->getProperty( 'collection_edit' ) ) {
			if( $this->commitForm() ) {
				$this->redirect( BASE_URI . DS . 'collections' . DS . 'all' );
				return;
			}
		} else {
			return $this->_findById();
		}
	}
	
	private function commitForm()
	{
		$collection = new Collection( self::$_request->getProperty( 'collection' ) );
		if( empty($collection->collection_parent) )
		{
			$collection->collection_parent = 'NULL';
		}
		if( $this->validates( $collection ) ) {
			return $collection->save();
		}
		return false;
	}
	
	protected function _delete()
	{
		if( $id = self::$_request->getProperty( 'collection_id' ) ) {
			/**
			 * Nested functionality
			 * Rearrange order so that nested child collections have the collection_parent of the current collection
			 */
			$collections = $this->_findChildren($id);
			foreach($collections as $collection)
			{
				$collection->collection_parent = $this->_findById($id)->collection_parent;
				$collection->save();
			}
			
			/**
			 * This will delete all the objects in the collection if the right $_request variable is set,
			 * for obvious reasons only a super user should be able to do this.
			 *
			 */
			if( self::$_session->isSuper() )
			{
				$deleteObjects = self::$_request->getProperty( 'delete_objects' );
				if($deleteObjects)
				{
					$objMapper = new Object_Mapper;
					$objects = $objMapper->find()->where('collection_id = ?', $id)->execute();
					foreach($objects as $object)
					{
						$object->delete();
					}
				}
			}
			
			$mapper = new Collection_Mapper;
			$mapper->delete( $id );
			
			
			$this->redirect( BASE_URI . DS . 'collections' . DS . 'all' );
		}
	}
	
	protected function _findById( $id = null)
	{
		if( !$id )
		{
			$id = self::$_request->getProperty( 'id' ) ?
					self::$_request->getProperty( 'id' ) : 
						(isset( self::$_route['pass'][0] ) ?
						self::$_route['pass'][0] :
						0);	
		}

		$mapper = new Collection_Mapper();

		return $mapper->find()
					  ->where( 'collection_id = ?', $id )
					  ->execute();
	}
	
	protected function _all( $type = 'object')
	{
		$mapper = new Collection_Mapper;
		switch( $type ) {
			case( 'object' ):
				return $mapper->allObjects();
			break;
			case( 'array' ):
				return $mapper->allArray();
			break;
		}
		
		return false;
	}
	
	/**
	 * Find the children of the current collection (this only works if collection_parent is implemented)
	 *
	 * @return void
	 * @author Kris Kelly
	 **/
	protected function _findChildren($parent = NULL)
	{
		$mapper = new Collection_Mapper();
		if($parent)
		{
		return $mapper->find()
					  ->where( 'collection_parent = ?', $parent )
					  ->execute();		
		}
		else
		{
		return $mapper->find()
					->where( 'collection_parent IS NULL' )
					->execute();
		}
	}
	
	protected function _findActive()
	{
		$mapper = new Collection_Mapper();
		return $mapper->find()
					  ->where( 'collection_active = ?', '1' )
					  ->execute();
	}
	
	protected function _addToCollection()
	{
		$obj_id = self::$_request->getProperty( 'object_id' );
		$coll_id = self::$_request->getProperty( 'collection_id' );

		$mapper = new Collection_Mapper();
		if( empty($obj_id) || empty($coll_id) ) {
			self::$_session->flash('Please choose a collection to assign the objects.');
			return null;
		} 
		return $mapper->addToCollection( $obj_id, $coll_id );
	}
	
	/**
	 * This is a total hack to display nested collections all at once on the same page using recursive partials.
	 * It would be better just to use findChildren() and deprecate this.  See content/admin/collections/all.php
	 *
	 * @return void
	 * @author Kris Kelly
	 **/
	public function displayNested($template, $useList = TRUE, $style = NULL, $parent_id = NULL)
	{
		$mapper = new Collection_Mapper;
		$collections = $this->_findChildren($parent_id);
		if ($collections->total() > 0)
		{
			if($useList) echo "\n<ul>\n";
			foreach( $collections as $collection )
			{	
					echo ($useList) ? "\t<li>" : "\t<div style=\"$style\">\n";
					include($template);
					$this->displayNested($template, $useList, $style, $collection->collection_id);
					echo ($useList) ? "\t</li>\n" : "\t</div>\n";
				
			}
			if($useList) echo "\n</ul>\n";
		}
	}
}

?>