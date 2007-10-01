<?php
/**
 * Exhibit Page
 * @package: Omeka
 */
class SectionPage extends Omeka_Record
{
	public function setTableDefinition()
    {
		$this->option('type', 'MYISAM');
		$this->setTableName('section_pages');
		$this->hasColumn("section_id", "integer",null,"notnull");
	
		$this->hasColumn("layout", "string",255,"notblank");
		$this->hasColumn("page_order as order", "integer",null,"notnull");
		$this->setAttribute(Doctrine::ATTR_COLL_KEY, 'page_order');
	}
	
	public function setUp()
    {
		$this->hasOne("Section", "SectionPage.section_id");
		$this->ownsMany("ItemsPages", "ItemsPages.page_id");
	//	$this->hasMany("Item as Items", "ItemsPages.item_id");
	}
	
	public function delete()
	{
		$section = $this->Section;
		
		$id = (int) $this->id;
		
		fire_plugin_hook('delete_exhibit_page', $this);
		
		//Delete thyself and all thine dependencies
		$delete = "DELETE items_section_pages, section_pages FROM section_pages
		LEFT JOIN items_section_pages ON items_section_pages.page_id = section_pages.id
		WHERE section_pages.id = $id;";
		
		$this->execute($delete);
				
		$section->reorderPages();
	}
	
	public function getItemCount()
	{
		$sql = "SELECT COUNT(*) FROM ".$this->getTableName('ItemsPages')." p WHERE p.page_id = ? AND p.item_id IS NOT NULL";
		return $this->execute($sql, array($this->id), true);
	}
	
	public function getTextCount()
	{
		$sql = "SELECT COUNT(*) FROM ".$this->getTableName('ItemsPages')." p WHERE p.page_id = ? AND p.text IS NOT NULL";
		return $this->execute($sql, array($this->id), true);
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
	public function preCommitForm(&$post, $options)
	{			
		$textCount = count($post['Text']);
		$itemCount = count($post['Item']);
		$highCount = ($textCount > $itemCount) ? $textCount : $itemCount;	
		
		for ($i=1; $i <= $highCount; $i++) { 
			$ip = $this->ItemsPages[$i];
			$text = $post['Text'][$i];
			$item_id = $post['Item'][$i];
			$ip->text = (string) $ip->strip($text);
			$ip->item_id = (int) is_numeric($item_id) ? $item_id : null;
			$ip->order = (int) $i;
		}
	}
	
	/**
	 * Retrieve the text at a given order on the page
	 *
	 * @return string|null	
	 **/
	public function Text($order) {
		
		$sql = "SELECT text FROM items_section_pages p WHERE p.page_id = ? AND p.entry_order = ? LIMIT 1";
		$res = $this->execute($sql,array($this->id, $order), true);
		return $res;
	}
	
/*
		public function Item($order) {
		$dql = "SELECT i.* FROM Item i INNER JOIN i.ItemsPages ip INNER JOIN ip.Page p WHERE p.order = ? AND p.id = ? LIMIT 1";
		$item = $this->executeDql($dql, array($order, $this->id));
		Zend_Debug::dump( get_class($item) );exit;
	}
*/	
	
	/**
	 * Retrieve the ID of the Item at a given order
	 *
	 * @return int|null
	 **/
	public function ItemId($order) {
		$sql = "SELECT item_id FROM items_section_pages p WHERE p.page_id = ? AND p.entry_order = ? LIMIT 1";
		$res = $this->execute($sql,array($this->id, $order), true);
		return $res;
	}
}
?>
