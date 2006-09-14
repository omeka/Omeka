<?php

class Object extends Kea_Domain_Model
{
	protected $validate		=	array(	//'object_title'					=> array( '/(\w)+/', 'Objects must have a title.' ),
	 									'object_status'					=> array( '/(\w)+/', 'Objects must have a status.' ),
	 									'object_contributor_consent'	=> array( '/(\w)+/', 'Objects must be given a contributor\'s consent.'),
	 									'object_contributor_posting'	=> array( '/(\w)+/', 'Objects must have posting consent.' ) );
	
	public $object_id;
	public $object_title;
	public $object_publisher;
	public $object_language;
	public $object_relation;
	public $object_coverage_start;
	public $object_coverage_end;
	public $object_rights;
	public $object_description;
	
	public $object_date;
	public $object_status;
	public $object_contributor_consent;
	public $object_contributor_posting;
	
	public $object_added;
	public $object_modified;
	public $objectType_id;
	public $object_featured;
	
	public $user_id;
	
	// Collection data
	public $collection_id;
	
	// Contributor data
	public $contributor_id;
	
	public $creator_id;
	
	public $creator_other;
	
	// Category <=> KJVObjectType data (same thing)
	public $category_id;
	
	public function __construct( $array = null )
	{
		parent::__construct( $array );
		if( empty( $this->contributor_id ) )
		{
			$this->contributor_id = null;
		}
		
		if( empty( $this->category_id ) )
		{
			$this->category_id = null;
		}
		
		if( empty( $this->creator_id ) )
		{
			$this->creator_id = null;
		}
	}
	
	// Maybe this should be called in the "construct" method above, so that metadata is always available?? [JMG]
	public function getCategoryMetadata($metafield_name = NULL, $length = -1, $append = '...')
	{
		$this->mapper()->getCategoryMetadata( $this );
		if (!is_null($metafield_name)) {
			
			// There's *got* to be a more elegant way of doing this than this... [JMG]
			foreach ($this->category_metadata as $meta_field) {
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
												->where( 'object_id = ?', $this->getId() )
												->order( array( 'file_thumbnail_name' => 'DESC' ) )
												->execute();
		return $this;
	}
	
	public function getFileTotal()
	{
		$adapter = Kea_DB_Adapter::instance();
		$select = $adapter->select();
		$select->from( 'files', 'COUNT(*) AS count' )
				->where( 'object_id =?', $this->getId() );
		$result = $adapter->fetchOne( $select );
		return $result;
	}
	
	public function getFilesWithThumbnails()
	{
		$this->files = self::getMapper( 'File' )->find()
												->where( 'object_id = ?', $this->getId() )
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
		$this->tags->findByObject( $this->getId() );
		return $this;
	}
	
	public function myTags( $user_id )
	{
		$tags = new Tags;
		return $tags->findByUser( $user_id, $this->getId() );
	}
	
	public function getLocation()
	{
		$this->location = self::getMapper( 'Location' )->findByObject( $this->getId() );
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
		$select->from( 'objects_favorites' )
				->where( 'user_id = ?', $user_id )
				->where( 'object_id = ?', $this->getId() );
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
			return $adapter->insert( 'objects_favorites', array(	'object_id'	=> $this->getId(),
			 														'user_id'	=> $user_id ) );
		}
		elseif( $this->isFav( $user_id ) )
		{
			return $adapter->delete( 'objects_favorites', "object_id = '" . $this->getId() . "' AND user_id = '" . $user_id . "'" );
		}
		return false;
	}

	public function isFeatured( )
	{
		$adapter = Kea_DB_Adapter::instance();
		$select = $adapter->select();
		$select->from( 'objects' )
				->where( 'object_id = ?', $this->getId() )
				->where( 'object_featured = ?', '1' );
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
			return $adapter->update( 'objects', array( 'object_featured'	=> '1' ), 'objects.object_id = \'' . $this->getId() . '\'' );
		}
		elseif( $this->isFeatured() )
		{
			return $adapter->update( 'objects', array( 'object_featured'	=> '0' ), 'objects.object_id = \'' . $this->getId() . '\'' );
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
		if ( $this->object_contributor_posting != 'anonymously') {
			$cite .= $this->contributor->getName();
			if ($cite != '') $cite .= ', ';
		}
		else 
		 	$cite = 'Anonymous, ';
		$cite .= ($this->object_title) ? '"'.$this->object_title.'." ' : '"Untitled." ';
		$cite .= '<em>'.INFO_TITLE.'</em>, ';
		$cite .= 'Object #'.$this->object_id.' ';
		$cite .= '(accessed '.date('F d Y, g:i a').') ';
	//	$cite .= '&lt;'.WEB_CONTENT_DIR.DS.'objects'.DS.$this->object_id.'&gt;';
		//$cite .= '('.date('F d Y, g:i a', strtotime($this->object_added)).')';
		return $cite;
	}
	
	public function delete()
	{
		return $this->mapper()->delete( $this->getId() );
	}
	
	/**
	 * standard accessor method for object descriptions
	 *
	 * Has been changed to support use of the file_description instead of object description,
	 * provided at least one of them is valid.  Can also be modified to pull descriptions from metadata,
	 * which is useful for stories, etc. where the main text is not in the object_description.  Basically
	 * this is a convenience function.
	 * 
	 * Priority is currently chosen in this order: file_description, object_description
	 * 
	 * @return mixed The string description of the object, otherwise false
	 * @author Kris Kelly
	 **/
	public function getDesc()
	{
		$this->getFiles();
		$fileDesc = @$this->files->getObjectAt(0)->file_description;
		$objectDesc = $this->object_description;
		if(!empty($fileDesc))
		{
			return $fileDesc;
		}
		elseif(!empty($objectDesc))
		{
			return $objectDesc;
		}
		else
		{
			return false;
		}
	}
}

?>