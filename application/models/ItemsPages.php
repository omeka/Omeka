<?php
/**
 * ItemsPages
 * @package: Omeka
 */
class ItemsPages extends Kea_Record
{
    public function setTableDefinition()
    {
		$this->option('type', 'MYISAM');
		$this->setTableName('items_section_pages');
		$this->hasColumn("item_id", "integer", null);
		$this->hasColumn("page_id", "integer", null,"notnull");
		$this->hasColumn("text", "string");
		$this->hasColumn("entry_order as order", "integer", null,"notnull");
		$this->setAttribute(Doctrine::ATTR_COLL_KEY, 'entry_order');
    }
    public function setUp()
    {
		$this->hasOne('Item', 'ItemsPages.item_id');
		$this->hasOne('SectionPage as Page', 'ItemsPages.page_id');
    }
}

?>