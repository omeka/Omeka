<?php
require_once 'SectionPage.php';
/**
 * Section
 * @package: Omeka
 */
class Section extends Kea_Record
{
    public function setTableDefinition()
    {
		$this->option('type', 'MYISAM');
		$this->setTableName('sections');
		$this->hasColumn("title", "string", 255,"notblank");
		$this->hasColumn("description", "string");
		$this->hasColumn("exhibit_id", "integer",null,"notnull");
		$this->hasColumn("section_order as order", "integer", null,"notnull");
		$this->hasColumn('slug', 'string', null, "notblank");
//		$this->index('exhibit_section', array('fields'=>array('exhibit_id', 'section_order'), 'type'=>'unique'));
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
	
	/**
	 * @since 7-24-07 Now with slugs!
	 *
	 **/
	protected function preCommitForm(&$post, $options)
	{
		//We need to make a slug for this section
		$slugFodder = !empty($post['slug']) ? $post['slug'] : $post['title'];
		
		$post['slug'] = $this->Exhibit->generateSlug($slugFodder);
	}

	protected function postCommitForm($post, $options)
	{
		//Change the order of the pages
		foreach ($post['Pages'] as $key => $page) {
			$this->Pages[$key]->order = $page['order'];
		}
		$this->Pages->save();
		$this->reorderPages();
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
	
	public function loadPages()
	{
		$dql = "SELECT p.* FROM SectionPage p WHERE p.section_id = {$this->id} ORDER BY p.page_order ASC";
		$this->Pages = $this->executeDql($dql);
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
		return $this->executeDql($dql, array($order,$this->id), true);
	}
}

?>