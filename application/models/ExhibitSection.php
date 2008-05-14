<?php
require_once 'ExhibitPage.php';
/**
 * Section
 * @package: Omeka
 */
class ExhibitSection extends Omeka_Record
{
	
	//Make sure $section_order is processed correctly even when aliased to $order
	public $title;
	public $description;
	public $exhibit_id;
	public $order;
	public $slug;
	
	protected $_related = array('Pages'=>'loadOrderedChildren', 'Exhibit'=>'getExhibit');
		
	public function construct()
	{
		$this->_modules[] = new Orderable($this, 'ExhibitPage', 'section_id', 'Pages');
	}

	protected function _validate()
	{
		if(empty($this->title)) {
			$this->addError('title', 'Sections of an exhibit must be given a title.');
		}
		
		if(empty($this->exhibit_id) or !is_numeric($this->exhibit_id)) {
			$this->addError('exhibit_id', 'Exhibit sections must be associated with an exhibit.');
		}
		
		if(empty($this->order) or !is_numeric($this->order)) {
			$this->addError('order', 'Exhibit section must be properly ordered with an exhibit.');
		}
		
		if(empty($this->slug)) {
			$this->addError('slug', "Slug must be given for each section of an exhibit.");
		}
		
		if($this->exhibit_id and !$this->slugIsUnique($this->slug)) {
			$this->addError('slug', 'Slugs for sections of an exhibit must be unique within that exhibit.  Please modify the slug so that it is unique.');
		}
	}

	protected function slugIsUnique($slug)
	{
		$db = $this->getDb();
		$exhibit_id = (int) $this->exhibit_id;
		
		if(!$exhibit_id) {
			$this->addError('exhibit_id', 'Section must be associated with a valid exhibit.');
			return false;
		}
		
		$sql = "SELECT COUNT(DISTINCT(s.id)) FROM $db->ExhibitSection s WHERE s.slug = ? AND s.exhibit_id = ?";
		$pass = array($slug, $exhibit_id);
		
		//If the record is persistent, get the count of sections 
		//with that slug that aren't this particular record
		if($this->exists()) {
			$sql .= " AND s.id != ?";
			$pass[] = (int) $this->id;			
		}
				
		//If there are no other sections with that particular slug, then it is unique
		$count = (int) $db->fetchOne($sql, $pass);
		return ($count == 0);
		
	}

	protected function _delete()
	{			
		foreach ($this->Pages as $page) {
			$page->delete();
		}
		
/*
		$id = (int) $this->id;
		//Delete thyself and all thine dependencies
		$delete = "DELETE items_section_pages, section_pages, sections FROM sections 
		LEFT JOIN section_pages ON section_pages.section_id = sections.id
		LEFT JOIN items_section_pages ON items_section_pages.page_id = section_pages.id
		WHERE sections.id = $id;";
		
		$this->getDb()->exec($delete);
*/					
	}
		
	//Deleting a section must re-order the other sections
	protected function afterDelete()
	{
		$exhibit = $this->Exhibit;
		$exhibit->reorderChildren();
	}
	
	/**
	 * @since 7-24-07 Now with slugs!
	 *
	 **/
	protected function beforeSaveForm(&$post)
	{
		//We need to make a slug for this section
		$slugFodder = !empty($post['slug']) ? $post['slug'] : $post['title'];

		$post['slug'] = generate_slug($slugFodder);
	}
	
	protected function getExhibit()
	{
		return $this->getTable('Exhibit')->find($this->exhibit_id);
	}
	
	public function getPageCount()
	{
		return $this->getChildCount();
	}
	
	public function getPage($order)
	{
		$db = $this->getDb();
		$sql = "SELECT p.* FROM $db->ExhibitPage p WHERE p.order = ? AND p.section_id = ?";
		return $this->getTable('ExhibitPage')->fetchObject($sql, array($order,$this->id));
	}
	
	public function hasPages()
	{
		$count = $this->getPageCount();
		return $count > 0;
	}
}

?>