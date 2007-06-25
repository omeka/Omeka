<?php
require_once 'Collection.php';
require_once 'Type.php';
require_once 'User.php';
require_once 'File.php';
require_once 'Tag.php';
require_once 'Taggable.php';
require_once 'ItemsTags.php';
require_once 'Metatext.php';
require_once 'ItemsFavorites.php';
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
	
	protected $_metatext;
	protected $_metafields;
		
	public function setUp() {
		$this->hasOne("Collection","Item.collection_id");
		$this->hasOne("Type","Item.type_id");
		$this->hasOne("User","Item.user_id");
		$this->ownsMany("File as Files","File.item_id");
		$this->ownsMany("Metatext", "Metatext.item_id");
		$this->hasMany("Tag as Tags", "ItemsTags.tag_id");
		$this->ownsMany("ItemsFavorites", "ItemsFavorites.item_id");
		$this->ownsMany("ItemsTags", "ItemsTags.item_id");

		$this->ownsMany("ItemsPages","ItemsPages.item_id");
//		$this->hasMany("SectionPage as ExhibitPages", "ItemsPages.page_id");
		
		parent::setUp();
	}
	
	public function construct()
	{
		$this->_taggable = new Taggable($this);
		
		
	}
	
	public function setTableDefinition() {
		
		$this->option('type', 'MYISAM');
		$this->setTableName('items');
		
		$this->hasColumn("title","string",255, array('notnull'=>true, 'default'=>''));
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
		
		$this->index('search_all', array('fields' => array( 
												'title', 
												'publisher', 
												'language', 
												'relation', 
												'spatial_coverage', 
												'rights', 
												'description', 
												'source', 
												'subject', 
												'creator', 
												'additional_creator', 
												'contributor', 
												'rights_holder', 
												'provenance', 
												'citation'),
											'type' => 'fulltext'));
		$this->index('title_search', array( 'fields' => 'title', 'type' => 'fulltext'));
 		$this->index('publisher_search', array( 'fields' => 'publisher', 'type' => 'fulltext'));
		$this->index('language_search', array( 'fields' => 'language', 'type' => 'fulltext'));
 		$this->index('relation_search', array( 'fields' => 'relation', 'type' => 'fulltext'));
 		$this->index('spatial_coverage_search', array( 'fields' => 'spatial_coverage', 'type' => 'fulltext'));
 		$this->index('rights_search', array( 'fields' => 'rights', 'type' => 'fulltext'));
 		$this->index('description_search', array( 'fields' => 'description', 'type' => 'fulltext'));
 		$this->index('source_search', array( 'fields' => 'source', 'type' => 'fulltext'));
 		$this->index('subject_search', array( 'fields' => 'subject', 'type' => 'fulltext'));
 		$this->index('creator_search', array( 'fields' => 'creator', 'type' => 'fulltext'));
 		$this->index('additional_creator_search', array( 'fields' => 'additional_creator', 'type' => 'fulltext'));
 		$this->index('contributor_search', array( 'fields' => 'contributor', 'type' => 'fulltext'));
 		$this->index('rights_holder_search', array( 'fields' => 'rights_holder', 'type' => 'fulltext'));
 		$this->index('provenance_search', array( 'fields' => 'provenance', 'type' => 'fulltext'));
 		$this->index('citation_search', array( 'fields' => 'citation', 'type' => 'fulltext'));
									
	}
	
	/**
	 * Optimized queries for obtaining related elements
	 *
	 * @return mixed
	 **/
	public function get($name) {
		switch ($name) {
			case 'Metafields':
				//Cache the metafields so we don't run the query too many times
				if(empty($this->_metafields)) {
					$mfIds = $this->getMetafieldIds();
					if(!empty($mfIds)) {
						$dql = "SELECT m.* FROM Metafield m WHERE";
						if(count($mfIds) > 1) 
							$dql .=  " m.id IN (".join(', ', $mfIds).")";
						else
							$dql .= " m.id = {$mfIds[0]}";
						$this->_metafields = $this->executeDql($dql);
					}else {
						$this->_metafields = new Doctrine_Collection('Metafield');
					}
				}
				
				return $this->_metafields;
			
			case 'Metatext':
				if(empty($this->_metatext)) {
					$mfIds = $this->getMetafieldIds();
					if(!empty($mfIds) and $this->exists() ) {
						$dql = "SELECT m.* FROM Metatext m WHERE ";
						if(count($mfIds) > 1)
							$dql .= "m.metafield_id IN(".join(', ',$mfIds).")";
						else 
							$dql .= "m.metafield_id = {$mfIds[0]}";
							
						$dql .= " AND m.item_id = {$this->id}";
						$this->_metatext = $this->executeDql($dql);	
					}else {
						$this->_metatext = new Doctrine_Collection('Metatext');
					}										
				}
				return $this->_metatext;
			default:
				return parent::get($name);
				break;
		}
	}
	
	/*	Pull in the appropriate metafield IDs
	 *	1) All metafields associated with the Item's type
	 *	2) All metafields associated with a plugin
	 * 	(metafields singularly associated with Items is not implemented)
	 *
	 * @param $for string 'All','Plugin','Type' Depending on which metafields you want
	 */ 
	protected function getMetafieldIds($for='All')
	{
		$mfTable = $this->getTableName('Metafield');
		$tmTable = $this->getTableName('TypesMetafields');
		$pTable = $this->getTableName('Plugin');
		$tTable = $this->getTableName('Type');
		
		//Get metafields for plugins
		if( ($for == 'Plugin') or ($for == 'All')) {
			$where[] = 'p.active = 1';
		}
		
		//Get the metafields for a specific Type
		if(!empty($this->type_id) and is_numeric($this->type_id) and ($for == 'All' || $for == 'Type')) {
			$where[] = "mf.id IN (
					SELECT mf.id FROM $mfTable mf
					INNER JOIN $tmTable tm ON tm.metafield_id = mf.id
					INNER JOIN $tTable t ON t.id = tm.type_id
					WHERE t.id = {$this->type_id} )";
		}
		$sql = "SELECT mf.id FROM $mfTable mf
				LEFT JOIN $pTable p ON p.id = mf.plugin_id
				WHERE ".join(' OR ', $where);

		$res = $this->execute($sql);
		$ids = array();
		foreach ($res as $k => $v) {
			$ids[$k] = $v[0];
		}
		return $ids;

	}
	
	public function hasThumbnail()
	{
		foreach ($this->Files as $k => $file) {
			if($file->hasThumbnail()) {
				return true;
			}
		}
		return false;
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
		$sql = "SELECT text FROM metatext mt INNER JOIN metafields mf ON mf.id = mt.metafield_id WHERE mt.item_id = ? AND mf.name = ?";
		$text = $this->execute($sql, array($this->id, $name), true);
		
		if($return_text) 
			return $text;
		
		echo $text;
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
		//Get files that have thumbnails
		$files = array();
		foreach ($this->Files as $k => $file) {
			if($file->hasThumbnail()) {
				$files[] = $file;
			}
		}
		$index = mt_rand(0, count($files)-1);
		
		return $files[$index];
   }

	public function isInExhibit($exhibit_id)
	{
		$iTable = $this->getTableName();
		$eTable = $this->getTableName('Exhibit');
		$ipTable = $this->getTableName('ItemsPages');
		$spTable = $this->getTableName('SectionPage');
		$sTable = $this->getTableName('Section'); 
		$sql = "SELECT COUNT(i.id) FROM $iTable i 
				INNER JOIN $ipTable ip ON ip.item_id = i.id 
				INNER JOIN $spTable sp ON sp.id = ip.page_id
				INNER JOIN $sTable s ON s.id = sp.section_id
				INNER JOIN $eTable e ON e.id = s.exhibit_id
				WHERE e.id = ? AND i.id = ?";
				
		$count = $this->execute($sql, array($exhibit_id, $this->id), true);
		
		return ($count > 0);
	}

	public function hasFiles()
	{
		return ($this->Files->count() > 0);
	}
		
} // END class Item extends Kea_Domain_Record

?>