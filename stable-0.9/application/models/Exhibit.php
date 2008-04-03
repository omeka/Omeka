<?php
require_once 'ExhibitSection.php';
require_once 'Tag.php';
require_once 'Taggings.php';
require_once 'Taggable.php';
require_once 'ExhibitTable.php';
require_once 'Orderable.php';
require_once 'ExhibitPermissions.php';

/**
 * Exhibit
 * @package: Omeka
 */
class Exhibit extends Omeka_Record
{
	public $title;
	public $description;
	public $credits;
	public $featured;
	public $public;
	
	public $theme;
	public $slug;
	
	protected $_related = array('Sections'=>'loadOrderedChildren', 'Tags'=>'getTags');

	protected function _validate()
	{
		if(empty($this->title)) {
			$this->addError('title', 'Exhibit must be given a title.');
		}
		
		if(strlen($this->title) > 255) {
			$this->addError('title', 'Title for an exhibit must be 255 characters or less.');
		}
		
		if(empty($this->slug)) {
			$this->addError('slug', 'Exhibit must be given a valid slug.');
		}
		
		if(!$this->fieldIsUnique('slug')) {
			$this->addError('slug', 'Your URL slug is already in use by another exhibit.  Please choose another.');
		}
		
		if(strlen($this->theme) > 30) {
			$this->addError('theme', 'The name of your theme must be 30 characters or less.');
		}
		
		if(strlen($this->slug) > 30) {
			$this->addError('slug', 'The slug for your exhibit must be 30 characters or less.');
		}
	}
	
	protected function _delete()
	{
		//Just delete the sections and the cascade will take care of the rest
		
		$sections = $this->Sections;
		
		foreach ($sections as $section) {
			$section->delete();
		}
				
/*
		//This query will delete everything from the exhibits tables when an exhibit is deleted
		//This is semi-duplicated in the Section, ExhibitPage, ExhibitPageEntry models as necessary
		$exhibit_id = $this->id;
		
		$delete = "DELETE items_section_pages, section_pages, sections, exhibits FROM exhibits 
		LEFT JOIN sections ON sections.exhibit_id = exhibits.id
		LEFT JOIN section_pages ON section_pages.section_id = sections.id
		LEFT JOIN items_section_pages ON items_section_pages.page_id = section_pages.id
		WHERE exhibits.id = $exhibit_id;";
		
		$db->exec($delete);
*/	
		
		
	}
	
	public function construct()
	{
		$this->_modules[] = new Taggable($this);
		$this->_modules[] = new Relatable($this);
		$this->_modules[] = new Orderable($this, 'ExhibitSection', 'exhibit_id', 'Sections');		
	}
		
	protected function beforeSaveForm(&$post)
	{					
		//Whether or not the exhibit is featured
		$this->featured = (bool) $post['featured'];
		unset($post['featured']);
		
		//Make an exhibit slug if the posted slug is empty
		//This is duplicated exactly in the Section class
		$slugFodder = !empty($post['slug']) ? $post['slug'] : $post['title'];
		$post['slug'] = generate_slug($slugFodder);
	}
	
	/**
	 * Check to see whether the slug field is empty, then provide one
	 *
	 * @return void
	 **/
	protected function beforeValidate()
	{
		if(empty($this->slug)) {
			$this->slug = generate_slug($this->title);
		}
	}
	
	protected function afterSaveForm($post)
	{
		//Add the tags after the form has been saved
		$current_user = Omeka::loggedIn();		
		$this->applyTagString($post['tags'], $current_user->Entity, true);	
	}
	
	public function getSection($slug)
	{
		$db = get_db();
		$sql = "SELECT s.* FROM $db->ExhibitSection s WHERE s.slug = ? AND s.exhibit_id = ?";

        return $this->getTable('ExhibitSection')->fetchObjects($sql, array( strtolower($slug), (int) $this->id), true);	
	}
	
	/**
	 * The number of sections in the exhibit
	 *
	 * @return int
	 **/
	public function getSectionCount()
	{
		return $this->getChildCount();
	}
}

?>