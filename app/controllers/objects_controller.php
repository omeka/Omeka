<?php

class ObjectsController extends Kea_Action_Controller
{
	/**
	 * Constructor
	 *
	 * 
	 * @author Nate Agrin
	 **/
	public function __construct()
	{
		$this->attachBeforeFilter(
			new RequireLogin( array( '_add' => '10' ) )
		);

		$this->attachBeforeFilter(
			new RequireLogin( array( '_edit' => '10' ) )
		);
	}
	
	/**
	 * Returns the next object in the database 
	 * 
	 * @param int $id An object_id
	 * @return Object Returns the entire object, otherwise returns false
	 * @author Nate Agrin
	 **/
	protected function _getNextObjectID( $id = null )
	{
		if( !$id )
		{
			$id = self::$_request->getProperty( 'id' ) ?
					self::$_request->getProperty( 'id' ) : 
						(isset( self::$_route['pass'][0] ) ?
						self::$_route['pass'][0] :
						0);	
		}
		
		$id = (int) $id;

		$mapper = new Object_Mapper;
		$select = $mapper->find()
						->where( 'object_id > ?', $id );
		$this->applyPermissions( $select );
		$obj = $select->execute()->getObjectAt(0);

		if( $obj )
		{
			return $obj;
		}
		return false;
	}

	/**
	 * Returns the object located immediately prior in the database
	 *
	 * @param int $id An object_id 
	 * @return Object Returns the entire object, otherwise returns false
	 * @author Nate Agrin
	 **/
	protected function _getPrevObjectID( $id = null )
	{
		if( !$id )
		{
			$id = self::$_request->getProperty( 'id' ) ?
					self::$_request->getProperty( 'id' ) : 
						(isset( self::$_route['pass'][0] ) ?
						self::$_route['pass'][0] :
						0);	
		}
		
		$id = (int) $id;

		$mapper = new Object_Mapper;
		$select = $mapper->find()
						->where( 'object_id < ?', $id )
						->order( array( 'object_id' => 'DESC' ) );
		$this->applyPermissions( $select );
		$obj = $select->execute()->getObjectAt(0);

		if( $obj )
		{
			return $obj;
		}
		return false;
	}

	/**
	 * Find an object by ID
	 *
	 * @param int $id object_id
	 * @return Object Returns the entire object, otherwise returns false
	 * @author Nate Agrin
	 **/
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
		
		$id = (int) $id;
		
		$mapper = new Object_Mapper();
		$select = $mapper->select()
					  ->joinLeft( 'categories', 'categories.category_id = objects.category_id' )
					  ->where( 'objects.object_id = ?', $id );
		$this->applyPermissions( $select );
		$obj = $mapper->findObjects( $select );
		
