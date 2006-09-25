<?php

/*
	This might be a plugin type of class
*/

class Tags extends Kea_Plugin implements Iterator
{
	public $object_id;
	public $user_id;
	public $tags		= array();

	protected $_validate = array(	'object_id' => array( '/([0-9])+/', 'The object_id must be set.' )
	 						//		'user_id'	=> array( '/([0-9]+)/', 'The user_id must be set.' )
	 							);

	protected $total	= 0;
	protected $pointer	= 0;
	
	public function __construct( $tags = null )
	{
		parent::__construct();

		if( is_string( $tags ) )
		{
			$tags = $this->stringToTags( $tags );
			$this->addTags( $tags );
		}
		elseif( is_array( $tags ) )
		{
			$tags = $this->cleanTags( $tags );
			$this->addTags( $tags );
		}
	}
	
	public function stringToTags( $tags, $delim = ',' )
	{
		$tags = explode( $delim, $tags );
		return $this->cleanTags( $tags );
	}
	
	private function cleanTags( array $tags )
	{
		foreach( $tags as $key => &$tag )
		{
			/* $potty_mouth = array( '/sh[^oOuU]+t/i', '/cr[^oOiI]+p/i', '/f[uU]+ck/i', '/fc[uU]k/i', '/(f[\w]ck)/i', '/b[^uUaA]+tch/i', '/c[^aAeE]+nt/i', '/(k[\w]ke)/i' );

			foreach( $potty_mouth as $swear )
			{
				$tag = preg_replace( $swear, '', $tag );
			} */

			$tag = trim( preg_replace( '/[^a-zA-Z0-9\s]/', '', $tag ) );
			
			if( $tag == '' )
			{
				unset( $tags[$key] );
			}
			
			$tag = strtolower( $tag );
		}
		return $tags;
	}
	
	private function addTags( array $tags )
	{
		foreach( $tags as $tag )
		{
			if( $tag != '' )
			{
				$this->add( $tag );	
			}
		}
	}
	
	public function findByObject( $object_id )
	{
		$select = $this->_adapter->select();
		$select->from( 'objects_tags', 'tags.*, COUNT( objects_tags.tag_id ) as tagCount' )
			   ->joinLeft( 'tags', 'objects_tags.tag_id = tags.tag_id' )
			   ->where( 'objects_tags.object_id = ?', $object_id )
			   ->group( 'tags.tag_name' );
		$result = $this->_adapter->fetchAssoc( $select );
		foreach( $result as $tag )
		{
			$this->add( $tag );
		}
	}
	
	public function findByUser( $user_id, $object_id = null )
	{
		$select = $this->_adapter->select();
		$select->from( 'objects_tags', 'tags.tag_id, tags.tag_name, COUNT(objects_tags.tag_id) as tagCount' )
			   ->joinLeft( 'tags', 'objects_tags.tag_id = tags.tag_id' )
			   ->where( 'objects_tags.user_id = ?', $user_id )
			   ->group( 'tags.tag_name' );
		if( $object_id )
		{
			$select->where( 'objects_tags.object_id = ?', $object_id );
		}
 		return $this->_adapter->fetchAssoc( $select );
	}
	
	public static function deleteAssociation( $tag_id, $object_id )
	{
		$inst = new self;
		return $inst->_adapter->delete( 'objects_tags', 'object_id=\'' . $object_id . '\' AND tag_id=\'' . $tag_id . '\'' );
	}
	
	public static function addMyTags( $tag_string, $object_id, $user_id )
	{
		$inst = new self( $tag_string );
		$inst->object_id = $object_id;
		$inst->user_id = $user_id;
		$inst->save();
	}
	
	public static function deleteMyTag( $tag_id, $object_id, $user_id )
	{
		$inst = new self;
		return $inst->_adapter->delete( 'objects_tags', "object_id = '$object_id' AND tag_id = '$tag_id' AND user_id = '$user_id'" );
	}
	
