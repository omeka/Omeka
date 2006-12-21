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
/*
	This might be a plugin type of class
*/

class Tags extends Kea_Plugin implements Iterator
{
	public $item_id;
	public $user_id;
	
	/**
	 * Array containing the tags themselves
	 * 
	 * Composition: for new (unsaved) tags, 1-dimensional array containing the string tag names.
	 * for retrieved tags, 2-dimensional array where each tag entry contains associative array
	 * with 'tag_id', 'tag_name' and 'tagCount'
	 *
	 * @var array
	 **/
	public $tags		= array();

	/**
	 * Validation rules for tags
	 * 
	 * Existing validation rules require a valid numeric integer item_id and user_id
	 *
	 * @var array
	 **/
	protected $_validate = array(	'item_id' => array( '/([0-9])+/', 'The item_id must be set.' )
	 						//		'user_id'	=> array( '/([0-9]+)/', 'The user_id must be set.' )
	 							);

	/**
	 * Current number of tags stored in the Tags object 
	 *
	 * @var int
	 **/
	protected $total	= 0;
	
	/**
	 * Key for the current accessed tag 
	 *
	 * @var int
	 **/
	protected $pointer	= 0;
	
	/**
	 * Constructor
	 *
	 * Converts the string or array input into tags and stores them in the object
	 *  
	 * @param array $tags Either a delimited string of tags or an array containing them
	 * @author Nate Agrin
	 **/
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
	
	/**
	 * Explodes the tags into an array and returns a clean version
	 *
	 * @param string $tags A string of tags, separated by a delimiter
	 * @param string $delim The delimiter to use (comma by default)
	 * @return array
	 * @author Nate Agrin
	 **/
	public function stringToTags( $tags, $delim = ',' )
	{
		$tags = explode( $delim, $tags );
		return $this->cleanTags( $tags );
	}
	
	/**
	 * Converts the current array of tags into a delimited string
	 *
	 * @param string $delim Delimiter (comma by default)
	 * @return string
	 * @author Kris Kelly
	 **/
	public function tagsToString( $delim = ',')
	{
		$tag_string = '';
		for ( $i=0; $i < count($this->tags); $i++ )
		{ 
			$tag_string .= $this->tags[$i]['tag_name'] . ( !empty($this->tags[$i+1]) ? $delim . ' ' : '' ); 
		}
		return $tag_string;
	}
	
	/**
	 * Removes all non-alphanumeric characters and (potentially) swear words from tags
	 *
	 * @param array $tags 
	 * @return array
	 * @author Nate Agrin
	 **/
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
	
	/**
	 * Adds an array of tags to the Tags object one by one
	 *
	 * @param array $tags
	 * @return void
	 * @author Nate Agrin
	 **/
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
	
	/**
	 * Retrieves the tags associated with an item and adds them to the object
	 *
	 * @param int $item_id 
	 * @return void
	 * @author Nate Agrin
	 **/
	public function findByItem( $item_id )
	{
		$select = $this->_adapter->select();
		$select->from( 'items_tags', 'tags.*, COUNT( items_tags.tag_id ) as tagCount' )
			   ->joinLeft( 'tags', 'items_tags.tag_id = tags.tag_id' )
			   ->where( 'items_tags.item_id = ?', $item_id )
			   ->group( 'tags.tag_name' );
		$result = $this->_adapter->fetchAssoc( $select );
		foreach( $result as $tag )
		{
			$this->add( $tag );
		}
	}
	
	/**
	 * Retrieves the tags associated with a user and returns them
	 *
	 * @param int $user_id Unique ID associated with the user
	 * @param int $item_id Unique ID associated with an item (optional)
	 * @return array
	 * @author Nate Agrin
	 **/
	public function findByUser( $user_id, $item_id = null )
	{
		$select = $this->_adapter->select();
		$select->from( 'items_tags', 'tags.tag_id, tags.tag_name, COUNT(items_tags.tag_id) as tagCount' )
			   ->joinLeft( 'tags', 'items_tags.tag_id = tags.tag_id' )
			   ->where( 'items_tags.user_id = ?', $user_id )
			   ->group( 'tags.tag_name' );
		if( $item_id )
		{
			$select->where( 'items_tags.item_id = ?', $item_id );
		}
 		return $this->_adapter->fetchAssoc( $select );
	}
	
	/**
	 * Remove all associations between a tag and an item
	 *
	 * @param int $tag_id
	 * @param int $item_id
	 * @return bool TRUE on success, FALSE on failure
	 * @author Nate Agrin
	 **/
	public static function deleteAssociation( $tag_id, $item_id )
	{
		$inst = new self;
		return $inst->_adapter->delete( 'items_tags', 'item_id=\'' . $item_id . '\' AND tag_id=\'' . $tag_id . '\'' );
	}
	