		if ($obj->object_id):
			$obj->getCategoryMetadata()
				->getCreator()
				->getLocation()
				->getContributor()
				->getTags()
				->getFiles();					
			return $obj;
		else:
			return false;
		endif;
	}

	/**
	 * Gets the current page number when browsing objects
	 *
	 * @return int Page number
	 * @author Nate Agrin
	 **/
	protected function _getPageNum()
	{
		$page = isset( self::$_route['pass'][0] ) ? (int) self::$_route['pass'][0] : 1;
		return $page;
	}
	
	/**
	 * Chooses a list of objects to display based on $_request data
	 *
	 * Handles both search and display of objects within the browse page.
	 *
	 * @param bool $short_desc Show a short description of each object 
	 * @param int $num_objects The number of objects per page
	 * @param bool $check_location Include objects with valid location coordinates
	 * @return Object_Collection Returns a collection of objects with the selected criteria
	 * @author Nate Agrin
	 **/
	protected function _paginate( $short_desc = true, $num_objects = 9, $check_location = false )
	{	
		$page = isset( self::$_route['pass'][0] ) ? (int) self::$_route['pass'][0] : 1;

		$mapper = new Object_Mapper();
		
		if( $short_desc ) {
			$select = $mapper->select( "*, RPAD( SUBSTRING( object_description, 1, 140 ),  LENGTH( SUBSTRING( object_description, 1, 140 ) ) + 3, '.') as short_desc" )
							 ->joinLeft( 'categories', 'categories.category_id = objects.category_id' )
							 ->order( array( 'objects.object_id' => 'DESC' ) );
		}

		/* Begin consent screening
		 	Assumes privilege levels of: 
			20 - Researcher
			10 - Admin
			1 - Superuser
		*/

		// Screen out objects for which consent hasn't been given unless an admin
/*		if( !self::$_session->isAdmin() ) {
			$select->where( 'objects.object_contributor_consent = ?', 'yes' );
		}
*/		
		// End consent screening
		
		if( self::$_request->getProperty( 'collection' ) ) {
			$select->where( 'objects.collection_id = ?', self::$_request->getProperty( 'collection' ) );
		}
		
		if( $cat = self::$_request->getProperty( 'objectType' ) )
		{
			$select->where( 'objects.category_id = ?', $cat );
		}
		
		if( self::$_request->getProperty( 'featured' ) ) {
			$select->where( 'objects.object_featured = ?', self::$_request->getProperty( 'featured' ) );
		}
		
		if( self::$_request->getProperty( 'contributor' ) ) {
			$select->where( 'objects.contributor_id = ?', self::$_request->getProperty( 'contributor' ) );
		}
		
		if( self::$_request->getProperty( 'status' ) ) {
			$select->where( 'objects.object_status = ?', self::$_request->getProperty( 'status' ) );
		}
		
		if( self::$_request->getProperty( 'type' ) ) {
			$select->where( 'objects.category_id = ?', self::$_request->getProperty( 'type' ) );
		}
		
		if( $tags = self::$_request->getProperty( 'tags' ) )
		{
			$all_tags = explode( ',', $tags );
			foreach($all_tags as $tag)
			{
				$tag = trim( $tag );
				$select->orWhere( 'tags.tag_name = ?', $tag );
			}
			
			$select->joinLeft( 'objects_tags', 'objects_tags.object_id = objects.object_id' )
				   ->joinLeft( 'tags', 'objects_tags.tag_id = tags.tag_id' )
					->group( 'objects.object_id' );
		}
		
		if( self::$_request->getProperty( 'search' ) ) {
			
/*			$select->orWhere( 'objects.object_title LIKE ?', '%'.self::$_request->getProperty( 'search' ).'%' );
			$select->orWhere( 'objects.object_description LIKE ?', '%'.self::$_request->getProperty( 'search' ).'%' );
			$select->join( 'metatext', 'objects.object_id = metatext.object_id');
			$select->orWhere( 'metatext.metatext_text LIKE ?', '%'.self::$_request->getProperty( 'search' ).'%' );
			$select->join( 'objects_tags', 'objects.object_id = objects_tags.object_id');
			$select->join( 'tags', 'objects_tags.tag_id = tags.tag_id');
			$select->orWhere( 'tags.tag_name LIKE ?', '%'.self::$_request->getProperty( 'search' ).'%' );
*/
			$select->join( 'metatext', 'objects.object_id = metatext.object_id');
			$select->join( 'objects_tags', 'objects.object_id = objects_tags.object_id');
			$select->join( 'tags', 'objects_tags.tag_id = tags.tag_id');

			$select->where( '( objects.object_title LIKE %'.self::$_request->getProperty( 'search' ).'% 
			OR objects.object_description LIKE %'.self::$_request->getProperty( 'search' ).'%
			OR metatext.metatext_text LIKE %'.self::$_request->getProperty( 'search' ).'%
			OR tags.tag_name LIKE %'.self::$_request->getProperty( 'search' ).'% ) AND objects.object_title != ?', '' );
		}
		
		if( $check_location )
		{
			$select->join( 'location', 'objects.object_id = location.object_id' );
			$select->where( 'location.latitude != ?', '' );
			$select->where( 'location.longitude != ?', '' );
		}
		
		$select->group( 'objects.object_id' );

		$this->applyPermissions( $select );

		return $mapper->paginate( $select, $page, $num_objects, 'objectsTotal' );
	}
	
	/**
	 * Filters objects based on user access permission
	 *
	 * If the user is not an admin, this function will filter objects where the contributor has not given consent,
	 * has not given permission to post the object (NOTE [KBK]: Current configuration only checks for Admin privileges, 
	 * we need to reconfigure so that researcher-level users will also be able to access the archives)
	 *
	 * @param Kea_DB_Select Select Object containing the parameters for selecting object(s) from the database 
	 * @return Kea_DB_Select The select object with permission-based filtering
	 * @author Nate Agrin
	 **/
	private function applyPermissions( Kea_DB_Select $select )
	{
		if( !self::$_session->isAdmin() )
		{
			$select->where( 'objects.object_contributor_consent = ?', 'yes' )
					->where( '(objects.object_contributor_posting = "anonymously" OR objects.object_contributor_posting = "yes") AND objects.object_status = ?', 'approved' );
				//	->orWhere( 'objects.object_contributor_posting = ?', 'anonymously' )
				//	->where( 'objects.object_status = ?', 'approved' );
		}
				
		return $select;	
	}
	
	/**
	 * Add an object to the archive
	 *
	 * If the object_add form has been submitted, commit the object to the database and redirect the user to view all objects,
	 * otherwise reset the saved form to empty
	 * @return void
	 * @author Nate Agrin
	 **/
	protected function _add()
	{
		if( self::$_request->getProperty( 'object_add' ) ) {
			if( $this->commitForm() ) {
				$this->redirect( BASE_URI . DS . 'objects' . DS . 'all' );
				return;
			}
			return;
		}
		self::$_session->setValue( 'object_form_saved', null );
	}
	
	/**
	 * Edit an archived object
	 *
	 * If the object_edit form has been submitted, commit the form to the database and redirect the user to that 
	 * object's entry.  If the form was not submitted, then find the current object and prepare that object's data
	 * to be displayed on the 'edit' page.
	 *
	 * @return Object Returns the object that is going to be edited
	 * @author Nate Agrin
	 **/
	protected function _edit()
	{
		if( self::$_request->getProperty( 'object_edit' ) ) {
			if( $this->commitForm() ) {
				$object = $this->_findById();
				$this->redirect( BASE_URI . DS . 'objects' . DS . 'show' . DS . $object->object_id );
				return;
			}
		} else {
			$object_c = $this->_findById();
			$object = $object_c->current();
			
			$sudo = array();
			$object_a = array(	'object_id'						=> $object->getId(),
								'object_status'					=> $object->object_status,
								'object_title'					=> $object->object_title,
								'object_description'			=> $object->object_description,
								'category_id'					=> $object->category_id,
								'collection_id'					=> $object->collection_id,
								'object_language'				=> $object->object_language,
								'contributor_id'				=> $object->contributor_id,
								'object_contributor_posting'	=> $object->object_contributor_posting,
								'object_contributor_consent'	=> $object->object_contributor_consent,
								'object_publisher'				=> $object->object_publisher,
								'object_rights'					=> $object->object_rights,
								'object_relation'				=> $object->object_relation );

			$sudo['object_added'] = $object->object_added;
			$sudo['object_modified'] = $object->object_modified;

			if( $object->object_language != 'eng' && $object->object_language != 'fra' )
			{
				$object_a['object_language'] = 'other';
				$sudo['object_language_other'] = $object->object_language;
			}
			else
			{
				$object_a['object_language'] = $object->object_language;
				$sudo['object_language_other'] = null;
			}
			
			if( $object->creator_id != $object->contributor_id )
			{
				$sudo['creator'] = 'no';
				$sudo['creator_other'] = $object->creator_other;
			}
			else
			{
				$sudo['creator'] = 'yes';
				$sudo['creator_other'] = null;
			}
			
			if( $object->object_date )
			{
				$sudo['object_creation_month'] = date( 'm', strtotime( $object->object_date ) );
				$sudo['object_creation_day'] = date( 'd', strtotime( $object->object_date ) );
				$sudo['object_creation_year'] = date( 'Y', strtotime( $object->object_date ) );
			}
			else
			{
				$sudo['object_creation_month'] = null;
				$sudo['object_creation_day'] = null;
				$sudo['object_creation_year'] = null;
			}

			if( $object->object_coverage_start )
			{
				$sudo['object_coverage_start_month'] = date( 'm', strtotime( $object->object_coverage_start ) );
				$sudo['object_coverage_start_day'] = date( 'd', strtotime( $object->object_coverage_start ) );
				$sudo['object_coverage_start_year'] = date( 'Y', strtotime( $object->object_coverage_start ) );
			}
			else
			{
				$sudo['object_coverage_start_month'] = null;
				$sudo['object_coverage_start_day'] = null;
				$sudo['object_coverage_start_year'] = null;
			}
			
			if( $object->object_coverage_end )
			{
				$sudo['object_coverage_end_month'] = date( 'm', strtotime( $object->object_coverage_end ) );
				$sudo['object_coverage_end_day'] = date( 'd', strtotime( $object->object_coverage_end ) );
				$sudo['object_coverage_end_year'] = date( 'Y', strtotime( $object->object_coverage_end ) );
			}
			else
			{
				$sudo['object_coverage_end_month'] = null;
				$sudo['object_coverage_end_day'] = null;
				$sudo['object_coverage_end_year'] = null;
			}
			
			$location_a = array(	'address'	=> $object->location->address,
									'zipcode'	=> $object->location->zipcode,
									'latitude'	=> $object->location->latitude,
									'longitude'	=> $object->location->longitude,
									'mapType'	=> $object->location->mapType,
									'zoomLevel'	=> $object->location->zoomLevel,
									'cleanAddress'	=> $object->location->cleanAddress,
									'location_id'	=> $object->location->getId() );
			$sudo['Object'] = $object_a;
			$sudo['Location'] = $location_a;
			self::$_session->setValue( 'object_form_saved', $sudo );
			
			return $object;
		}
	}
	
	private function commitForm()
	{	
		$adapter = Kea_DB_Adapter::instance();
		$adapter->beginTransaction();

		$object = new Object( self::$_request->getProperty( 'Object' ) );
		
		if( self::$_request->getProperty('object_creation_month')
		 	&& self::$_request->getProperty('object_creation_day')
			&& self::$_request->getProperty('object_creation_year') )
		{
			$create_month = self::$_request->getProperty('object_creation_month');
			$create_day = self::$_request->getProperty('object_creation_day');
			$create_year = self::$_request->getProperty('object_creation_year');
			$object->object_date = $create_year . '-' . $create_month . '-' . $create_day;
		}
		else
		{
			$object->object_date = 'NULL';
		}
		
		if( self::$_request->getProperty('object_coverage_start_month')
			&& self::$_request->getProperty('object_coverage_start_day')
			&& self::$_request->getProperty('object_coverage_start_year') )
		{
			$c_start_month = self::$_request->getProperty('object_coverage_start_month');
			$c_start_day = self::$_request->getProperty('object_coverage_start_day');
			$c_start_year = self::$_request->getProperty('object_coverage_start_year');
			$object->object_coverage_start = $c_start_year . '-' . $c_start_month . '-' . $c_start_day;
		}
		else
		{
			$object->object_coverage_start = 'NULL';
		}
		
		if( self::$_request->getProperty('object_coverage_end_month') 
			&& self::$_request->getProperty('object_coverage_end_day') 
			&& self::$_request->getProperty('object_coverage_end_year') )
		{
			$c_end_month = self::$_request->getProperty('object_coverage_end_month');
			$c_end_day = self::$_request->getProperty('object_coverage_end_day');
			$c_end_year = self::$_request->getProperty('object_coverage_end_year');
			$object->object_coverage_end = $c_end_year . '-' . $c_end_month . '-' . $c_end_day;
		}
		else
		{
			$object->object_coverage_end = 'NULL';
		}
		
		if( self::$_request->getProperty( 'creator' ) == 'yes' && !empty( $obj->contributor_id ) )
		{
			$object->creator_id = $obj->contributor_id;
		}
		elseif( self::$_request->getProperty( 'creator_other' ) )
		{
			$object->creator_id = null;
			$object->creator_other = self::$_request->getProperty( 'creator_other' );
		}
		else
		{
			$object->creator_id = null;
			$object->creator_other = null;
		}
		
		if( $object->object_language == 'other' && self::$_request->getProperty('object_language_other') )
		{
			$object->object_language = self::$_request->getProperty('object_language_other');
		}
		
		if( empty( $object->collection_id ) )
		{
			$object->collection_id = 'NULL';
		}
		
		if( empty( $object->contributor_id ) )
		{
			$object->contributor_id = 'NULL';
		}
		
		// This is a kludge to make sure that editing an object doesn't destroy its relationship with the user who uploaded it [JMG]
		if ( !self::$_request->getProperty('object_edit') ) 
		{
			$object->user_id = self::$_session->getUser()->getId();
		}

		if( $this->validates( $object ) )
		{
			$object->save();
		}
		
		$location = new Location( self::$_request->getProperty( 'Location' ) );
		if( $location->hasValues() )
		{
			$location->object_id = $object->object_id;
			$location->latitude = round( $location->latitude, 5 );
			$location->longitude = round( $location->longitude, 5 );
			if( $this->validates( $location ) )
			{
				$location->save();	
			}
		}
		
		$tags = new Tags( self::$_request->getProperty( 'tags' ) );
		$tags->object_id = $object->object_id;
		$tags->user_id = self::$_session->getUser()->getId();

		//if( $this->validates( $tags ) )
		//{
			$tags->save();
		//}
		
		// Add the metadata
		if( $metadata = self::$_request->getProperty( 'metadata' ) )
		{
			foreach( $metadata as $k => $v )
			{
				$m = new Metatext( $v );
				$m->object_id = $object->object_id;
				if( $this->validates( $m ) )
				{
					$m->save();
				}
			}
		}
		
		File::add( $object->getId(), $object->contributor_id, 'objectfile' );

		if( count( $this->validationErrors ) > 0 ) {
			self::$_session->setValue( 'object_form_saved', $_REQUEST );
			$adapter->rollback();
			return false;
		} else {
			self::$_session->setValue( 'object_form_saved', null );
			$adapter->commit();
			return true;
		}
	}
	
	protected function _total()
	{
		$mapper = new Object_Mapper();
		return $mapper->total();
	}
	
	protected function _totalInCategory( $category_id )
	{
		$mapper = new Object_Mapper();
		return $mapper->totalSliced( $category_id , null);
	}
	
	protected function _totalInCollection( $collection_id )
	{
		$mapper = new Object_Mapper();
		return $mapper->totalSliced( null, $collection_id );

	}

	
	protected function _deleteCategoryAssociation( $object_id )
	{
		$adapter = Kea_DB_Adapter::instance();
		$adapter->update( 'objects', array( 'category_id'	=> 'NULL' ), 'objects.object_id = \'' . $object_id . '\'' );
		$adapter->delete( 'metatext', 'object_id = \'' . $object_id . '\'' );
		if( $adapter->error() )
		{
			return $adapter->error();
		}
		else
		{
			return true;
		}
	}
	
	protected function _delete()
	{
		if( $id = self::$_request->getProperty( 'object_id' ) ) {
			/*
				First get the files and delete them.
				This is reinforced through foreign key constraints in mysql
				but the underlying physical files should also be deleted.
			*/
			$object = $this->_findById( $id );
			foreach( $object->files as $file )
			{
				File::delete( $file->getId() );
			}

			$mapper = new Object_Mapper;
			$mapper->delete( $id );
			$this->redirect( BASE_URI . DS . 'objects' . DS . 'all' );
		}
	}
	
	protected function _findFeatured()
	{
		$mapper = new Object_Mapper;
		$featured = $mapper->find()
							->where( 'object_featured = ?', '1' );
		$this->applyPermissions( $featured );
		return $featured->execute(); 
	}

	protected function _findRandomFeatured()
	{
		$mapper = new Object_Mapper;
		$featured = $mapper->find()
							->where( 'object_featured = ?', '1' );
		$this->applyPermissions( $featured );
		$all = $featured->execute(); 
		return $all->getObjectAt( mt_rand( 0, $all->total() - 1 ) );
	}

	protected function _findRandomFeaturedWithThumb()
	{
/*		// First see if there even are thumbnails
		$mapper = new Object_Mapper;
		$has_thumbs = $mapper->find( 'objects.object_id, files.file_id' )
							->join( 'files', 'objects.object_id = files.object_id' )
							->where( 'objects.object_featured = ?', '1' )
							->where( 'files.file_thumbnail_name != ?', '')
							->execute();

		if( $has_thumbs->total() == 0 )
		{
			return false;
		}
		
		// Or if there are featured with thumbs...
		$featured = $has_thumbs->getObjectAt( mt_rand( 0, $has_thumbs->total() - 1 ) );
		$new = $this->findById($featured->object_id);
		echo $new->object_title;
		print_r($new); exit;
		
		foreach( $featured->files as $file ):
			if( $file->file_thumbnail_name ):
				return $featured;
				exit;
			endif;
		endforeach;
*/
		$featured = $this->findRandomFeatured();
		$featured->getFiles();
		//print_r($featured); exit;
		foreach( @$featured->files as $file ):
			if( $file->file_thumbnail_name ):
				return $featured;
			endif;
		endforeach;
		
		return $this->findRandomFeaturedWithThumb();

	}
	
	protected function _getMapObjects( $map_objects = 40 )
	{
		return $this->_paginate( true, $map_objects, true );
	}
	
	protected function _getRandomMapFeatured()
	{
		$mapper = new Object_Mapper;
		$featured = $mapper->find( "*, RPAD( SUBSTRING( object_description, 1, 140 ),  LENGTH( SUBSTRING( object_description, 1, 140 ) ) + 3, '.') as short_desc" )
							->where( 'object_featured = ?', '1' )
							->join( 'location', 'location.object_id = objects.object_id' )
							->where( 'location.latitude != ?', '')
							->where( 'location.longitude != ?', '')
							->execute();
		return $featured->getObjectAt( mt_rand( 0, $featured->total() - 1 ) );
	}

// End class
}

?>