<?php
require_once 'SectionPage.php';
/**
 * Section
 * @package: Omeka
 * @todo Have a function that re-orders the pages if the order gets fucked up or one gets deleted, etc.
 */
class Section extends Kea_Record
{
    public function setTableDefinition()
    {
		$this->setTableName('sections');
		$this->hasColumn("title", "string", 255,"notblank");
		$this->hasColumn("description", "string");
		$this->hasColumn("exhibit_id", "integer",null,"notnull");
		$this->hasColumn("section_order as order", "integer", null,"notnull");
    }
    public function setUp()
    {
		$this->hasOne('Exhibit', 'Section.exhibit_id');	
		$this->ownsMany('SectionPage as Pages', 'SectionPage.section_id');	
		$this->setAttribute(Doctrine::ATTR_COLL_KEY, 'section_order');
    }

	//Deleting a section must re-order the other sections
	public function delete()
	{
		$exhibit = $this->Exhibit;
		$retVal = parent::delete();
		
		$exhibit->reorderSections();
	}

	public function reorderPages()
	{
		$dql = "SELECT p.* FROM SectionPage p WHERE p.section_id = ? ORDER BY p.page_order ASC";
		$q = new Doctrine_Query;
		$q->parseQuery($dql);
		
		$pages = $q->execute(array($this->id));
		
		$i = 1;
		foreach ($pages as $key => $page) {
			$page->order = $i;
			$page->save();
			$i++;
		}
	}

	public function getPageCount()
	{
		$sql = "SELECT COUNT(*) FROM section_pages WHERE section_id = ?";
		$res = $this->getTable()->getConnection()->execute($sql,array($this->id));
		$count = $res->fetch();
		return $count[0];
	}
	
	public function getPage($order)
	{
		$dql = "SELECT p.* FROM SectionPage p WHERE p.order = ? AND p.section_id = ?";
		$q = new Doctrine_Query;
		$q->parseQuery($dql);
		return $q->execute(array($order,$this->id))->getFirst();
	}
}

?>