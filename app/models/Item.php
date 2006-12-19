<?php
/**
 *
 * Copyright 2006:
 * George Mason University
 * Center for History and New Media,
 * State of Virginia 
 *
 * LICENSE
 *
 * This source file is subject to the GNU Public License that
 * is bundled with this package in the file GPL.txt, and the
 * specific license found in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL: 
 * http://www.gnu.org/licenses/gpl.txt
 * If you did not receive a copy of the GPL or local license and are unable to
 * obtain it through the world-wide-web, please send an email 
 * to chnm@gmu.edu so we can send you a copy immediately.
 *
 * This software is licensed under the GPL license by the Center
 * For History and New Media, at George Mason University, except 
 * where other free software licenses apply.
 * The source code may only be reused or redistributed if the
 * copyright notice and licensing information above are retained,
 * and other included Zend and Cake licenses, are preserved. 
 * 
 * @author Nate Agrin
 * @contributors Josh Greenburg, Kris Kelly, Dan Stillman
 * @license http://www.gnu.org/licenses/gpl.txt GNU Public License
 */
require_once 'Kea/Domain/Model.php';
class Item extends Kea_Domain_Model
{
	protected $validate		=	array(	'item_title'					=> array( '/(\w)+/', 'Items must have a title.' ) );
	
	public $item_id;
	public $item_title;
	public $item_publisher;
	public $item_language;
	public $item_relation;
	public $item_coverage;
	public $item_rights;
	public $item_description;
	public $item_source;
	public $item_subject;
	public $item_creator;
	public $item_additional_creator;
	public $item_date;
	
	public $item_added;
	public $item_modified;
	public $item_featured;
	public $item_public;
	
	public $user_id;
	
	// Collection data
	public $collection_id;
	
	// Type <=> KJVObjectType data (same thing)
	public $type_id;
	
	//Put this in your model to use the syntax Item::findById()
	public static function findById( $id ) {
		return self::doFindById( $id, __CLASS__ );
	}
/*	
	public static function total() {
		return self::doTotal( __CLASS__ );
	}
*/	
	/**
	 * If $name is non-public property, then call loadName() and return $this->$name
	 *
	 * @return mixed
	 * @author Kris Kelly
	 **/
	public function __get( $name )
	{
		if ( !property_exists( 'Item', $name) )
		{
			$load_method = 'load' . ucwords($name);
			call_user_func_array( array($this, $load_method), array() );			
		}
		
		return $this->$name;
	}
	
	private function loadMetadata()
	{
		$this->doLoadMetadata( $this );
		return $this;
	}
	
	private function loadFiles()
	{
		if( !is_array($this->files) ) 
		{
			$sql = "SELECT * FROM files WHERE item_id = :item_id ORDER BY file_thumbnail_name DESC";
			$res = $this->query($sql, array('item_id' => $this->getId() ) );
			$this->files = $this->findObjects($res, 'File');
		}

		return $this;
	}
	
	private function loadThumbnails()
	{
		$thumbs = array();
		foreach( $this->files as $file )
		{
			if( !empty($file->file_thumbnail_name) ) 
			{
				array_push( $thumbs, $file );
			}
		}
		$this->thumbnails = $thumbs;
		return $this;		
	}
	
	private function loadTags()
	{
		$this->tags = new Tags;
		$this->tags->findByItem( $this->getId() );
		return $this;
	}
	
	private function loadCollection()
	{
		if( !(@$this->collection instanceof Collection) )
		{
			$this->collection = self::getMapper( 'Collection' )->findById( $this->collection_id );		
		}
		return $this;
	}
	
	private function loadType()
	{
		if( !(@$this->type instanceof Type) )
			$this->type = self::getMapper( 'Type' )->findById( $this->type_id );
			
		return $this;
	}
	
