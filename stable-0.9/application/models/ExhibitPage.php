<?php
require_once 'ExhibitPageEntry.php';
/**
 * Exhibit Page
 * @package: Omeka
 */
class ExhibitPage extends Omeka_Record
{
	public $section_id;
	public $layout;
	public $order;
	
	protected $_related = array('ExhibitPageEntry'=>'loadOrderedChildren', 'Section'=>'getSection');
	
	public function construct()
	{
		$this->_modules[] = new Orderable($this, 'ExhibitPageEntry', 'page_id', 'ExhibitPageEntry');
	}
	
	/**
	 * In order to validate:
	 * 1) must have a layout
	 * 2) must be properly ordered
	 * 3) Must be associated with a section
	 *
	 * @return void
	 **/
	protected function _validate()
	{
		if(empty($this->layout)) {
			$this->addError('layout', 'Layout must be provided for each exhibit page.');
		}
		
		if(empty($this->order)) {
			$this->addError('order', 'Exhibit page must be ordered within its section.');
		}
		
		if(empty($this->section_id)) {
			$this->addError('section_id', 'Exhibit page must be given a section');
		}
	}
	
	protected function getSection()
	{
		return $this->getTable('ExhibitSection')->find($this->section_id);
	}
	
	protected function _delete()
	{			
		foreach ($this->ExhibitPageEntry as $ip) {
			$ip->delete();
		}
	}
		
	protected function afterDelete()
	{
		$section = $this->Section;
		$section->reorderChildren();		
	}
		
	/**
	 * Page Form POST will look like:
	 *
	 * Text[1] = 'Text inserted <a href="foobar.com">With HTML</a>'
	 * Item[2] = 35		(integer ID)
	 * Item[3] = 64
	 * Text[3] = 'This is commentary for the Item with ID # 64' 
	 * 
	 * @return void
	 **/
	public function afterSaveForm($post)
	{			
		$textCount = count($post['Text']);
		$itemCount = count($post['Item']);
		$highCount = ($textCount > $itemCount) ? $textCount : $itemCount;	
		
		$entries = $this->ExhibitPageEntry;
		for ($i=1; $i <= $highCount; $i++) { 
			$ip = $entries[$i];
			if(!$ip) {
				$ip = new ExhibitPageEntry;
				$ip->page_id = $this->id;
			}
			$text = $post['Text'][$i];
			$item_id = $post['Item'][$i];
			$ip->text = (string) $text;
			$ip->item_id = (int) is_numeric($item_id) ? $item_id : null;
			$ip->order = (int) $i;
			$ip->forceSave();
		}
	}
}
?>
