<?php

class TypesController extends Kea_Action_Controller
{	

	public function __construct()
	{
		$this->attachBeforeFilter(
			new RequireLogin( array( 'create' => '10' ) )
		);
	}
	
	protected function _all( $type = 'object' )
	{
		$mapper = new Type_Mapper;
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
		if( self::$_request->getProperty( 'type_edit' ) ) {
			if( $this->commitForm() ) {
				$this->redirect( BASE_URI . DS . 'types' . DS . 'all' );
				return;
			}
		} 
		else {
			self::$_session->setValue('type_form_saved', self::$_request->getProperties() );
			return $this->_findById();
		}
	}

	protected function _delete()
	{
		if( $id = self::$_request->getProperty( 'type_id' ) ) {
			
			// Delete type
			$mapper = new Type_Mapper;
			$mapper->delete( $id );
			
			$this->redirect( BASE_URI . DS . 'types' . DS . 'all' );
		}
	}

	private function commitForm()
	{
		$db_adapter = Kea_DB_Adapter::instance();
		$db_adapter->beginTransaction();
		
		$type = new Type( self::$_request->getProperty( 'type' ) );
		//print_r($_REQUEST); exit;
		/**
		 * If the type is valid, save it, get its available metafields, then cycle through the form's list of metafields.
		 * If there are any new metafield names, save those and replace the old ones in the list (but don't delete the old ones)
		 * Otherwise if there are any changes to the metafield dropdown, process those changes
		 * 
		 * @author Kris Kelly
		 **/
		if( $this->validates( $type ) ) {
			$type->save();
			$type->getMetafields();
			if($metafields = self::$_request->getProperty( 'metafields') )
			{
				//metafields are passed as arrays with metafield data + metafield_name_new if applicable
				foreach($metafields as $k => $v)
				{
					$mf_dropdown = new Metafield($v);
					
					//The stupid type editing form doesn't send an updated metafield ID, just a name, so we have to get the whole thing
					$mf_dropdown = Metafield::findBy('metafield_name', $mf_dropdown->metafield_name);
					if( $mf_dropdown instanceof Metafield_Collection )
					{
						$mf_dropdown = $mf_dropdown->getObjectAt(0);
					}
					
					if( !empty($v['metafield_name_new']) )
					{
						$mf_new = new Metafield( array('metafield_name' => $v['metafield_name_new']) );
						if( !$mf_new->uniqueName($mf_new->metafield_name) )
						{
							self::$_session->flash('The name '.$mf_new->metafield_name.' is already in use.  Please choose another name');
						}
						elseif( $mf_new->validates() ) 
						{
							$mf_new->save();
							$type->addMetafieldAssoc($mf_new);
							if( $mf_dropdown && $type->hasMetafield($mf_dropdown) )
							{
								$type->removeMetafieldAssoc($mf_dropdown);
							}
						}
					}
					//If somebody left an empty dropdown box, then they want the old metafield to go away
					elseif( is_null($mf_dropdown) )
					{
						$mf_old = Metafield::findBy('metafield_id', $v['metafield_id']);
						$type->removeMetafieldAssoc($mf_old);
					}
					//Or else it is a new metafield choice
					elseif( !$type->hasMetafield($mf_dropdown) )
					{
						$type->addMetafieldAssoc($mf_dropdown);
						
						if( $mf_old = $type->metafields->getObjectAt($k) )
						{
							$type->removeMetafieldAssoc($mf_old);
						}
					}
				}
			}
			//$type->getMetafields(); var_dump($type);
			//return $this->add();
			//return $type->save();
			//return TRUE;
			if( $db_adapter->error() )
			{
				$db_adapter->rollback();
				self::$_session->setValue( 'type_form_saved', self::$_request->getProperties() );
				return false;
			}
			else
			{
				$db_adapter->commit();
				self::$_session->setValue( 'type_form_saved', null );
				return true;
			}
		}
		else
		{
			self::$_session->setValue( 'type_form_saved', self::$_request->getProperties() );
		}
		//self::$_session->setValue( 'type_form_saved', self::$_request->getProperties() );
		return false;
	}
	
	protected function _total()
	{	
		$mapper = new Type_Mapper;
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

		$mapper = new Type_Mapper();

		$obj = $mapper->find()
					  ->where( 'types.type_id = ?', $id )
					  ->execute();
		
		$obj->getMetafields();
		return $obj;
	}
	
	//
	//	Unfinished
	//
	
	protected function _add()
	{	
		if( !self::$_request->getProperty( 'type_submitted' ) ) {
			self::$_session->setValue( 'type_form_saved', null );
			return;
		}

		// Save the request data from the form to the session	
		self::$_session->setValue( 'type_form_saved', $_REQUEST );

		$cat = new Type( self::$_request->getProperty( 'type' ) );
		$cat_map = new Type_Mapper;
		$mf_map = new Metafield_Mapper;
		
		
		try{
		// This should be a beforeSave filter or rule on Type objs
		if( !$cat_map->unique( 'type_name', $cat->type_name ) ) {
			throw new Kea_Exception(
				'An item type called "'
				. $cat->type_name .
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
				
				// This should be handled in the types mapper or metafields mapper
				$join_mapper = Kea_Domain_HelperFactory::getMapper('TypesMetafields');
				foreach( $mf_ids as $mf_id ) {
					$join_mapper->insert( $cat->getId(), $mf_id );
				}
echo 'made it';
				// Commit the transaction
				echo $db_adapter->error();
				if ($db_adapter->commit()) 
				{
					self::$_session->setValue( 'type_form_saved', null );
					$this->redirect( BASE_URI . DS . 'types' . DS . 'all' );
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