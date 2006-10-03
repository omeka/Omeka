<?php

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
	public $itemType_id;
	public $item_featured;
	public $item_public;
	
	public $user_id;
	
	// Collection data
	public $collection_id;
	
	// Contributor data
	// Contributor system is being removed/revised in future revisions [KBK]
	//public $contributor_id;
	
	//public $creator_id;
	
	public $creator_other;
	
	// Type <=> KJVObjectType data (same thing)
	public $type_id;
	
	public function __construct( $array = null )
	{
		parent::__construct( $array );
		if( empty( $this->contributor_id ) )
		{
			$this->contributor_id = null;
		}
		
		if( empty( $this->type_id ) )
		{
			$this->type_id = null;
		}
		
		if( empty( $this->creator_id ) )
		{
			$this->creator_id = null;
		}
	}
	
	// Maybe this should be called in the "construct" method above, so that metadata is always available?? [JMG]
	public function getTypeMetadata($metafield_name = NULL, $length = -1, $append = '...')
	{
		$this->mapper()->getTypeMetadata( $this );
		if (!is_null($metafield_name)) {
			
			// There's *got* to be a more elegant way of doing this than this... [JMG]
			foreach ($this->type_metadata as $meta_field) {
				if ($meta_field['metafield_name'] == $metafield_name) {
					if ( ($length > 0) && (strlen($meta_field['metatext_text']) > $length) ) {
						$short = substr($meta_field['metatext_text'], 0, strrpos($meta_field['metatext_text'], ' ', $length-strlen($meta_field['metatext_text']))).$append;
						return $short;
					}
					else return $meta_field['metatext_text'];
				}
			}
			return false;
		}
		else{
			return $this;
		}
	}
	
	public function getFiles()
	{
		$this->files = self::getMapper( 'File' )->find()
												->where( 'item_id = ?', $this->getId() )
												->order( array( 'file_thumbnail_name' => 'DESC' ) )
												->execute();
		return $this;
	}
	
	public function getFileTotal()
	{
		$adapter = Kea_DB_Adapter::instance();
		$select = $adapter->select();
		$select->from( 'files', 'COUNT(*) AS count' )
				->where( 'item_id =?', $this->getId() );
		$result = $adapter->fetchOne( $select );
		return $result;
	}
	
	public function getFilesWithThumbnails()
	{
		$this->files = self::getMapper( 'File' )->find()
												->where( 'item_id = ?', $this->getId() )
												->where( 'file_thumbnail_name != ?', '' )
												->execute();
		return $this;
	}
	
	public function getRandomThumbnail()
	{
		// Pick a random one
		$files = $this->getFilesWithThumbnails();
		return $this->files->getObjectAt(mt_rand(0, $this->files->total() - 1 ) );
	}
	
	public function getTags()
	{
		$this->tags = new Tags;
		$this->tags->findByItem( $this->getId() );
		return $this;
	}
	
	public function myTags( $user_id )
	{
		$tags = new Tags;
		return $tags->findByUser( $user_id, $this->getId() );
	}
	
	public function getLocation()
	{
		$this->location = self::getMapper( 'Location' )->findByItem( $this->getId() );
		return $this;
	}
	
	public function getContributor()
	{
		$this->contributor = self::getMapper( 'Contributor' )->findById( $this->contributor_id );
		return $this;
	}
	
	public function getCreator()
	{
		$this->creator = self::getMapper( 'Contributor' )->findById( $this->creator_id );
		return $this;
	}
	
/*	public function getCollection()
	{
		$this->collection = self::getMapper( 'Collection' )->findById( $this->collection_id );
		return $this->collection_id;
	} */
	
	public function isFav( $user_id )
	{
		$adapter = Kea_DB_Adapter::instance();
		$select = $adapter->select();
		$select->from( 'items_favorites' )
				->where( 'user_id = ?', $user_id )
				->where( 'item_id = ?', $this->getId() );
		$result = $adapter->query( $select );
		print_r($adapter->error());
		if( $result->num_rows > 0 )
		{
			return true;
		}
		return false;
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
		print_r($adapter->error());
		if( $result->num_rows > 0 )
		{
			return true;
		}
		return false;
	}
	
	public function addRemoveFeatured( )
	{
		$adapter = Kea_DB_Adapter::instance();
		if( !$this->isFeatured() )
		{
			return $adapter->update( 'items', array( 'item_featured'	=> '1' ), 'items.item_id = \'' . $this->getId() . '\'' );
		}
		elseif( $this->isFeatured() )
		{
			return $adapter->update( 'items', array( 'item_featured'	=> '0' ), 'items.item_id = \'' . $this->getId() . '\'' );
		}
		return false;
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
		if (strlen($fullDesc) > $length ):
			$shortDesc = substr($fullDesc, 0, strrpos($fullDesc, ' ', $length-strlen($fullDesc)));
			$shortDesc = $shortDesc.$append;
			return $shortDesc;
		else: 
			return $fullDesc;
		endif;
	}


	public function getCitation() 
	{
		$cite = '';
		$cite .= $this->contributor->getName();
		if ($cite != '') $cite .= ', ';
		$cite .= ($this->item_title) ? '"'.$this->item_title.'." ' : '"Untitled." ';
		$cite .= '<em>'.INFO_TITLE.'</em>, ';
		$cite .= 'Item #'.$this->item_id.' ';
		$cite .= '(accessed '.date('F d Y, g:i a').') ';
	//	$cite .= '&lt;'.WEB_CONTENT_DIR.DS.'items'.DS.$this->item_id.'&gt;';
		//$cite .= '('.date('F d Y, g:i a', strtotime($this->item_added)).')';
		return $cite;
	}
	
	public function delete()
	{
		return $this->mapper()->delete( $this->getId() );
	}
	
	/**
	 * standard accessor method for item descriptions
	 *
	 * Has been changed to support use of the file_description instead of item description,
	 * provided at least one of them is valid.  Can also be modified to pull descriptions from metadata,
	 * which is useful for stories, etc. where the main text is not in the item_description.  Basically
	 * this is a convenience function.
	 * 
	 * Priority is currently chosen in this order: file_description, item_description
	 * 
	 * @return mixed The string description of the item, otherwise false
	 * @author Kris Kelly
	 **/
	public function getDesc()
	{
		$this->getFiles();
		$fileDesc = @$this->files->getObjectAt(0)->file_description;
		$itemDesc = $this->item_description;
		if(!empty($fileDesc))
		{
			return $fileDesc;
		}
		elseif(!empty($itemDesc))
		{
			return $itemDesc;
		}
		else
		{
			return false;
		}
	}
}

?>