	public function save()
	{
		if( !isset( $this->object_id ) )
		{
			return false;
		}
		
	/*	if( !isset( $this->user_id ) )
		{
			return false;
		} */
		
		if( count( $this->tags ) == 0 )
		{
			return false;
		}

		$tag_ids = array();

		foreach( $this->tags as $tag )
		{
			$select = $this->_adapter->select();
			$select->from( 'tags' )
				   ->where( 'tag_name = ?', $tag );
			$result = $this->_adapter->query( $select );
			if( $result->num_rows > 0 )
			{
				$a = $result->fetch_assoc();
				$tag_ids[] = $a['tag_id'];
			}
			else
			{
				$this->_adapter->insert( 'tags', array( 'tag_name' => $tag ) );
				$tag_ids[] = $this->_adapter->insertId();
			}
		}
		
		foreach( $tag_ids as $tag_id )
		{
			$select = $this->_adapter->select();
			$select->from( 'objects_tags')
					->where( 'tag_id = ?', $tag_id )
					->where( 'object_id = ?', $this->object_id )
					->where( 'user_id =?', $this->user_id );
			$result = $this->_adapter->query( $select );
			if( $result->num_rows == 0 )
			{
				$this->_adapter->insert( 'objects_tags', array( 'tag_id' => $tag_id, 'object_id' => $this->object_id, 'user_id' => $this->user_id ) );
			}
		}
	}
	
	public function getTagsAndCount( $limit = '100', $alpha = true, $count = false, $object_id = null, $user_id = null )
	{
		$select = $this->_adapter->select();
		$select->joinLeft( 'tags', 'tags.tag_id = objects_tags.tag_id' )
			   ->group( 'tag_id' )
			   ->limit( $limit );

		if( $object_id )
		{
			$select->from( 'objects_tags', 'objects_tags.tag_id, objects_tags.object_id, COUNT( objects_tags.tag_id ) as tagCount, tags.tag_name' )
				   ->where( 'objects_tags.object_id = ?', $object_id );
		}
		else
		{
			$select->from( 'objects_tags', 'objects_tags.tag_id, COUNT( objects_tags.tag_id ) as tagCount, tags.tag_name' );
		}
		
		if( $user_id )
		{
			$select->where( 'objects_tags.user_id = ?', $user_id );
		}
				
		if( $alpha )
		{
			$count = false;
			$select->order( array( 'tags.tag_name' => 'ASC' ) );
		}
		
		if( $count )
		{
			$select->order( array( 'tagCount' => 'DESC' ) );
		}

		// Add authentication check here
		//$select->join( 'objects', 'objects.object_id = objects_tags.object_id' );
		//$this->applyPermissions( $select );


		return $this->_adapter->fetchAssoc( $select );
	}
	
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

	public function getMaxCount( $user_id = null )
	{
		$large = $this->getTagsAndCount( 1, false, true, null, $user_id );
		if( count( $large ) == 1 )
		{
			return $large[0]['tagCount'];
		}
		return false;
	}
	
	public function tagCount()
	{
		$select = $this->_adapter->select();
		$select->from( 'objects_tags', 'COUNT(tag_id) as tagCount' );
		return $this->_adapter->fetchOne( $select );
	}
	
	public function getTagAt( $key )
	{
		if( $key >= $this->total || $key < 0 )
		{
			return null;
		}

		if( array_key_exists( $key, $this->tags ) )
		{
			return $this->tags[$key];
		}
		return false;
	}
	
	
	protected function add( $tag )
	{
		$this->tags[$this->total] = $tag;
		$this->total++;
	}
	
	public function rewind()
	{
		$this->pointer = 0;
	}
	
	public function current()
	{
		return $this->getTagAt( $this->pointer );
	}
	
	public function key()
	{
		return $this->pointer;
	}
	
	public function nextIsValid()
	{
		if( $this->getTagAt( $this->pointer + 1 ) )
		{
			return true;
		}
		return false;
	}
	
	public function next()
	{
		$tag = $this->getTagAt( $this->pointer );
		if( $tag ) {
			$this->pointer++;
		}
		return $tag;
	}
	
	public function valid()
	{
		return( !is_null( $this->current() ) );
	}
	
	public function total()
	{
		return $this->total;
	}
}

?>