	/**
	 * retrieve one or all metadata fields for the item
	 * 
	 * @param string $metafield_name The name of the metafield to retrieve (optional)
	 * @param int $length The length of the metatext to display (optional)
	 * @param string $append The string to append to the metadata snippet, '...' by default
	 * @return mixed If a metafield name is given, it returns the text (or a snippet thereof), otherwise it returns the entire type_metadata array
	 * @author Kris Kelly
	 **/
	public function getTypeMetadata($metafield_name = NULL, $length = NULL, $append = '...')
	{
		if (!is_null($metafield_name) && @$this->metadata ) {
			
			foreach ($this->type_metadata as $meta_field) {
				if ($meta_field['metafield_name'] == $metafield_name) {
					if ( $length ) return snippet($meta_field['metatext_text'], 0, $length, $append);
					else return $meta_field['metatext_text'];
				}
			}
			return false;
		}
		else{
			return @$this->metadata;
		}
	}
	
	/**
	 * Retrieves a file at the given order in the list of files associated with an item, starting with 0, 
	 * i.e. $this->getFile(0) would retrieve the first file.  
	 *
	 * @return File
	 * @author Kris Kelly
	 **/
	public function getFile( $order = 0 )
	{
		return $this->files[$order];
	}
	
	/**
	 * Retrieve the number of files associated with the item
	 *
	 * @return int
	 * @author Kris Kelly
	 **/
	public function getFileTotal()
	{
		return count($this->files);
	}
	
	/**
	 * standard accessor method for item descriptions
	 *
	 * @return string
	 * @author Kris Kelly
	 **/
	public function getDesc() { return $this->item_description; }	
	
	/**
	 * Retrieve one random file that has a thumbnail
	 *
	 * @return File
	 * @author Kris Kelly
	 **/
	public function getRandomThumbnail()
	{
		// Pick a random one
		return $this->thumbnails->getObjectAt( mt_rand(0, $this->thumbnails->total() - 1 ) );
	}
	
	/**
	 * Retrieve only the tags associated with a given user ID (returns a Tags object)
	 *
	 * @return Tags
	 * @author Kris Kelly
	 **/
	public function myTags( $user_id )
	{
		$tags = new Tags;
		return $tags->findByUser( $user_id, $this->getId() );
	}

	public function isFav( $user_id )
	{
		$adapter = Kea_DB_Adapter::instance();
		$select = $adapter->select();
		$select->from( 'items_favorites' )
				->where( 'user_id = ?', $user_id )
				->where( 'item_id = ?', $this->getId() );
		$result = $adapter->query( $select );
		return ( $result->num_rows > 0 );
	}
	
	public function addRemoveFav( $user_id )
	{
		$adapter = Kea_DB_Adapter::instance();
		if( !$this->isFav( $user_id ) )
		{
			return $adapter->insert( 'items_favorites', array(	'item_id'	=> $this->getId(),
			 														'user_id'	=> $user_id ) );
		}
		elseif( $this->isFav( $user_id ) )
		{
			return $adapter->delete( 'items_favorites', "item_id = '" . $this->getId() . "' AND user_id = '" . $user_id . "'" );
		}
		return false;
	}

	public function isFeatured( )
	{
		$adapter = Kea_DB_Adapter::instance();
		$select = $adapter->select();
		$select->from( 'items' )
				->where( 'item_id = ?', $this->getId() )
				->where( 'item_featured = ?', '1' );
		$result = $adapter->query( $select );
		return ( $result->num_rows > 0 );
	}
	
	public function isPublic()
	{
		return $this->item_public;
	}
	
	public function addRemoveFeatured( )
	{
		return $this->flip('item_featured');
	}
	
	public function flip( $field )
	{
		$adapter = Kea_DB_Adapter::instance();
		if( $this->$field == 0)
		{
			$res = $adapter->update( 'items', array( $field	=> '1' ), 'items.item_id = \'' . $this->getId() . '\'' );
			if($res) $this->$field = 1;
			return $res;
		}
		elseif( $this->$field == 1 )
		{
			$res = $adapter->update( 'items', array( $field	=> '0' ), 'items.item_id = \'' . $this->getId() . '\'' );
			if($res) $this->$field = 0;
			return $res;
		}
		return false;
	}
	
