<?php
require_once 'Collection.php';
require_once 'Type.php';
require_once 'User.php';
require_once 'File.php';
require_once 'Tag.php';
require_once 'Taggable.php';
require_once 'ItemsTags.php';
require_once 'Metatext.php';
require_once 'ItemMetatext.php';
require_once 'ItemsFavorites.php';
require_once 'ItemsFulltext.php';

require_once 'ItemsPages.php';
require_once 'Section.php';

/**
 * @package Omeka
 * 
 * @todo Create/modify the ItemTable::findAll() method (or all find methods) to check for ACL privileges and only return the Items that are public
 **/
class Item extends Kea_Record
{		
	protected $error_messages = array(	'title' => array('notblank' => 'Item must be given a title.'));
	
	protected $constraints = array('collection_id','type_id','user_id');
	
	public function setUp() {
		$this->hasOne("Collection","Item.collection_id");
		$this->hasOne("Type","Item.type_id");
		$this->hasOne("User","Item.user_id");
		$this->ownsMany("File as Files","File.item_id");
		$this->ownsMany("ItemMetatext as Metatext", "ItemMetatext.item_id");
		$this->hasMany("Tag as Tags", "ItemsTags.tag_id");
		$this->ownsMany("ItemsFavorites", "ItemsFavorites.item_id");
		$this->ownsOne("ItemsFulltext", "ItemsFulltext.item_id");
		$this->ownsMany("ItemsTags", "ItemsTags.item_id");

		$this->ownsMany("ItemsPages","ItemsPages.item_id");
//		$this->hasMany("SectionPage as ExhibitPages", "ItemsPages.page_id");
		
		parent::setUp();
	}
	
	public function construct()
	{
		$this->_taggable = new Taggable($this);
		
		/*	Pull in the appropriate metadata fields
		 *	1) All metafields associated with the Item's type
		 *	2) All metafields associated with a plugin
		 * 	(metafields singularly associated with Items is not implemented)
		 */ 
		//$dql = "SELECT m.* FROM Metatext m "
	}
	
	public function setTableDefinition() {
//		$this->option('type', 'MYISAM');
		$this->setTableName('items');
		
		$this->hasColumn("title","string",255, "notblank|unique");
        $this->hasColumn('publisher', 'string', null, array('notnull' => true, 'default'=>''));
        $this->hasColumn('language', 'string', null, array('notnull' => true, 'default'=>''));
        $this->hasColumn('relation', 'string', null, array('notnull' => true, 'default'=>''));
        $this->hasColumn('spatial_coverage', 'string', null, array('notnull' => true, 'default'=>''));
        $this->hasColumn('rights', 'string', null, array('notnull' => true, 'default'=>''));
        $this->hasColumn('description', 'string', null, array('notnull' => true, 'default'=>''));
        $this->hasColumn('source', 'string', null, array('notnull' => true, 'default'=>''));
        $this->hasColumn('subject', 'string', null, array('notnull' => true, 'default'=>''));
        $this->hasColumn('creator', 'string', null, array('notnull' => true, 'default'=>''));
        $this->hasColumn('additional_creator', 'string', null, array('notnull' => true, 'default'=>''));
        $this->hasColumn('date', 'date');
        $this->hasColumn('added', 'timestamp', null);
        $this->hasColumn('modified', 'timestamp', null);
        $this->hasColumn('type_id', 'integer');
        $this->hasColumn('collection_id', 'integer');
        $this->hasColumn('user_id', 'integer');
        $this->hasColumn('contributor', 'string', null, array('notnull' => true, 'default'=>''));
        $this->hasColumn('rights_holder', 'string', null, array('notnull' => true, 'default'=>''));
        $this->hasColumn('provenance', 'string', null, array('notnull' => true, 'default'=>''));
        $this->hasColumn('citation', 'string', null, array('notnull' => true, 'default'=>''));
        $this->hasColumn('temporal_coverage_start', 'date');
        $this->hasColumn('temporal_coverage_end', 'date');
		$this->hasColumn("featured", "boolean", null,array('notnull' => true, 'default'=>'0'));
		$this->hasColumn("public", "boolean", null,array('notnull' => true, 'default'=>'0'));		
		
		$this->index('featured', array('fields' => array('featured')));
		$this->index('public', array('fields' => array('public')));
		$this->index('type', array('fields' => array('type_id')));
		$this->index('coll', array('fields' => array('collection_id')));
		$this->index('user', array('fields' => array('user_id')));
	}
	
/* @todo Uncomment this and finish optimizing the queries	
	public function get($name) {
		switch ($name) {
			case 'Tags':
				//make an optimized DQL query
				$tags = Doctrine_Manager::getInstance()->getTable('Tag')->getSome(null,null,null,null,$this);
				//Do I need to set some sort of relation marker or something to make sure that this tag collection saves when the item saves?
				return $tags;
				break;
			
			default:
				return parent::get($name);
				break;
		}
	}
*/	
	public function hasThumbnail()
	{
		$sql = "SELECT COUNT(f.id) thumbCount FROM files f WHERE f.item_id = ? AND f.thumbnail_filename IS NOT NULL";
		$res = $this->execute($sql, array($this->id), true);
		return $res > 0;
	}
	
	
/**
	 * Process the date info given, return false on invalid date given, otherwise set the appropriate field
	 *
	 * @return bool
	 **/
	public function processDate($field,$year,$month,$day) 
	{
			//Process the date fields, convert to YYYY-MM-DD
			$date = array();
			$date[0] = !empty($year) 	? $year 	: '0000';
			$date[1] = !empty($month) 	? $month 	: '00';
			$date[2] = !empty($day) 	? $day 		: '00';
			
			$mySqlDate = implode('-', $date);
			
			//If its a blank thing then its valid I suppose
			
			if($mySqlDate == '0000-00-00') {
					$this->$field = null;
					return true;
			}
			//If the date is invalid, return false
			elseif( !checkdate($date[1], $date[2], $date[0]) ) {

					return false;
			
			}else {
				
				
				$this->$field = $mySqlDate;
				return true;
			}					
	}
	
