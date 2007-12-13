<?php 
require_once 'Collection.php';
require_once 'Type.php';
require_once 'User.php';
require_once 'File.php';
require_once 'Tag.php';
require_once 'Taggable.php';
require_once 'Taggings.php';
require_once 'Metatext.php';
require_once 'MetatextTable.php';
require_once 'Exhibit.php';
require_once 'Relatable.php';
require_once 'ItemTable.php';
require_once 'ItemPermissions.php';	
/**
* Item
*/
class Item extends Omeka_Record
{		
	public $title;
	public $publisher = '';
	public $language = '';
	public $relation = '';
	public $spatial_coverage = '';
	public $rights = '';
	public $description = '';
	public $source = '';
	public $subject = '';
	public $creator = '';
	public $additional_creator = '';
	public $format = '';
	public $date;
	public $type_id;
	public $collection_id;
	public $contributor = '';
	public $rights_holder = '';
	public $provenance = '';
	public $citation = '';
	public $temporal_coverage_start;
	public $temporal_coverage_end;
	public $featured = 0;
	public $public = 0;	
	
	/**
	 *
	 * @see Item::setMetatext()
	 *
	 * @return void
	 **/
	protected $_metatext;
	
	protected $_related = array(
		'Collection'=>'getCollection', 
		'TypeMetadata'=>'getTypeMetadata', 
		'Tags'=>'getTags',
		'Type'=>'getType',
		'FormTypeMetadata'=>'getFullTypeMetadata',
		'Files'=>'getFiles');

	protected function construct()
	{
		$this->_modules[] = new Taggable($this);
		$this->_modules[] = new Relatable($this);
	}

	//Pulling in related data
	protected function getCollection()
	{		
			$lk_id = (int) $this->collection_id;
			return $this->getTable('Collection')->find($lk_id);			
	}
		
	protected function getTypeMetadata()
	{
		return $this->getTable('Metatext')->findTypeMetadata($this, true);
	}
	
	protected function getFullTypeMetadata()
	{
		return $this->getTable('Metatext')->findTypeMetadata($this, false);
	}
	
	protected function getType()
	{
		return $this->getTable('Type')->find($this->type_id);
	}
	
	protected function getFiles()
	{
		return $this->getTable('File')->findByItem($this->id);
	}
	
	protected function getUserWhoCreated()
	{
		$creator = $this->getRelatedEntities('added');
		
		if(is_array($creator)) $creator = current($creator);
		
		return $creator;
	}
	
	/**
	 * Stop the form submission if we are using the non-JS form to change the type or add files
	 *
	 * Also, do not allow people to change the public/featured status of an item unless they got permission
	 *
	 * @return void
	 **/
	protected function beforeSaveForm(&$post)
	{

		if(!empty($post['change_type'])) return false;
		if(!empty($post['add_more_files'])) return false;
		
		if(!$this->userHasPermission('makePublic')) {
			unset($post['public']);
		}
		
		if(!$this->userHasPermission('makeFeatured')) {
			unset($post['featured']);
		}
	}
	
	private function deleteFiles($ids = null) 
	{
		if(!is_array($ids)) return false;
		
		//Retrieve file objects so that we have the benefit of the plugin hooks
		foreach ($ids as $file_id) {
			$file = $this->getTable('File')->find($file_id);
			$file->delete();
		}		
	}
	
	/**
	 * Save metatext, tags, files provided from the form
	 * Basically we can't do any of this stuff until the item has a valid item ID
	 * 
	 * @return void
	 **/
	public function afterSaveForm($post)
	{
		$this->saveFiles();
		
		//Removing tags from other users
		if($this->userHasPermission('untagOthers')) {
			
			if(array_key_exists('remove_tag', $post)) {
				$this->removeOtherTag($post['remove_tag']);
			}
		}
		
		//Delete files that have been designated by passing an array of IDs through the form
		$this->deleteFiles($post['delete_files']);
		
		//Change the tags (remove some, add some)
		if(array_key_exists('tags', $post)) {
			$user = Omeka::loggedIn();
			$entity = $user->Entity;
			if($entity) {
				$this->applyTagString($post['tags'], $entity);
			}
		}
		
		//Fire a plugin hook specifically for items that have had their 'public' status changed
		if(isset($post['public']) and ($this->public == '1') ) {
			fire_plugin_hook('make_item_public', $this);
		}
		
		//Save metatext from the form
		if(!empty($post['metafields'])) {
			foreach ($post['metafields'] as $metafield_id => $mt_a) {
				$mt_obj = get_db()->getTable('Metatext')->findByItemAndMetafield($this->id, $metafield_id);
			
				$mt_obj->text = (string) $mt_a['text'];
				$mt_obj->save();
			}			
		}
	}
	