	/**
	 * Create an instance of Tags, load it up and save it to the database
	 *
	 * @param string $tag_string
	 * @param int $item_id
	 * @param int $user_id
	 * @return void
	 * @author Nate Agrin
	 **/
	public static function addMyTags( $tag_string, $item_id, $user_id )
	{
		$inst = new self( $tag_string );
		$inst->item_id = $item_id;
		$inst->user_id = $user_id;
		$inst->save();
	}
	
	/**
	 * Remove a tag <-> item association for only a specific user
	 *
	 * @param int $tag_id
	 * @param int $item_id
	 * @param int $user_id
	 * @return bool TRUE on success, FALSE on failure
	 * @author Nate Agrin
	 **/
	public static function deleteMyTag( $tag_id, $item_id, $user_id )
	{
		$inst = new self;
		return $inst->_adapter->delete( 'items_tags', "item_id = '$item_id' AND tag_id = '$tag_id' AND user_id = '$user_id'" );
	}
	
	/**
	 * Save the Tags object to the database
	 *
	 * @return bool FALSE: if item_id not set, or no tags to save, or failure to save to database, TRUE on success
	 * @author Nate Agrin
	 **/		
	public function save()
	{
		if( !isset( $this->item_id ) )
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
			$select->from( 'items_tags')
					->where( 'tag_id = ?', $tag_id )
					->where( 'item_id = ?', $this->item_id )
					->where( 'user_id =?', $this->user_id );
			$result = $this->_adapter->query( $select );
			if( $result->num_rows == 0 )
			{
				$this->_adapter->insert( 'items_tags', array( 'tag_id' => $tag_id, 'item_id' => $this->item_id, 'user_id' => $this->user_id ) );
			}
		}
	}
	
	/**
	 * Retrieve tags along with the number of times they occur
	 *
	 *
	 * @param int $limit Maximum number of tags to retrieve
	 * @param bool $alpha Sort tags in alphabetical order (optional)
	 * @param bool $count Sort tags from highest count to lowest count (optional)
	 * @param int $item_id Retrieve only tags associated with a specific item (optional)
	 * @param int $user_id Retrieve only tags associated with a specific user (optional)
	 * @return array 'tag_id', 'tag_name', 'tagCount' ('item_id' if chosen)
	 * @author Nate Agrin
	 **/
	public function getTagsAndCount( $limit = '100', $alpha = true, $count = false, $item_id = null, $user_id = null )
	{
		$select = $this->_adapter->select();
		$select->joinLeft( 'tags', 'tags.tag_id = items_tags.tag_id' )
			   ->group( 'tag_id' )
			   ->limit( $limit );

		if( $item_id )
		{
			$select->from( 'items_tags', 'items_tags.tag_id, items_tags.item_id, COUNT( items_tags.tag_id ) as tagCount, tags.tag_name' )
				   ->where( 'items_tags.item_id = ?', $item_id );
		}
		else
		{
			$select->from( 'items_tags', 'items_tags.tag_id, COUNT( items_tags.tag_id ) as tagCount, tags.tag_name' );
		}
		
		if( $user_id )
		{
			$select->where( 'items_tags.user_id = ?', $user_id );
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
		//$select->join( 'items', 'items.item_id = items_tags.item_id' );
		//$this->applyPermissions( $select );


		return $this->_adapter->fetchAssoc( $select );
	}
	
	/**
	 * Filter tags based on user permissions
	 *
	 * TODO: put this and getTagsAndCount() in the Tags controller
	 *
	 * @param Kea_DB_Select $select SELECT object that will query for the tags
	 * @return void
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
	 * Retrieve the count of the tag with the highest count for all users (or a specific user)
	 *
	 * @param int $user_id (optional)
	 * @return int
	 * @author Nate Agrin
	 **/
	public function getMaxCount( $user_id = null )
	{
		$large = $this->getTagsAndCount( 1, false, true, null, $user_id );
		if( count( $large ) == 1 )
		{
			return $large[0]['tagCount'];
		}
		return false;
	}
	
	/**
	 * Retrieve the number of tags currently in the system
	 *
	 * @return int
	 * @author Nate Agrin
	 **/
	public function tagCount()
	{
		$select = $this->_adapter->select();
		$select->from( 'items_tags', 'COUNT(tag_id) as tagCount' );
		return $this->_adapter->fetchOne( $select );
	}
	
	/**
	 * Retrieve a tag from the Tags object given its index
	 *
	 * @param int $key Index of the tag (starting from 0)
	 * @return void
	 * @author Nate Agrin
	 **/
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
	
	/**
	 * Add a tag to the Tags object
	 *
	 * @param mixed $tag Either a 1-dimensional array or a string
	 * @return void
	 * @author Nate Agrin
	 **/	
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