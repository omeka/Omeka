<?php

class Object extends Kea_Domain_Model
{
	protected $validate		=	array(	'object_title'					=> array( '/(\w)+/', 'Objects must have a title.' ),
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
	
	public function getCategoryMetadata()
	{
		$this->mapper()->getCategoryMetadata( $this );
		return $this;
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
		if (strlen($this->object_description) > $length ):
			$shortDesc = substr($this->object_description, 0, strrpos($this->object_description, ' ', $length-strlen($this->object_description)));
			$shortDesc = $shortDesc.$append;
			return $shortDesc;
		else: 
			return $this->object_description;
		endif;
	}


	public function getCitation() 
	{
		if ( $this->object_contributor_posting != 'anonymously') 
			$cite = $this->contributor->contributor_first_name . ' ' . $this->contributor->contributor_last_name.', ';
		else 
		 	$cite = 'Anonymous, ';
		$cite .= '"'.$this->object_title.'." ';
		$cite .= '<em>Katrina\'s Jewish Voices</em>, ';
		$cite .= 'Object #'.$this->object_id.' ';
		$cite .= '('.date('F d Y, g:i a').')';
		//$cite .= '('.date('F d Y, g:i a', strtotime($this->object_added)).')';
		return $cite;
	}
	
	public function delete()
	{
		return $this->mapper()->delete( $this->getId() );
	}

}

?>