	/**
	 * Save metatext provided via Item::setMetatext
	 *
	 * @return void
	 **/
	protected function afterSave()
	{
		$this->saveMetatext();
	}
	
	private function removeOtherTag($tag_id)
	{
		//Special method for untagging other users' tags
		
		$tagToDelete = $this->getTable('Tag')->find($tag_id);
		$current_user = Omeka::loggedIn();
		if($tagToDelete) {
			fire_plugin_hook('remove_item_tag',  $tagToDelete->name, $current_user);
	
			//delete the tag from the Item
			$tagsDeleted = $this->deleteTags($tagToDelete, null, true);
		}
	}
	
	/**
	 * All of the custom code for deleting an item
	 *
	 * @return void
	 **/
	protected function _delete()
	{	
		//Delete files one by one
		$files = $this->Files;
		
		foreach ($files as $file) {
			$file->delete();
		}
		
		//Delete metatext
		$metatext = get_db()->getTable('Metatext')->findByItem($this->id);
		
		foreach ($metatext as $entry) {
			$entry->delete();
		}
		
		//Update the exhibits to get rid of all references to the item anywhere in there
		$db = get_db();
		
		$update = "UPDATE $db->ExhibitPageEntry SET item_id = NULL WHERE item_id = ?";
		$db->exec($update, array($this->id));
	}
	
	private function saveFiles()
	{
		
		if(!empty($_FILES["file"]['name'][0])) {			
			
			File::handleUploadErrors('file');
			//Handle the file uploads
			foreach( $_FILES['file']['error'] as $key => $error )
			{ 
				try{
					$file = new File();
					$file->upload('file', $key);
					$file->item_id = $this->id;
					$file->save();
					fire_plugin_hook('after_upload_file', $file, $this);
				}catch(Exception $e) {
					if(!$file->exists()) {
						$file->unlinkFile();
					}
				throw $e;
				}
			}	
		}
	}	
	
	/**
	 * Saves any extended metatext that was set via Item::setMetatext()
	 *
	 * @return void
	 **/
	private function saveMetatext()
	{
		if(!empty($this->_metatext)) {
			foreach ($this->_metatext as $field => $text) {
				//Retrieve the metafield_id given the name of the $field
				$metafield_id = $this->getTable('Metafield')->findIdFromName($field);
			
				if(!$metafield_id) {
					throw new Exception( 'There is no metafield with the name:' . $field . "!" );
				}
			
				//Retrieve a metatext object corresponding to that field for this item
				$mt_obj = $this->getTable('Metatext')->findByItemAndMetafield($this->id, $metafield_id);
			
				$mt_obj->text = $text;
			
				//Save the Metatext row
				$mt_obj->save();
			}			
		}
	}
	
