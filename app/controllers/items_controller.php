<?php

class ItemsController extends Kea_Action_Controller
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
	 * Returns the next item in the database 
	 * 
	 * @param int $id An item_id
	 * @return Item Returns the entire item, otherwise returns false
	 * @author Nate Agrin
	 **/
	protected function _getNextItemID( $id = null )
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

		$mapper = new Item_Mapper;
		$select = $mapper->find()
						->where( 'item_id > ?', $id )
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
	 * Returns the item located immediately prior in the database
	 *
	 * @param int $id An item_id 
	 * @return Item Returns the entire item, otherwise returns false
	 * @author Nate Agrin
	 **/
	protected function _getPrevItemID( $id = null )
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

		$mapper = new Item_Mapper;
		$select = $mapper->find()
						->where( 'item_id < ?', $id )
						->order( array( 'item_id' => 'DESC' ) )
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
	 * Find an item by ID
	 *
	 * @param int $id item_id
	 * @return Item Returns the entire item, otherwise returns false
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
		
		$mapper = new Item_Mapper();
		$select = $mapper->select()
					  ->joinLeft( 'types', 'types.type_id = items.type_id' )
					  ->where( 'items.item_id = ?', $id );
		$this->applyPermissions( $select );
		$obj = $mapper->findObjects( $select );
		
		if ($obj->item_id):
			$obj->getTypeMetadata()
				->getLocation()
				->getTags()
				->getFiles();					
			return $obj;
		else:
		throw new Kea_Domain_Exception('Cannot retrieve item with ID # '.$id);
			return false;
		endif;
	}

	/**
	 * Gets the current page number when browsing items
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
	 * Chooses a list of items to display based on $_request data
	 *
	 * Handles both search and display of items within the browse page.
	 *
	 * @param bool $short_desc Show a short description of each item 
	 * @param int $num_items The number of items per page
	 * @param bool $check_location Include items with valid location coordinates
	 * @return array Contains the following keys: 'total' => total # of items found, 'page' => current page, 'per_page' => # per page, 'items' => Item_Collection containing the items found
	 * @author Nate Agrin
	 **/
	protected function _paginate( $short_desc = true, $num_items = 9, $check_location = false )
	{	
		$page = isset( self::$_route['pass'][0] ) ? (int) self::$_route['pass'][0] : 1;

		$mapper = new Item_Mapper();
		
		if( $short_desc ) {
			$select = $mapper->select( "*, RPAD( SUBSTRING( item_description, 1, 140 ),  LENGTH( SUBSTRING( item_description, 1, 140 ) ) + 3, '.') as short_desc" )
							 ->joinLeft( 'types', 'types.type_id = items.type_id' )
							 ->order( array( 'items.item_id' => 'DESC' ) );
		}

		/* Begin consent screening
		 	Assumes privilege levels of: 
			20 - Researcher
			10 - Admin
			1 - Superuser
		*/
		
		if( self::$_request->getProperty( 'collection' ) ) {
			$select->where( 'items.collection_id = ?', self::$_request->getProperty( 'collection' ) );
		}
		
		if( ( $cat = self::$_request->getProperty( 'itemType' ) )  
			|| ( $cat = self::$_request->getProperty( 'type' ) )
		 	 )
		{
			$select->where( 'items.type_id = ?', $cat );
		}
		
		if( self::$_request->getProperty( 'featured' ) ) {
			$select->where( 'items.item_featured = ?', self::$_request->getProperty( 'featured' ) );
		}
		
		if( $tags = self::$_request->getProperty( 'tags' ) )
		{
			$all_tags = explode( ',', $tags );
			foreach($all_tags as $tag)
			{
				$tag = trim( $tag );
				$select->orWhere( 'tags.tag_name = ?', $tag );
			}
			
			$select->joinLeft( 'items_tags', 'items_tags.item_id = items.item_id' )
				   ->joinLeft( 'tags', 'items_tags.tag_id = tags.tag_id' )
					->group( 'items.item_id' );
		}
		
	/*	if( self::$_request->getProperty( 'search' ) ) {

			$select->join( 'metatext', 'items.item_id = metatext.item_id');
			$select->join( 'items_tags', 'items.item_id = items_tags.item_id');
			$select->join( 'tags', 'items_tags.tag_id = tags.tag_id');

			$select->where( '( items.item_title LIKE "%'.self::$_request->getProperty( 'search' ).'%" 
				OR items.item_description LIKE "%'.self::$_request->getProperty( 'search' ).'%"
				OR metatext.metatext_text LIKE "%'.self::$_request->getProperty( 'search' ).'%"
				OR tags.tag_name LIKE "%'.self::$_request->getProperty( 'search' ).'%" ) AND items.item_id != ?', '' );			
	*/
		$this->searchItems($select);
		
		if( $check_location )
		{
			$select->join( 'location', 'items.item_id = location.item_id' );
			$select->where( 'location.latitude != ?', '' );
			$select->where( 'location.longitude != ?', '' );
		}
		
		$select->group( 'items.item_id' );
		$this->applyPermissions( $select );

		return $mapper->paginate( $select, $page, $num_items, 'itemsTotal' );
	}
	
	/**
	 * Filters a data query based on search criteria
	 *
	 * @param Kea_DB_Select The Select item
	 * @return Kea_DB_Select $select The Select item 
	 * @author Kris Kelly
	 **/
	private function searchItems( Kea_DB_Select $select)
	{
		if( $phrase = self::$_request->getProperty( 'search' ) ) 
		{
			$select->join( 'metatext', 'items.item_id = metatext.item_id');
			//$select->joinRight( 'files', 'items.item_id = files.item_id');
			
			//Search fails when items are not tagged, figure out a way to fix this [KBK 8/29]
			//Also, search should also cover 'files' file_description, but I can't get that to work (it breaks the Browse page)
			
			//$select->join( 'items_tags', 'items.item_id = items_tags.item_id');
			//$select->join( 'tags', 'items_tags.tag_id = tags.tag_id');

			$phrase = trim($phrase);
			$phrase = ' "%'.$phrase.'%" ';
			$select->where( '( items.item_title LIKE '.$phrase. 
				'OR items.item_description LIKE '.$phrase.
				'OR metatext.metatext_text LIKE '.$phrase.
				//'OR files.file_description LIKE '.$phrase.
				//'OR tags.tag_name LIKE '.$phrase.
				' ) AND items.item_id != "" ');			
			
		}
		return $select;
	}
	
	/**
	 * Filters items based on user access permission
	 *
	 * @param Kea_DB_Select Select Item containing the parameters for selecting item(s) from the database 
	 * @return Kea_DB_Select The select item with permission-based filtering
	 * @author Nate Agrin
	 **/
	private function applyPermissions( Kea_DB_Select $select )
	{
		if( !self::$_session->isAdmin() )
		{
			$select->where( 'items.item_public = ?', 1 );
		}
				
		return $select;	
	}
	
	/**
	 * Add an item to the archive
	 *
	 * If the item_add form has been submitted, commit the item to the database and redirect the user to view all items,
	 * otherwise reset the saved form to empty
	 * @return void
	 * @author Nate Agrin
	 **/
	public function add( $type = 'admin' )
	{
		
		if( self::$_request->getProperty( 'item_add' ) ) {
			
			
			if( $item = $this->commitForm() ) {
				switch ($type) 
				{
					case 'public': 
						self::$_session->setValue( 'item_form_saved', null );
						self::$_session->setValue( 'contributed_item', $item );
						$this->redirect( BASE_URI . DS . 'mycontributions' );
					break;
					case 'admin': 
						$this->redirect( BASE_URI . DS . 'items' . DS . 'all' );
					break;
					
				}
				return;
			}
			return;
		}
		self::$_session->setValue( 'item_form_saved', null );
	}
	
	/**
	 * Edit an archived item
	 *
	 * If the item_edit form has been submitted, commit the form to the database and redirect the user to that 
	 * item's entry.  If the form was not submitted, then find the current item and prepare that item's data
	 * to be displayed on the 'edit' page.
	 *
	 * @return Item Returns the item that is going to be edited
	 * @author Nate Agrin
	 **/
	protected function _edit( $type = 'admin')
	{
		if( self::$_request->getProperty( 'item_edit' ) ) {
			if( $this->commitForm() ) {
				$item = $this->_findById();
				switch ($type) 
				{
					case 'public': 
						$this->redirect( BASE_URI . DS . 'mycontributions' );
					break;
					case 'admin': 
						$this->redirect( BASE_URI . DS . 'items' . DS . 'show' . DS . $item->item_id );
					break;
				}			
				return;
			}
		} else {
			$item_c = $this->_findById();
			$item = $item_c->current();
			$item->getTypeMetadata();
			
			$sudo = array();
			$item_a = array(	'item_id'						=> $item->getId(),
								'item_title'					=> $item->item_title,
								'item_description'			=> $item->item_description,
								'type_id'					=> $item->type_id,
								'collection_id'					=> $item->collection_id,
								'item_language'				=> $item->item_language,
								'item_publisher'				=> $item->item_publisher,
								'item_rights'					=> $item->item_rights,
								'item_date'					=> $item->item_date,
								'item_coverage'				=> $item->item_coverage,
								'item_creator'				=> $item->item_creator,
								'item_additional_creator'		=> $item->item_additional_creator,
								'item_relation'				=> $item->item_relation,
								'item_subject'				=> $item->item_subject,
								'item_source'					=> $item->item_source,
								'item_public'					=> $item->item_public,
								'type_metadata'				=> @$item->type_metadata );

			$sudo['item_added'] = $item->item_added;
			$sudo['item_modified'] = $item->item_modified;

			if( $item->item_language != 'eng' && $item->item_language != 'fra' )
			{
				$item_a['item_language'] = 'other';
				$sudo['item_language_other'] = $item->item_language;
			}
			else
			{
				$item_a['item_language'] = $item->item_language;
				$sudo['item_language_other'] = null;
			}
			
			$location_a = array(	'address'	=> $item->location->address,
									'zipcode'	=> $item->location->zipcode,
									'latitude'	=> $item->location->latitude,
									'longitude'	=> $item->location->longitude,
									'mapType'	=> $item->location->mapType,
									'zoomLevel'	=> $item->location->zoomLevel,
									'cleanAddress'	=> $item->location->cleanAddress,
									'location_id'	=> $item->location->getId() );
			$sudo['Item'] = $item_a;
			$sudo['Location'] = $location_a;
			self::$_session->setValue( 'item_form_saved', $sudo );	
			return $item;
		}
	}
	
	private function commitForm()
	{	
		$adapter = Kea_DB_Adapter::instance();
		$adapter->beginTransaction();

		$item = new Item( self::$_request->getProperty( 'Item' ) );
		
		if( $item->item_language == 'other' && self::$_request->getProperty('item_language_other') )
		{
			$item->item_language = self::$_request->getProperty('item_language_other');
		}
		
		if( empty( $item->collection_id ) )
		{
			$item->collection_id = 'NULL';
		}
		
		if ( empty( $item->item_added) )
		{
			$item->item_added = null;
		}
		
		if ( empty( $item->item_modified) )
		{
			$item->item_modified = 'NULL';
		}

		if ( empty( $item->item_public) )
		{
			$item->item_public = '0';
		}

		// This is a kludge to make sure that editing an item doesn't destroy its relationship with the user who uploaded it [JMG]
		if ( !self::$_request->getProperty('item_edit') ) 
		{
			$item->user_id = self::$_session->getUser()->getId();
		}
		
		if ( empty( $item->type_id ) )
		{
			$item->type_id = 'NULL';
		}
		
		/* Deprecated in favor of the following if statement as of r351 
		if (self::$_session->getUser()) $item->user_id = self::$_session->getUser()->getId();
		*/
				
		if( $this->validates( $item ) )
		{
			$item->save();
			
			$location = new Location( self::$_request->getProperty( 'Location' ) );
			if( $location->hasValues() )
			{
				$item->getLocation();
				$location_id = $item->location->location_id;
				$location->item_id = $item->item_id;
				$location->latitude = round( $location->latitude, 5 );
				$location->longitude = round( $location->longitude, 5 );
				if( $location_id ) { $location->location_id = $location_id; }
				if( $this->validates( $location ) )
				{
					$location->save();	
				}
			}
		
			$tags = new Tags( self::$_request->getProperty( 'tags' ) );
			$tags->item_id = $item->item_id;
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
							$m->item_id = $item->item_id;
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
                                                'item_id' => $item->item_id,
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
		
		
			$files = File::add( $item->getId(), 'itemfile', self::$_request->getProperty('File') );
		
			$item->save();
		}

		if( count( $this->validationErrors ) > 0 ) {
			self::$_session->setValue( 'item_form_saved', $_REQUEST );
			$adapter->rollback();
			return false;
		} else {
			self::$_session->setValue( 'item_form_saved', null );
			$adapter->commit();
			return $item;
		}
	}
	
	protected function _total()
	{
		$mapper = new Item_Mapper();
		return $mapper->total();
	}
	
	protected function _totalInType( $type_id )
	{
		$mapper = new Item_Mapper();
		return $mapper->totalSliced( $type_id , null);
	}
	
	protected function _totalInCollection( $collection_id )
	{
		$mapper = new Item_Mapper();
		return $mapper->totalSliced( null, $collection_id );

	}

	
	protected function _deleteTypeAssociation( $item_id )
	{
		$adapter = Kea_DB_Adapter::instance();
		$adapter->update( 'items', array( 'type_id'	=> 'NULL' ), 'items.item_id = \'' . $item_id . '\'' );
		$adapter->delete( 'metatext', 'item_id = \'' . $item_id . '\'' );
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
		if( $id = self::$_request->getProperty( 'item_id' ) ) {
			/*
				First get the files and delete them.
				This is reinforced through foreign key constraints in mysql
				but the underlying physical files should also be deleted.
			*/
			$item = $this->_findById( $id );
			foreach( $item->files as $file )
			{
				File::delete( $file->getId() );
			}

			$mapper = new Item_Mapper;
			$mapper->delete( $id );
			switch ($type) 
			{
				case 'public': 
					$this->redirect( BASE_URI . DS . 'mycontributions' );
				break;
				case 'admin': 
					$this->redirect( BASE_URI . DS . 'items' . DS . 'all' );
				break;
				
			}
			$this->redirect( BASE_URI . DS . 'items' . DS . 'all' );
		}
	}
	
	protected function _findFeatured()
	{
		$mapper = new Item_Mapper;
		$featured = $mapper->find()
							->where( 'item_featured = ?', '1' );
		$this->applyPermissions( $featured );
		return $featured->execute(); 
	}

	protected function _findRandomFeatured()
	{
		$mapper = new Item_Mapper;
		$featured = $mapper->find()
							->where( 'item_featured = ?', '1' );
		$this->applyPermissions( $featured );
		$all = $featured->execute(); 
		return $all->getObjectAt( mt_rand( 0, $all->total() - 1 ) );
	}

	protected function _findRandomFeaturedWithThumb()
	{
/*		// First see if there even are thumbnails
		$mapper = new Item_Mapper;
		$has_thumbs = $mapper->find( 'items.item_id, files.file_id' )
							->join( 'files', 'items.item_id = files.item_id' )
							->where( 'items.item_featured = ?', '1' )
							->where( 'files.file_thumbnail_name != ?', '')
							->execute();

		if( $has_thumbs->total() == 0 )
		{
			return false;
		}
		
		// Or if there are featured with thumbs...
		$featured = $has_thumbs->getObjectAt( mt_rand( 0, $has_thumbs->total() - 1 ) );
		$new = $this->findById($featured->item_id);
		echo $new->item_title;
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
	
	protected function _getMapItems( $map_items = 40 )
	{
		return $this->_paginate( true, $map_items, true );
	}
	
	protected function _getRandomMapFeatured()
	{
		$mapper = new Item_Mapper;
		$featured = $mapper->find( "*, RPAD( SUBSTRING( item_description, 1, 140 ),  LENGTH( SUBSTRING( item_description, 1, 140 ) ) + 3, '.') as short_desc" )
							->where( 'item_featured = ?', '1' )
							->join( 'location', 'location.item_id = items.item_id' )
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