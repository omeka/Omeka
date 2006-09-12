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
	 * @return array Contains the following keys: 'total' => total # of objects found, 'page' => current page, 'per_page' => # per page, 'objects' => Object_Collection containing the objects found
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

	// this is called by the consent form
	protected function _submitContribution()
	{
		if( $object_consent = self::$_request->getProperty( 'object_contributor_consent' ) )
		{
			$object = self::$_session->getValue( 'contributed_object' );
			$files = self::$_session->getValue( 'contributed_files' );
			
			if( $object_consent == 'no' )
			{
				self::$_session->unsetValue( 'contributed_object' );
				if( $files )
				{
					foreach( $files as $file )
					{
						File::delete( $file->getId() );
					}
				}
				
				$object->delete();
				$this->redirect( BASE_URI . DS . 'contribute' );
				return;
			}
			
	/*		if( self::$_session->getValue( 'contributed_user' ) )
			{
				self::$_session->loginUser( self::$_session->getValue( 'contributed_user' ) );
				self::$_session->unsetValue( 'contributed_user' );
			} */
			
			// Stash contributor_id
			//self::$_session->setValue( 'contributor_id', $object->contributor_id );
			
			$object->object_contributor_consent = $object_consent;
			$object->save();

			
			// Send e-mail
			$contributor_mapper =  new Contributor_Mapper();
			$email = $contributor_mapper->find()->where('contributor_id = ?', $object->contributor_id)->execute()->contributor_email;
			$message = "Thank you for your contribution to ".SITE_TITLE.".  Your contribution has been accepted and will be preserved in the digital archive. For your records, the permanent URL for your contribution is noted at the end of this email. Please note that contributions may not appear immediately on the website while they await processing by project staff.
			
Contribution URL (pending review by project staff): http://".$_SERVER['SERVER_NAME'] . substr($_SERVER['PHP_SELF'] , 0, strrpos($_SERVER['PHP_SELF'], '/')) . DS .'object' . DS .self::$_session->getValue( 'contributed_object' )->object_id;
			$title = "Your ".SITE_TITLE." Contribution";
			$header = 'From: '.EMAIL . "\n" . 'X-Mailer: PHP/' . phpversion();
			
			mail( $email, $title, $message, $header);
			
			//self::$_session->unsetValue( 'contributed_object' );
			if( self::$_session->getValue( 'contributed_files' ) )
			{
				self::$_session->unsetValue( 'contributed_files' );
			}
			$this->redirect( BASE_URI . DS . 'thankyou' );
			return;

		}
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
			$object->getCategoryMetadata();
			
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
								'object_relation'				=> $object->object_relation,
								'category_metadata'				=> $object->category_metadata );

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
		

		if( empty( $object->contributor_id ) )
		{
			$object->contributor_id = 'NULL';
		}		
		// Create contributor
		$contributor = new Contributor( self::$_request->getProperty( 'Contributor' ) );
		
		if(empty($contributor->contributor_id) && self::$_request->getProperty('object_add'))
		{
			
			// Try to match contributor by e-mail
			if ($contributor->contributor_email)
			{
				$email = $contributor->contributor_email;
				if( $this->validates($contributor) )
				{
					// If the contributor's e-mail is unique, grab the contributor ID of the existing contributor
					if( $contributor->uniqueNameEmail() )
					{
						$contributor->save();
						$object->contributor_id = $contributor->contributor_id;
					}
					elseif(!empty($email))
					{
						$contributor->contributor_id = $contributor->findIDBy('contributor_email', $contributor->contributor_email);
						$object->contributor_id = $contributor->contributor_id;
					}	
					else
					{
							$contributor_a = $contributor->mapper()->find( "contributor_id" )
									->where( "contributor_first_name = ?", $contributor->contributor_first_name )
									->where( "contributor_last_name = ?", $contributor->contributor_last_name)
									->execute();
							$object->contributor_id = $contributor_a->contributor_id;
					}
				

				}
			}
			
			// Else, try to match contributor by logged-in ID
			elseif ( $user = self::$_session->getUser() )
			{	
				
				
				
				// If the user has a contributor ID attached, then hand it to the object
				if ($user->getContributor())
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
					$contributor->save();
					$adapter->commit();

					// Attach the new contributor to the logged-in user
					$user->contributor_id = $contributor->contributor_id; 
					$user->save();					

					// Attach the new contributor to the new object
					$object->contributor_id = $user->contributor_id;
				}
			}
		}
		

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
		
		if( $object->object_language == 'other' && self::$_request->getProperty('object_language_other') )
		{
			$object->object_language = self::$_request->getProperty('object_language_other');
		}
		
		if( empty( $object->collection_id ) )
		{
			$object->collection_id = 'NULL';
		}
		
		

		
		// This is a kludge to make sure that editing an object doesn't destroy its relationship with the user who uploaded it [JMG]
		if ( !self::$_request->getProperty('object_edit') ) 
		{
			$object->user_id = self::$_session->getUser()->getId();
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
	
	
	protected function _ingest()
	{
		if (self::$_request->getProperty('batch_add_do')) {
				$contributor = new Contributor(self::$_request->getProperty('contributor'));
				if( $this->validates($contributor) ) 
				{
					if( $contributor->uniqueNameEmail() )
					{
						$contributor->save();	
					}
					else
					{
						$contributor = $contributor->findSelf();
					}
					self::$_request->setProperty('contributor_id', $contributor->contributor_id);
					//echo self::$_request->getProperty('contributor_id');
					if(self::$_request->getProperty('collection_id'))
					{
						$this->ingestFileTree($this->getFileTree(), self::$_request->getProperty('collection_id'));
					}
					else
					{
						$this->ingestFileTree($this->getFileTree());
					}
				}
				else 
				{
					return;
				}
		}
		
		
		return;
	}
	
	private function ingestFileTree( $tree , $parent_collection_id=null, $output=null ) 
	{
	try{
		$output = "";
		//print_r(self::$_request->getProperties());
		if(self::$_request->getProperty('contributor_id'))
		{
			$contributor_id = self::$_request->getProperty('contributor_id');
			foreach ( $tree as $key => $value  )
			{
				if ( is_array( $value ) && is_readable($key) && is_writeable($key) ) 
				{
					// It's a folder:
					// 1. Make a collection
					// parent_collection_id ===> collection_parent
					// $key ===> collection_name
					// 2. Call ingestFileTree( $value , last_mysql_collection_id_created)
					$collParams = array( 
						'collection_name'			=>	basename($key),
						'collection_collector'		=>	NULL,	//How to get contributor name from the API?
						'collection_description'	=>	NULL,	//Get this value somehow
						'collection_active'			=>  0, 		//Safe to assume this collection is active?
						'collection_featured'		=>	0,		//Not featured by default
						'collection_parent'			=>	$parent_collection_id);
				
					$currentCollection = new Collection($collParams);
				
					if( !($this->validates($currentCollection)) ) 
					{
						return;
					}
					else 
					{
						$currentCollection->save();
						$output .= "<ul>Collection named: " . $currentCollection->collection_name . " created successfully!</ul>\n";
						$output .= $this->ingestFileTree($value, $currentCollection->collection_id, $output);
					
						//Delete the directory if it is empty, first make sure to delete Thumbs.db
						$dir = $key;
						@unlink($dir.DIRECTORY_SEPARATOR.'Thumbs.db');

						if( $this->is_empty_dir($dir) ) 
						{
							if( !rmdir($dir) ) {
								throw new Kea_Action_Exception("Empty Directory: $dir could not be deleted");
							}

						}
					}
				}	
				elseif( is_readable($value) && is_writeable($value) )
				{
				
					$objParams = array(
						'object_title'				=>	$key,
						'contributor_id'			=>	$contributor_id,
						'creator_id'				=>	$contributor_id,
						'collection_id'				=>	($parent_collection_id) ? $parent_collection_id : 'NULL'); 
					$currentObject = new Object(array_merge($objParams, self::$_request->getProperty('Object')));
					
					if($this->validates($currentObject))
					{
						$currentObject->save();
					}
					else
					{
						return;
					}
					
					$fileParams = array(
						'file_title'			=>	$key,
						'object_id'				=>	$currentObject->object_id,
						'contributor_id'		=>	$contributor_id,
						'file_original_filename'=>	basename($value),
						'file_archive_filename'	=>	File::createArchiveFilename(basename($value)),
						'file_added'			=>	date("Y-m-d H:i:s") );
					
					$currentFile = new File($fileParams);
					
					//print_r($currentFile);
					$old_path = $value;
					$new_path = ABS_VAULT_DIR . DIRECTORY_SEPARATOR . $currentFile->file_archive_filename;
				
					//Extraction of header data
					switch ( self::$_request->getProperty('use_image_headers') )
					{
						case 'exif':
							if(function_exists('exif_imagetype') && function_exists('exif_read_data'))
							{
								$fileimagetype = exif_imagetype($old_path);
						
								if( ($fileimagetype == IMAGETYPE_JPEG) || ($fileimagetype == IMAGETYPE_TIFF_II) || ($fileimagetype == IMAGETYPE_TIFF_MM) ) 
								{
									$exif = @exif_read_data($old_path, 0, true);
									$exif = $this->windowsToAscii($exif);
									if($exif) {
										$exifImageDesc = (isset($exif['IFD0']['ImageDescription'])) ? $exif['IFD0']['ImageDescription'] : NULL;
										$exifComments = (isset($exif['IFD0']['Comments'])) ? $exif['IFD0']['Comments'] : NULL;
										$exifTitle = (isset($exif['IFD0']['Title'])) ? $exif['IFD0']['Title'] : NULL;
									}
									//Save the image metadata
									if ( strlen($exifImageDesc) > strlen($exifComments) )
									{
										$imageMeta = new Metatext(
											array(
												'metafield_id' => 3, 
												'object_id' => $currentObject->object_id, 
												'metatext_text' => $exifImageDesc));
										$imageMeta->save();
										$output .= "Metadata saved with metatext_id # {$imageMeta->metatext_id}<br/>\n"; 
									}
									elseif ( !empty($exifComments) )
									{
										$imageMeta = new Metatext(array('metafield_id' => 3, 'object_id' => $currentObject->object_id, 'metatext_text' => $exifComments));
										$imageMeta->save();
										$output .= "Metadata saved with metatext_id # {$imageMeta->metatext_id}<br/>\n"; 
									}

								}
							}
							else
							{
								throw new Kea_Action_Exception("Unable to process EXIF data in image headers (application needs PHP Exif library installed)");
							}
						break;
						
						case 'iptc':
							$iptcdata = File::getIPTCvalues($old_path);
							$iptcdata = $this->windowsToAscii($iptcdata);
							if ( !empty($iptcdata['name']) )
							{
								$currentFile->file_title = $iptcdata['name'];
							}
							if( !empty($iptcdata['description']) ) 
							{
								$imageMeta = new Metatext(array('metafield_id' => 3, 'object_id' => $currentObject->object_id, 'metatext_text' => $iptcdata['description']));
								$imageMeta->save();
								$output .= "Metadata saved with metatext_id # {$imageMeta->metatext_id}<br/>\n"; 
							}
							if(!empty($iptcdata['location'])) 
							{
								//$currentLocation = new Location(array('address' => $iptcdata['location'], 'object_id' => $currentObject->object_id));
								if ($this->validates($currentLocation)) 
								{
									$currentLocation->save();
								}
							}
							if(!empty($iptcdata['creator']))
							{
								$currentFile->file_producer = $iptcdata['creator'];
							}
							if(!empty($iptcdata['author']))
							{
								$currentObject->creator_other = $iptcdata['author'];
							}
							if(!empty($iptcdata['source']))
							{
								$currentFile->file_publisher = $iptcdata['source'];
							}
							if(!empty($iptcdata['creation_date'])) {
								$fileyear = substr(@$iptcdata['creation_date'], 0, 4);
								$filemonth =  substr(@$iptcdata['creation_date'], 3, 2);
								$fileday = substr(@$iptcdata['creation_date'], 5, 2);
								$currentFile->file_date = "$fileyear:$filemonth:$fileday 00:00:00";
							}

						break;
						
						case 'none':		
						default:
						break;
					}
					
					throw new Kea_Action_Exception( "Category is not sent for new objects.  Find a way of doing this");
				
				
					//Move the file from the dropbox to the vault directory
					
					if(!rename($old_path, $new_path))
					{
						throw new Kea_Action_Exception("File: {$currentFile->file_original_filename} cannot be moved, possible permissions error");
					}
					else {
							$currentFile->file_mime_php = mime_content_type( $new_path );
							$currentFile->file_mime_os = trim( exec( 'file -ib ' . trim( escapeshellarg ( $new_path ) ) ) );
							$currentFile->file_type_os = trim( exec( 'file -b ' . trim( escapeshellarg ( $new_path ) ) ) );
							$currentFile->file_size = filesize($new_path);
							$currentFile->file_thumbnail_name = File::createThumbnail( $new_path, null, THUMBNAIL_SIZE );
					}
				
					 
					if( $this->validates($currentFile) ) 
					{
						$currentFile->save();
					}
					else
					{
						throw new Kea_Domain_Exception("File named {$currentFile->file_original_filename} could not be saved in DB!");
					}
				
					//$currentObject->category_id = categoryFromFileMimeType($currentFile->file_mime_os);
				
					if( $this->validates($currentObject) ) 
					{
						$currentObject->save();
					}
				
					$output .= "<li>Object # {$currentObject->object_id}: {$currentObject->object_title} created successfully!</li>";
					// It's a file:
					// 1. create an object
					// 2. Create new file attached to object
					// parent_collection_id ===> object_collection_id
					// $key ===> object_name
					// $value ===> file path
				}
			}

		}	
		self::$_request->setProperty('batch_output', $output);
		return $output;
	} catch(Exception $e) {
		//die($e->__toString());
		echo $output . $e->__toString();
	}
	}
	
	protected function _displayDropbox()
	{
		$tree = $this->getFileTree();
		
		$displayExif = ( self::$_request->getProperty('preview_exif') ) ? TRUE : FALSE;
		$displayIPTC = ( self::$_request->getProperty('preview_iptc') ) ? TRUE : FALSE;
		
		$output = $this->displayFileTree($tree, 0, $displayExif, $displayIPTC);
		
		echo $output;
	}
	
	private function displayFileTree($tree, $filenum = 0, $displayEXIF = FALSE, $displayIPTC = FALSE)
	{
		$output = "\n\t";
		foreach ( $tree as $key => $value  )
		{
			if ( is_array( $value ) )
			{
				$filenum = 0;
				$output .= "<ul class=\"directory\">".basename($key).$this->displayFileTree( $value, $filenum, $displayEXIF, $displayIPTC )."</ul>";
				if( !is_readable($key) || !is_writeable($key) )
				{
					$output .= "<div class=\"error\">Warning: Batch ingest does not have access to directory $key Improper file/directory permissions.";
					$output .= "Have an administrator fix this before uploading.</div>";
					 	
				}
			}
			else
			{
				$filenum++;
				if( !is_readable($value) || !is_writeable($value) )
				{
						$output .= "<div class=\"error\">Warning: Batch ingest does not have access to file ".basename($value).": ";
						$output .= "Improper file/directory permissions.  Have an administrator fix this before uploading.</div>";
				}
				$output .= "<li class=\"file\">";
				
				if ( $displayEXIF || $displayIPTC )
				{
					//Let's make a table that displays all the header info
					$output .= "\n<table class=\"file\">";
					$output .= "<tr>\n\t<td>$filenum ) $key</td>";
					$output .= "</tr><tr>\n\t<td>";
					if ( $displayEXIF )
					{
						//Preview exif data
						$exif = exif_read_data($value, 0, true);
						if($exif) {
							$output .= ( ( isset($exif['IFD0']['Title']) ) ? "EXIF Title: ".$this->windowsToAscii($exif['IFD0']['Title'])."<br/>" : "&nbsp;" );
							$output .= ( ( isset($exif['IFD0']['ImageDescription']) ) ? "EXIF ImageDescription: " . $this->windowsToAscii($exif['IFD0']['ImageDescription']) : "&nbsp;" );
							$output .= ( ( isset($exif['IFD0']['Comments'] ) ) ? "EXIF Comments: " . $this->windowsToAscii($exif['IFD0']['Comments']) : "&nbsp;");
						}
						else{
							$output .= "No EXIF header data";
						}
					
						$output .= "</td>\n\t<td>";
					}
					
					if ( $displayIPTC )
					{
						$iptc = File::getIPTCValues($value);
						$iptc = $this->windowsToAscii($iptc);

						if( !empty($iptc['name']) || !empty($iptc['description']) )
						{
							$output .= "IPTC Title: ".$this->windowsToAscii($iptc['name'])."<br/>";
							$output .= "IPTC Description: ".$this->windowsToAscii($iptc['description']);
						}
					}

					$output .= "</td>\n</tr>\n</table>";
				}
				else
				{
					$output .= "$filenum ) $key";
				}
				$output .= "</li>\n";
			}
		}

		return $output;
	}
	
	private function getFileTree($rootPath = ABS_DROPBOX_DIR)
	{
	   $pathStack = array($rootPath);
	   $contentsRoot = array();
	   $contents = &$contentsRoot;
	   while ($path = array_pop($pathStack)) {
	       $contents[basename($path)] = array();
	       $contents = &$contents[basename($path)];
	       foreach (scandir($path) as $filename) {
	           if ('.' != substr($filename, 0, 1)) {
	               $newPath = $path.'/'.$filename;
	               if (is_dir($newPath)) {
	                   //$contents[basename($newPath)] = getFileTree($newPath);
						$contents[$newPath] = $this->getFileTree($newPath);
	               } else {
						// screen out files we know we don't want
	                   if (!strpos($newPath, 'umbs.db') )
						$contents[basename($filename)] = $newPath;
	               }
	           }
	       }
	   }
	   return $contentsRoot[basename($rootPath)];
	}
	
	private function is_empty_dir($dirname){

		// Returns true if  $dirname is a directory and it is empty

		   $result=false;                      // Assume it is not a directory
		   if(is_dir($dirname) ){
		       $result=true;                  // It is a directory
		       $handle = opendir($dirname);
		       while( ( $name = readdir($handle)) !== false){
		               if ($name!= "." && $name !=".."){
		             $result=false;        // directory not empty
		             break;                  // no need to test more
		           }
		       }
		       closedir($handle);
		   }
		   return $result;
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