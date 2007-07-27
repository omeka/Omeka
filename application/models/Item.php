<?php
require_once 'Collection.php';
require_once 'Type.php';
require_once 'User.php';
require_once 'File.php';
require_once 'Tag.php';
require_once 'Taggable.php';
require_once 'Taggings.php';
require_once 'Metatext.php';
require_once 'ItemsPages.php';
require_once 'Section.php';
require_once 'ItemsRelations.php';
require_once 'Relatable.php';
require_once 'ItemTable.php';
require_once 'ItemTaggings.php';
require_once 'ExhibitTaggings.php';
/**
 * @package Omeka
 * 
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
		$this->ownsMany("File as Files","File.item_id");
		$this->ownsMany("Metatext", "Metatext.item_id");
		$this->ownsMany("ItemTaggings", "ItemTaggings.relation_id");
	//	$this->hasMany("Tag as Tags", "ItemTaggings.tag_id");

		$this->ownsMany("ItemsPages","ItemsPages.item_id");
		
		
		
		$this->ownsMany("ItemsRelations", "ItemsRelations.relation_id");
//		$this->hasMany("SectionPage as ExhibitPages", "ItemsPages.page_id");
		
		parent::setUp();
	}
	
	public function construct()
	{
		$this->_strategies[] = new Taggable($this);
		$this->_strategies[] = new Relatable($this);
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
        $this->hasColumn('type_id', 'integer');
        $this->hasColumn('collection_id', 'integer');
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
					
			case 'added':
			case 'modified':
				return $this->timeOfLastRelationship($name);
				break;
				
			case 'Tags':
				return $this->getTags();
			default:
				return parent::get($name);
				break;
		}
	}
	
	/**
	 * Will set the metatext for a given metafield 
	 * This is slow so should not be used unless setting only specific metafields
	 *
	 * @return void
	 **/
	public function setMetatext($field, $value)
	{
		$mt = $this->Metatext;
		
		//Look for the entry within the current set of metatext
		foreach ($mt as $k => $m) {
			if($m->Metafield->name == $field) {
				$m->text = $value;
				return true;
			}
		}
		
		//Find the metafield
		$metafield = Doctrine_Manager::getInstance()->getTable('Metafield')->findByName($field);
				
		if(!$metafield) {
			throw new Exception( 'Metafield named '.$field . ' does not exist!' );
		}
		
		//Create the new metatext entry and append to the rest of them
		$newMt = new Metatext;
		$newMt->text = $value;
		$newMt->Metafield = $metafield;
		$newMt->Item = $this;
		
		$mt->add($newMt);
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
			$ids[$k] = $v['id'];
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
			//If day or month is not included, then just check the year
			if( ($date[2] == '00') or ($date[1] == '00') ) {
				if($date[0] < 0 or $date[0] > date('Y')) {
					return false;
				}
			}
			//If the date is invalid, return false
			elseif( !checkdate($date[1], $date[2], $date[0]) ) {

					return false;
			
			}
			
			$this->$field = $mySqlDate;
			return true;
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
		
	public function getMetatext( $name, $return_text = true ) {	
		//first check the available metatext
		foreach ($this->Metatext as $k => $mt) {
			if($mt->Metafield->name == $name) {
				if($return_text) 
					return $mt->text;
				echo $text;
				return;
			}
		}
			
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
		return $this->getMetatext($name, $return_text);
	}
	
	///// END METADATA METHODS /////

   public function getRandomFileWithImage()
   {	
		$dql = "SELECT f.* FROM File f WHERE f.item_id = {$this->id} AND f.has_derivative_image = 1";
		if($res = $this->executeDql($dql)) {
			return $res->getFirst();
		}
   }

	public static function getRandomFeaturedItem($withImage=true)
	{
		if($withImage) {
			$sql = "SELECT i.id, RAND() as rand 
					FROM items i INNER JOIN files f ON f.item_id = i.id 
					WHERE i.featured = 1 AND f.has_derivative_image = 1 ORDER BY rand DESC LIMIT 1";
		}else {
			$sql = "SELECT i.id, RAND() as rand 
					FROM items i INNER JOIN files f ON f.item_id = i.id 
					WHERE i.featured = 1 ORDER BY rand DESC LIMIT 1";
		}
		$conn = Zend::Registry( 'doctrine' )->connection();

		$id = $conn->fetchOne($sql);
		
		if($id) {
			return Zend::Registry( 'doctrine' )->getTable('Item')->find($id);
		}
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

	public function commitForm($post, $save=true, $options=array())
	{
		if(!empty($post))
		{	
			$conn = $this->_table->getConnection();
			$conn->beginTransaction();
			
			$clean = $post;
			unset($clean['id']);
			
			
			//Process the separate date fields
			$validDate = $this->processDate('date',
								$clean['date_year'],
								$clean['date_month'],
								$clean['date_day']);
								
			$validCoverageStart = $this->processDate('temporal_coverage_start', 
								$clean['coverage_start_year'],
								$clean['coverage_start_month'],
								$clean['coverage_start_day']);
								
			$validCoverageEnd = $this->processDate('temporal_coverage_end', 
								$clean['coverage_end_year'],
								$clean['coverage_end_month'],
								$clean['coverage_end_day']);				
			
			//Special method for untagging other users' tags
			if($this->userHasPermission('untagOthers')) {
				if(array_key_exists('remove_tag', $post)) {
					$tagId = $post['remove_tag'];
					$tagToDelete = Zend::Registry( 'doctrine' )->getTable('Tag')->find($tagId);
					$current_user = Kea::loggedIn();
					if($tagToDelete) {
						$this->pluginHook('onUntagItem', array($tagToDelete->name, $current_user));
				
						//delete the tag from the Item
						$tagsDeleted = $this->deleteTags($tagToDelete, null, true);
					}
				}				
			}
			
			//Mirror the form to the record
			$this->setFromForm($clean);
			
			//Check to see if the date was valid
			if(!$validDate) {
				throw new Exception( 'The date provided is invalid.  Please provide a correct date.' );
			}
			
			//If someone is providing coverage dates, they need to provide both a start and end or neither
			if( (!$validCoverageStart and $validCoverageEnd) or ($validCoverageStart and !$validCoverageEnd) ) {
				throw new Exception( 'For coverage, both start date and end date must be specified, otherwise neither may be specified.' );
			}
			
			if(!empty($clean['change_type'])) return false;
			if(!empty($clean['add_more_files'])) return false;
			
			if(!empty($_FILES["file"]['name'][0])) {
				//Handle the file uploads
				foreach( $_FILES['file']['error'] as $key => $error )
				{ 
					try{
						$file = new File();
						$file->upload('file', $key);
						$this->Files->add($file);
					}catch(Exception $e) {
						$file->delete();
						$conn->rollback();
						throw $e;
					}
				
				}
			}
			
			/* Delete files what that have been chosen as such */
			if($filesToDelete = $clean['delete_files']) {
				foreach ($this->Files as $key=> $file) {
					if(in_array($file->id,$filesToDelete)) {
						$file->delete();
					}
				}
			}		
									
			//Handle the boolean vars
			if(array_key_exists('public', $clean)) {
				if($this->userHasPermission('makePublic')) {
					//If item is being made public
					if(!$this->public && $clean['public'] == 1) {
						$wasMadePublic = true;
					}
					
					$this->public = (bool) $clean['public'];
				}
			}
			
			if(array_key_exists('featured', $clean) and $this->userHasPermission('makeFeatured')) {
				$this->featured = (bool) $clean['featured'];
			}
			
			try {
				$this->save();
				
				//Tagging must take place after the Item has been saved (b/c otherwise no Item ID is set)
				if(array_key_exists('modify_tags', $clean) || !empty($clean['tags'])) {
					$user = Kea::loggedIn();
					$this->applyTagString($clean['tags'], $user->Entity);
				}
				
				//If the item was made public, fire the plugin hook
				if($wasMadePublic) {
					$this->pluginHook('onMakePublicItem');
				}
				
				$conn->commit();
				return true;
			}
			catch(Doctrine_Validator_Exception $e) {
				$this->gatherErrors($e);
				$conn->rollback();
				
				//Reload the files b/c of a stupid bug
				foreach ($this->Files as $key => $file) {
					if(!$file->exists()) {
						$file->delete();
					}
					unset($this->Files[$key]);
				}
				throw new Exception( $this->getErrorMsg() );				
			}	
		}
		return false;
	}

	public function postSave()
	{
		//Special process to save the metatext
		$mts = $this->Metatext;
		foreach ($mts as $mt) {
			$mt->item_id = $this->id;
		}
		$mts->save();		
	}

	public function hasFiles()
	{
		return ($this->Files->count() > 0);
	}
		
} // END class Item extends Kea_Domain_Record

?>