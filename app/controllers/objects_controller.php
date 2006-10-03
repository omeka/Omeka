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
						->where( 'object_id > ?', $id )
						->limit(1);
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
						->order( array( 'object_id' => 'DESC' ) )
						->limit(1);
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
					  ->joinLeft( 'types', 'types.type_id = objects.type_id' )
					  ->where( 'objects.object_id = ?', $id );
		$this->applyPermissions( $select );
		$obj = $mapper->findObjects( $select );
		
		if ($obj->object_id):
			$obj->getTypeMetadata()
				->getCreator()
				->getLocation()
				->getContributor()
				->getTags()
				->getFiles();					
			return $obj;
		else:
		throw new Kea_Domain_Exception('Cannot retrieve object with ID # '.$id);
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
	 * @return array Contains the following keys: 'total' => total # of objects found, 'page' => current page, 'per_page' => # per page, 'objects' => Object_Collection containing the objects found
	 * @author Nate Agrin
	 **/
	protected function _paginate( $short_desc = true, $num_objects = 9, $check_location = false )
	{	
		$page = isset( self::$_route['pass'][0] ) ? (int) self::$_route['pass'][0] : 1;

		$mapper = new Object_Mapper();
		
		if( $short_desc ) {
			$select = $mapper->select( "*, RPAD( SUBSTRING( object_description, 1, 140 ),  LENGTH( SUBSTRING( object_description, 1, 140 ) ) + 3, '.') as short_desc" )
							 ->joinLeft( 'types', 'types.type_id = objects.type_id' )
							 ->order( array( 'objects.object_id' => 'DESC' ) );
		}

		/* Begin consent screening
		 	Assumes privilege levels of: 
			20 - Researcher
			10 - Admin
			1 - Superuser
		*/
		
		if( self::$_request->getProperty( 'collection' ) ) {
			$select->where( 'objects.collection_id = ?', self::$_request->getProperty( 'collection' ) );
		}
		
		if( ( $cat = self::$_request->getProperty( 'objectType' ) )  
			|| ( $cat = self::$_request->getProperty( 'type' ) )
		 	 )
		{
			$select->where( 'objects.type_id = ?', $cat );
		}
		
		if( self::$_request->getProperty( 'featured' ) ) {
			$select->where( 'objects.object_featured = ?', self::$_request->getProperty( 'featured' ) );
		}
		
		if( self::$_request->getProperty( 'contributor' ) ) {
			$select->where( 'objects.contributor_id = ?', self::$_request->getProperty( 'contributor' ) );
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
		
	/*	if( self::$_request->getProperty( 'search' ) ) {

			$select->join( 'metatext', 'objects.object_id = metatext.object_id');
			$select->join( 'objects_tags', 'objects.object_id = objects_tags.object_id');
			$select->join( 'tags', 'objects_tags.tag_id = tags.tag_id');

			$select->where( '( objects.object_title LIKE "%'.self::$_request->getProperty( 'search' ).'%" 
				OR objects.object_description LIKE "%'.self::$_request->getProperty( 'search' ).'%"
				OR metatext.metatext_text LIKE "%'.self::$_request->getProperty( 'search' ).'%"
				OR tags.tag_name LIKE "%'.self::$_request->getProperty( 'search' ).'%" ) AND objects.object_id != ?', '' );			
	*/
		$this->searchObjects($select);
		
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
	 * Filters a data query based on search criteria
	 *
	 * @param Kea_DB_Select The Select object
	 * @return Kea_DB_Select $select The Select object 
	 * @author Kris Kelly
	 **/
	private function searchObjects( Kea_DB_Select $select)
	{
		if( $phrase = self::$_request->getProperty( 'search' ) ) 
		{
			$select->join( 'metatext', 'objects.object_id = metatext.object_id');
			//$select->joinRight( 'files', 'objects.object_id = files.object_id');
			
			//Search fails when objects are not tagged, figure out a way to fix this [KBK 8/29]
			//Also, search should also cover 'files' file_description, but I can't get that to work (it breaks the Browse page)
			
			//$select->join( 'objects_tags', 'objects.object_id = objects_tags.object_id');
			//$select->join( 'tags', 'objects_tags.tag_id = tags.tag_id');

			$phrase = trim($phrase);
			$phrase = ' "%'.$phrase.'%" ';
			$select->where( '( objects.object_title LIKE '.$phrase. 
				'OR objects.object_description LIKE '.$phrase.
				'OR metatext.metatext_text LIKE '.$phrase.
				//'OR files.file_description LIKE '.$phrase.
				//'OR tags.tag_name LIKE '.$phrase.
				' ) AND objects.object_id != "" ');			
			
		}
		return $select;
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
			$select->where( 'objects.object_published = ?', 1 );
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
	public function add( $type = 'admin' )
	{
		
		if( self::$_request->getProperty( 'object_add' ) ) {
			
			
			if( $object = $this->commitForm() ) {
				switch ($type) 
				{
					case 'public': 
						self::$_session->setValue( 'object_form_saved', null );
						self::$_session->setValue( 'contributed_object', $object );
						$this->redirect( BASE_URI . DS . 'mycontributions' );
					break;
					case 'admin': 
						$this->redirect( BASE_URI . DS . 'objects' . DS . 'all' );
					break;
					
				}
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
	protected function _edit( $type = 'admin')
	{
		if( self::$_request->getProperty( 'object_edit' ) ) {
			if( $this->commitForm() ) {
				$object = $this->_findById();
				switch ($type) 
				{
					case 'public': 
						$this->redirect( BASE_URI . DS . 'mycontributions' );
					break;
					case 'admin': 
						$this->redirect( BASE_URI . DS . 'objects' . DS . 'show' . DS . $object->object_id );
					break;
				}			
				return;
			}
		} else {
			$object_c = $this->_findById();
			$object = $object_c->current();
			$object->getTypeMetadata();
			
			$sudo = array();
			$object_a = array(	'object_id'						=> $object->getId(),
								'object_title'					=> $object->object_title,
								'object_description'			=> $object->object_description,
								'type_id'					=> $object->type_id,
								'collection_id'					=> $object->collection_id,
								'object_language'				=> $object->object_language,
								//'contributor_id'				=> $object->contributor_id,
								'object_publisher'				=> $object->object_publisher,
								'object_rights'					=> $object->object_rights,
								'object_date'					=> $object->object_date,
								'object_coverage'				=> $object->object_coverage,
								'object_creator'				=> $object->object_creator,
								'object_additional_creator'		=> $object->object_additional_creator,
								'object_relation'				=> $object->object_relation,
								'object_subject'				=> $object->object_subject,
								'object_source'					=> $object->object_source,
								'object_public'					=> $object->object_public,
								'type_metadata'				=> @$object->type_metadata );

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
/*			
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
*/
			
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
		
		// Else, try to match contributor by logged-in ID
		// If the object's contributor ID is not set, then try to grab contributor info from the form.
		// make a new contributor if its unique, otherwise find the pre-existing one, then take the ID
		// from the one it found and attach that to the object.  If that doesn't work, then we try to get
		// or make new contributor info from the user who is logged in [JMG addition]  
		//If the object's contributor ID is still null, then just go ahead and set it to 'NULL' string
/*
		if( empty($object->contributor_id) )
		{
			if( self::$_request->getProperty('object_add') )
			{
				if( self::$_request->getProperty('Contributor' ) )
				{
					$contributor = new Contributor( self::$_request->getProperty( 'Contributor' ) );
	
					$email = $contributor->contributor_email;
					if( $this->validates($contributor) )
					{
						if( $contributor->uniqueNameEmail() )
						{
							$contributor->save();
						}
						elseif(!empty($email))
						{
							$contributor = $contributor->findUnique();
						}
					
					}
					if( !empty($contributor->contributor_id) )
					{
						$object->contributor_id = $contributor->contributor_id;
					}					
				}
				elseif ( $user = self::$_session->getUser() )
          		{   
	              // If the user has a contributor ID attached, then hand it to the object
	              	if ($contributor = $user->getContributor())
	              	{
	                   $object->contributor_id = $user->contributor_id;
	              	}
	              // If the user doesn't have a contributor ID attached, make a new one
	            	else
	             	{
	                   $contributor = new Contributor();
	                   $contributor->contributor_first_name = $user->user_first_name;
	                   $contributor->contributor_last_name = $user->user_last_name;
	                   $contributor->contributor_email = $user->user_email;
					   $contributor->contributor_contact_consent = 'no';
						if( $contributor->validates() )
						{
		                   $contributor->save();

		                   // Attach the new contributor to the logged-in user
		                   $user->contributor_id = $contributor->contributor_id;
		                   $user->save();                   

		                   // Attach the new contributor to the new object
		                   $object->contributor_id = $user->contributor_id;							
						}

	               }
          		}
			}
	        
			if( empty($object->contributor_id) )
			{
				$object->contributor_id = 'NULL';
			}
		}
*/		
/*
		if( self::$_request->getProperty( 'creator' ) == 'yes' && !empty( $object->contributor_id ) )
		{
			$object->creator_id = $object->contributor_id;
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
*/		
		if( $object->object_language == 'other' && self::$_request->getProperty('object_language_other') )
		{
			$object->object_language = self::$_request->getProperty('object_language_other');
		}
		
		if( empty( $object->collection_id ) )
		{
			$object->collection_id = 'NULL';
		}
		
		if ( empty( $object->object_added) )
		{
			$object->object_added = null;
		}
		
		if ( empty( $object->object_modified) )
		{
			$object->object_modified = 'NULL';
		}

		if ( empty( $object->object_public) )
		{
			$object->object_public = '0';
		}

		// This is a kludge to make sure that editing an object doesn't destroy its relationship with the user who uploaded it [JMG]
		if ( !self::$_request->getProperty('object_edit') ) 
		{
			$object->user_id = self::$_session->getUser()->getId();
		}
		
		if ( empty( $object->type_id ) )
		{
			$object->type_id = 'NULL';
		}
		
		/* Deprecated in favor of the following if statement as of r351 
		if (self::$_session->getUser()) $object->user_id = self::$_session->getUser()->getId();
		*/
				
		if( $this->validates( $object ) )
		{
			$object->save();
			
			$location = new Location( self::$_request->getProperty( 'Location' ) );
			if( $location->hasValues() )
			{
				$object->getLocation();
				$location_id = $object->location->location_id;
				$location->object_id = $object->object_id;
				$location->latitude = round( $location->latitude, 5 );
				$location->longitude = round( $location->longitude, 5 );
				if( $location_id ) { $location->location_id = $location_id; }
				if( $this->validates( $location ) )
				{
					$location->save();	
				}
			}
		
			$tags = new Tags( self::$_request->getProperty( 'tags' ) );
			$tags->object_id = $object->object_id;
			if (self::$_session->getUser()) $tags->user_id = self::$_session->getUser()->getId();
			
			//if( $this->validates( $tags ) )
			//{
				$tags->save();
			//}
		
			// Add the metadata
			if( $metadata = self::$_request->getProperty( 'metadata' ) )
			{
				foreach( $metadata as $k => $v )
				{
					if ( is_int($k) )
					{
						$text = @$v['metatext_text'];
						if(!empty($text))
						{
							$m = new Metatext( $v );
							$m->object_id = $object->object_id;
							if( $this->validates( $m ) )
							{
								$m->save();
							}		
						}
							
					}
					else 
					{
						if ( !empty($v) )
						{
							$mF = new Metafield();
							$metafield_id = $mF->findIDBy('metafield_name', $k);

							 $m = new Metatext(array(
                             					'metafield_id' => $metafield_id,
                                                'object_id' => $object->object_id,
                                                'metatext_text' => $v
                                               ) );

							if( $this->validates( $m ) )
							{
								$m->save();
							}
						}
						
					}
				
				}
			}
		
		
			$files = File::add( $object->getId(), $object->contributor_id, 'objectfile', self::$_request->getProperty('File') );
		
			$object->save();
		}

		if( count( $this->validationErrors ) > 0 ) {
			self::$_session->setValue( 'object_form_saved', $_REQUEST );
			$adapter->rollback();
			return false;
		} else {
			self::$_session->setValue( 'object_form_saved', null );
			$adapter->commit();
			return $object;
		}
	}
	
	protected function _total()
	{
		$mapper = new Object_Mapper();
		return $mapper->total();
	}
	
	protected function _totalInType( $type_id )
	{
		$mapper = new Object_Mapper();
		return $mapper->totalSliced( $type_id , null);
	}
	
	protected function _totalInCollection( $collection_id )
	{
		$mapper = new Object_Mapper();
		return $mapper->totalSliced( null, $collection_id );

	}

	
	protected function _deleteTypeAssociation( $object_id )
	{
		$adapter = Kea_DB_Adapter::instance();
		$adapter->update( 'objects', array( 'type_id'	=> 'NULL' ), 'objects.object_id = \'' . $object_id . '\'' );
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
	
	protected function _delete( $type = 'admin' )
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
			switch ($type) 
			{
				case 'public': 
					$this->redirect( BASE_URI . DS . 'mycontributions' );
				break;
				case 'admin': 
					$this->redirect( BASE_URI . DS . 'objects' . DS . 'all' );
				break;
				
			}
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
	
	public function windowsToAscii($text, $replace_single_quotes = true, $replace_double_quotes = true, $replace_emdash = true, $use_entities = false)
	{
		if ( is_array($text) )
		{
			foreach( $text as $key => $value )
			{
				$text[$key] = $this->windowsToAscii($value, $replace_single_quotes, $replace_double_quotes, $replace_emdash, $use_entities);
				return $text;
			}
		}
		else
		{
			$cout = '';
	
	
		    $translation_table_ascii = array(
		        145 => '\'', 
		        146 => '\'', 
		        147 => '"', 
		        148 => '"', 
		        151 => '-'
		    );

		    $translation_table_entities = array(
		        145 => '&lsquo;', 
		        146 => '&rsquo;', 
		        147 => '&ldquo;', 
		        148 => '&rdquo;', 
		        151 => '&mdash;'
		      );

		    $translation_table = ($use_entities ? $translation_table_entities : $translation_table_ascii);

		    if ($replace_single_quotes) {
		        $text = preg_replace('#\x' . dechex(145) . '#', $translation_table[145], $text);
		        $text = preg_replace('#\x' . dechex(146) . '#', $translation_table[146], $text);
		    }

		    if ($replace_double_quotes) {
		        $text = preg_replace('#\x' . dechex(147) . '#', $translation_table[147], $text);
		        $text = preg_replace('#\x' . dechex(148) . '#', $translation_table[148], $text);
		    }

		    if ($replace_emdash) {
		        $text = preg_replace('#\x' . dechex(151) . '#', $translation_table[151], $text);
		    }
    
			for($i=0;$i<strlen($text);$i++) {
			   $ord=ord($text[$i]);
			   if($ord>=192&&$ord<=239) $cout.=chr($ord-64);
			   elseif($ord>=240&&$ord<=255) $cout.=chr($ord-16);
			   elseif($ord==168) $cout.=chr(240);
			   elseif($ord==184) $cout.=chr(241);
			   elseif($ord==185) $cout.=chr(252);
			   elseif($ord==150||$ord==151) $cout.=chr(45);
			   elseif($ord==147||$ord==148||$ord==171||$ord==187) $cout.=chr(34);
			   elseif($ord>=128&&$ord<=190) $i=$i; //нет представления данному символу
			   else $cout.=chr($ord);
			}
	
			$cout = str_replace("‘", "'", $cout);
			$cout = str_replace("’", "'", $cout);
			$cout = str_replace("”", '"', $cout);
			$cout = str_replace("“", '"', $cout);
			$cout = str_replace("–", "-", $cout);
			$cout = str_replace("…", "...", $cout);
	
			return $cout;
		}
		
	}
// End class
}

?>