<?php

class CategoriesController extends Kea_Action_Controller
{	

	public function __construct()
	{
		$this->attachBeforeFilter(
			new RequireLogin( array( 'create' => '10' ) )
		);
	}
	
	protected function _all( $type = 'object' )
	{
		$mapper = new Category_Mapper;
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
	
	protected function _edit()
	{
		if( self::$_request->getProperty( 'category_edit' ) ) {
			if( $this->commitForm() ) {
				$this->redirect( BASE_URI . DS . 'categories' . DS . 'all' );
				return;
			}
		} else {
			return $this->_findById();
		}
	}

	protected function _delete()
	{
		if( $id = self::$_request->getProperty( 'category_id' ) ) {
			
			// Delete category
			$mapper = new Category_Mapper;
			$mapper->delete( $id );
			
			$this->redirect( BASE_URI . DS . 'categories' . DS . 'all' );
		}
	}

	private function commitForm()
	{
		$category = new Category( self::$_request->getProperty( 'category' ) );
		if( $this->validates( $category ) ) {
			
			return $this->add();
			//return $category->save();
			
		}
		return false;
	}
	
	protected function _total()
	{	
		$mapper = new Category_Mapper;
		return $mapper->total();
	}
	
	protected function _findById( $id = null )
	{
		if( !$id )
		{
			$id = self::$_request->getProperty( 'id' ) ?
					self::$_request->getProperty( 'id' ) : 
						(isset( self::$_route['pass'][0] ) ?
						self::$_route['pass'][0] :
						0);
		}

		$mapper = new Category_Mapper();

		$obj = $mapper->find()
					  ->where( 'categories.category_id = ?', $id )
					  ->execute();
		
		$obj->getMetafields();
		
		return $obj;
	}
	
	//
	//	Unfinished
	//
	
	protected function _add()
	{	
		if( !self::$_request->getProperty( 'category_submitted' ) ) {
			self::$_session->setValue( 'category_form_saved', null );
			return;
		}

		// Save the request data from the form to the session	
		self::$_session->setValue( 'category_form_saved', $_REQUEST );

		$cat = new Category( self::$_request->getProperty( 'category' ) );
		$cat_map = new Category_Mapper;
		$mf_map = new Metafield_Mapper;
		
		
		try{
		// This should be a beforeSave filter or rule on Category objs
		if( !$cat_map->unique( 'category_name', $cat->category_name ) ) {
			throw new Kea_Exception(
				'An object category called "'
				. $cat->category_name .
				'" already exists.  Please choose a unique name.'
			);
		}

			$metafield_names = array();
			$metafield_coll = array();
			
			if( self::$_request->getProperty( 'metafields' ) ) {
				foreach( self::$_request->getProperty( 'metafields' ) as $metafield ) {
					// Check if both are not set and skip to the next field
					if( !$metafield['metafield_name'] && !$metafield['metafield_name_new'] ) continue;
				
					// Check if both metafield_name and metafield_name_new are set, fail if the are.
					if( $metafield['metafield_name'] && $metafield['metafield_name_new'] ) {
						throw new Kea_Exception(
							'Please only create a new metafield or select a preexisting one.'
						);
					}

					// Check if metafield_name_new is really unique
					if( $metafield['metafield_name_new']
						&& !$mf_map->unique( 'metafield_name', $metafield['metafield_name_new'] ) ) {
						throw new Kea_Exception(
							'`' . $metafield['metafield_name_new'] .
							'` already exists as a metafield name; select it from the list.'
						);
					}

					$name = notemptyor( $metafield['metafield_name'], $metafield['metafield_name_new'] );

					if( in_array( $name, $metafield_names ) ) {
						throw new Kea_Exception( '`'. $name .
							'` is a duplicate metafield name.  Ensure that each metafield name is unique.'
						);
					} else {
						$metafield_names[] = $name;
					}
				
				
					$mf = new Metafield( array(	'metafield_name'		=> trim( $name ),
												'metafield_description'	=> $metafield['metafield_description'] ) );
				
					$metafield_coll[] = $mf;
				}
			}

			try{
				// Start the transaction
				$db_adapter = Kea_DB_Adapter::instance();
				$db_adapter->beginTransaction();
				
				// Submit the data to the db				
				$cat->save();

				$mf_ids = array();
				foreach( $metafield_coll as $metafield ) {
					$mf = $mf_map->find()->where( 'metafield_name = ?', $metafield->metafield_name)->execute();
					if( $mf->total() > 0 ) {
						$mf_ids[] = $mf->getId();
					} else {
						$metafield->save();
						$mf_ids[] = $metafield->getId();
					}
				}
				
				// This should be handled in the categories mapper or metafields mapper
				$join_mapper = Kea_Domain_HelperFactory::getMapper('CategoriesMetafields');
				foreach( $mf_ids as $mf_id ) {
					$join_mapper->insert( $cat->getId(), $mf_id );
				}
echo 'made it';
				// Commit the transaction
				echo $db_adapter->error();
				if ($db_adapter->commit()) 
				{
					self::$_session->setValue( 'category_form_saved', null );
					$this->redirect( BASE_URI . DS . 'categories' . DS . 'all' );
				}
				
			} catch( Kea_Exception $e ) {
				// Rollback the transaction
				$db_adapter->rollback();
				return $e;
			}

		} catch( Kea_Exception $e ) {
			print_r($e);
			return $e;
		}
	}

}

?>