	/**
	 * There is some code duplication in this method, but only a couple of lines.  Remains to see if it is a problem.
	 *
	 * @return void
	 **/
	protected function filterInput($input)
	{
		$options = array('namespace'=>'Omeka_Filter');
		
		$filters = array(
			
			//Text values
			'title' 	=> 	'StringTrim',
			'subject' 	=> 	'StringTrim',
			'description' 	=> 	'StringTrim',
			'creator' 	=> 	'StringTrim',
			'additional_creator' 	=> 	'StringTrim',
			'source' 	=> 	'StringTrim',
			'publisher' 	=> 	'StringTrim',
			'language' 	=> 	'StringTrim',
			'provenance' 	=> 	'StringTrim',
			'citation' 	=> 	'StringTrim',
			'tags' 	=> 	'StringTrim',
			'contributor' 	=> 	'StringTrim',
			'rights' 	=> 	'StringTrim',
			'rights_holder' 	=> 	'StringTrim',
			'relation' 	=> 	'StringTrim',
			'spatial_coverage' 	=> 	'StringTrim',


			//Date values
			'date_year' 	=> 	array('StringTrim', 'Digits'),
			'date_month' 	=> 	array('StringTrim', 'Digits'),
			'date_day' 	=> 	array('StringTrim', 'Digits'),

			'coverage_start_year' 	=> 	array('StringTrim', 'Digits'),
			'coverage_start_month' 	=> 	array('StringTrim', 'Digits'),
			'coverage_start_day' 	=> 	array('StringTrim', 'Digits'),

			'coverage_end_year' 	=> 	array('StringTrim', 'Digits'),
			'coverage_end_month' 	=> 	array('StringTrim', 'Digits'),
			'coverage_end_day' 	=> 	array('StringTrim', 'Digits'),


			//Foreign keys
			'type_id' => 'ForeignKey',
			'collection_id' => 'ForeignKey',
			
			//Booleans
			'public'=>'Boolean',
			'featured'=>'Boolean');
			
		$filter = new Zend_Filter_Input($filters, null, $input, $options);

		$clean = $filter->getUnescaped();
		
		//Now handle proper parsing of the date fields
		
		//I couldn't get this to jive with Zend's thing so screw them
		$dateFilter = new Omeka_Filter_Date;
		
		if($clean['date_year']) {
			$clean['date'] = $dateFilter->filter($clean['date_year'], $clean['date_month'], $clean['date_day']);
		}
	
		if($clean['coverage_start_year']) {
			$clean['temporal_coverage_start'] = $dateFilter->filter($clean['coverage_start_year'], $clean['coverage_start_month'], $clean['coverage_start_day']);
		}
		
		if($clean['coverage_end_year']) {
			$clean['temporal_coverage_end'] = $dateFilter->filter($clean['coverage_end_year'], $clean['coverage_end_month'], $clean['coverage_end_day']);			
		}
	
		//Ok now let's process the metafields
		
		if(!empty($clean['metafields'])) {
			foreach ($clean['metafields'] as $key => $mf) {
				$clean['metafields'][$key] = array_map('trim', $mf);
			}			
		}
		
		//Now, happy shiny user input
		return $clean;		
	}
	
	public function hasFiles()
	{
		$db = get_db();
		$sql = "SELECT COUNT(f.id) FROM $db->File f WHERE f.item_id = ?";
		$count = (int) $db->fetchOne($sql, array((int) $this->id));
		
		return $count > 0;
	}
	
	/**
	 * Provides an idiom for saving extended metadata outside the context of the form (useful for plugins)
	 *
	 * @return void
	 **/
	public function setMetatext($field, $value)
	{
		$this->_metatext[$field] = $value;
	}
	
	/**
	 * This will retrieve specific values for metatext.  It does this by retrieving/caching all the available 
	 * metatext for the item, then checking that for whatever data is desired
	 *
	 * @return string|null
	 **/
	public function getMetatext($field)
	{
		if( !($metatext = $this->getCached('Metatext'))) {
			$metatext = $this->getTable('Metatext')->findByItem($this->id);
			
			$this->addToCache($metatext, 'Metatext');
		}
		
		$obj = $metatext[$field];
		
		if($this->_metatext[$field]) {
			return $this->_metatext[$field];
		}
		elseif($obj) {
			return $obj->text;
		}
	}
	
	/**
	 * Easy facade for the Item class so that it almost acts like an iterator
	 *
	 * @return Item|false
	 **/
	public function previous()
	{
		return get_db()->getTable('Item')->findPrevious($this);
	}
	
	public function next()
	{
		return get_db()->getTable('Item')->findNext($this);
	}
		
	/**
	 * Facade for the Exhibit Table's check method to see if an item is contained in an exhibit
	 *
	 * @return void
	 **/
	public function isInExhibit($exhibit)
	{
		return get_db()->getTable('Exhibit')->exhibitHasItem($exhibit->id, $this->id);
	}
	
	//Everything past this is elements of the old code that may be changed or deprecated
	
		/**
	 * Retrieve simply the names of the fields, converted to words and uppercase
	 *
	 * @return array
	 **/
	public static function fields($prefix=true)
	{
		
		//Hack to the get the list of columns
		$cols = get_db()->getTable('Item')->getColumns();

		//Avoid certain fields because they are DB keys or protected/private
		$avoid = array('id', 'type_id', 'collection_id', 'featured', 'public');

		$fields = array();
		foreach ($cols as $col) {
			if(in_array($col, $avoid)) continue;
			
			//Field name should not have underscores and should be uppercase
			$field = Omeka::humanize($col);
			
			$key = $prefix ? 'item_' . $col : $col; 
			$fields[$key] = $field;
		}
		return $fields;
	}
	
	public function hasThumbnail()
	{
		$db = get_db();
		
		$sql = "SELECT COUNT(f.id) FROM $db->File f WHERE f.item_id = ? AND f.has_derivative_image = 1";
		
		$count = $db->fetchOne($sql, array((int) $this->id));
			
		return $count > 0;
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
}
 
?>