	public function getShortDesc ( $length = 250 , $append = '...')
	{
		$fullDesc = $this->getDesc();
		if (strlen($fullDesc) > $length ) return snippet($fullDesc, 0, $length, $append);
		return $fullDesc;
	}


	public function getCitation() 
	{
		$cite = '';
		$cite .= $this->item_creator;
		if ($cite != '') $cite .= ', ';
		$cite .= ($this->item_title) ? '"'.$this->item_title.'." ' : '"Untitled." ';
		$cite .= '<em>'.SITE_TITLE.'</em>, ';
		$cite .= 'Item #'.$this->item_id.' ';
		$cite .= '(accessed '.date('F d Y, g:i a').') ';
	//	$cite .= '&lt;'.WEB_CONTENT_DIR.DS.'items'.DS.$this->item_id.'&gt;';
		//$cite .= '('.date('F d Y, g:i a', strtotime($this->item_added)).')';
		return $cite;
	}
	
	public function hasFiles()
	{
		return ($this->files->total() > 0);
	}
	
	public function hasThumbnail()
	{
		return ( $this->thumbnails->total() > 0 );
	}
	
	//Everything below is copied directly from the Item Mapper.  12/19/06
	
	public function singleUpdate( $field, $value, $obj_id )
	{
		return self::$_adapter->update( $this->_table_name, array( $field => $value), 'item_id = ' . $obj_id );
	}
	
	public function doLoadMetadata( Item $obj )
	{
		$select = self::$_adapter->select()
					->from('types_metafields', 'metafield_name, metatext_text, metafields.metafield_id, metafield_description, metatext.metatext_id' )
					->joinLeft( 'metafields', 'metafields.metafield_id = types_metafields.metafield_id' )
					->joinLeft( 'metatext', 'metatext.metafield_id = metafields.metafield_id' )
					->where( 'types_metafields.type_id = ?', $obj->type_id )
					->where( 'metatext.item_id = ?', $obj->item_id )
					->order( array( 'metatext_id' => 'ASC' ) );
			
		if( $result = $this->query( $select ) ) {
			if( $result->num_rows > 0) {
				while( $row = $result->fetch_assoc() ) {
					$obj->type_metadata[$row['metafield_id']] = array(	'metafield_id'			=> $row['metafield_id'],
														'metafield_name'		=> $row['metafield_name'],
														'metafield_description'	=> $row['metafield_description'],
														'metatext_id'			=> $row['metatext_id'],
														'metatext_text'			=> $row['metatext_text'] );
				}
				return $obj;
			} else {
				$result->free();
				$select = self::$_adapter->select()
							->from('types_metafields', 'metafield_name, metafield_description, metafields.metafield_id' )
							->joinLeft( 'metafields', 'metafields.metafield_id = types_metafields.metafield_id' )
							->where( 'types_metafields.type_id = ?', $obj->type_id )
							->order( array( 'metafield_id' => 'ASC' ) );;
				$result = $this->query( $select );
				while( $row = $result->fetch_assoc() ) {
					$obj->type_metadata[$row['metafield_id']] = array( 	'metafield_id'			=> $row['metafield_id'],
														'metafield_name'		=> $row['metafield_name'],
														'metafield_description'	=> $row['metafield_description'],
														'metatext_text'			=> null );
				}
			}
		} else {
			throw new Kea_DB_Mapper_Exception( self::$_adapter->error() );
		}
	}
	
	public static function total()
	{
		$adapter = Kea_DB_Adapter::instance();
		$select = $adapter->select();
		$select->from( 'itemsTotal', '*' );
		return $adapter->fetchOne( $select );
	}
	
	public function totalSliced( $type_id = null, $collection_id = null)
	{
		$select = self::$_adapter->select();
		$select->from( 'items', 'COUNT(*) as count' );
		if ( $type_id != null) $select->where( 'items.type_id = ?', $type_id );
		if ( $collection_id != null) $select->where( 'items.collection_id = ?', $collection_id );
		return self::$_adapter->fetchOne( $select );
	}
}

?>