	public function getCitation()
	{
	    if(!empty($this->citation)) {
			return $this->citation;
		}

		$cite = '';
	    $cite .= $this->creator;
	    if ($cite != '') $cite .= ', ';
	    $cite .= ($this->title) ? '"'.$this->title.'." ' : '"Untitled." ';
	    $cite .= '<em>'.get_option('site_title').'</em>, ';
	    $cite .= 'Item #'.$this->id.' ';
	    $cite .= '(accessed '.date('F d Y, g:i a').') ';
	    //$cite .= '('.date('F d Y, g:i a', strtotime($this->added)).')';
	    return $cite;
	 }
	
	///// METADATA METHODS /////
	
	public function metadata( $name, $return_text = true ) {		
		foreach( $this->Metatext as $key => $record )
		{
			//metadata is either all plugin data, all type data, or a single field name
			if($name == $record->Metafield->name) {
				if($return_text) return $record->text;
				return $record;
			}
		}
		return null;
	}
	
	/**
	 * Alias of metadata()
	 *
	 * @return mixed
	 **/
	public function Metatext( $name, $return_text = true) {
		return $this->metadata($name, $return_text);
	}
	
	///// END METADATA METHODS /////
	

	
	public function isFavoriteOf($user) {
		$q = new Doctrine_Query();
		$q->from('ItemsFavorites if')
					->where('if.user_id = :user_id AND if.item_id = :item_id');
		$res = $q->execute(array('user_id' => $user->id, 'item_id' => $this->id));
		return count($res) > 0;
	}
	
   public function getRandomFileWithImage()
   {
           $q = new Doctrine_Query;
           $q->parseQuery("SELECT f.*, RANDOM() rand FROM File f WHERE f.item_id = ? AND f.thumbnail_filename IS NOT NULL AND f.thumbnail_filename != '' ORDER BY rand");
           return $q->execute(array($this->id))->getFirst();
   }

	public function hasFiles()
	{
		return ($this->Files->count() > 0);
	}
		
} // END class Item extends Kea_Domain_Record

?>