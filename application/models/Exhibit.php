<?php
require_once 'Section.php';
require_once 'Tag.php';
require_once 'Taggable.php';
require_once 'ExhibitsTags.php';
/**
 * Exhibit
 * @package: Omeka
 */
class Exhibit extends Kea_Record
{
	protected $error_messages = array(	
		'slug' => array('notblank' => 'Exhibit must be given a valid slug.'),
		'title' => array('notblank' => 'Exhibit must be given a title.')		
				);
	
	protected $_taggable;
	
    public function setTableDefinition()
    {
		$this->option('type', 'MYISAM');
		$this->setTableName('exhibits');
		$this->hasColumn("title", "string", 255, "notblank");
		$this->hasColumn("description", "string");
		$this->hasColumn("credits", "string");
		$this->hasColumn("featured", "boolean");
		
		//Display info (not archival)
		$this->hasColumn("theme","string",30);
		$this->hasColumn("slug", "string", 30,"unique|notblank");
    }
    public function setUp()
    {
		$this->ownsMany('Section as Sections', 'Section.exhibit_id');
		$this->ownsMany('ExhibitsTags', 'ExhibitsTags.exhibit_id');
		$this->hasMany('Tag as Tags','ExhibitsTags.tag_id');
		
    }
	
	public function construct()
	{
		$this->_strategies[] = new Taggable($this);
	}
	
	public function reorderSections()
	{
		$this->loadSections();
		$i = 1;
		foreach ($this->Sections as $key => $section) {
			$section->order = $i;
			$section->save();
			$i++;
		}
		
		return $this;
	}
	
	public function loadSections()
	{
		$dql = "SELECT s.* FROM Section s WHERE s.exhibit_id = {$this->id} ORDER BY s.section_order ASC";
		$q = new Doctrine_Query;
		$sections = $q->parseQuery($dql)->execute();
		$this->Sections = $sections;
	}
	
	public function getSection($order)
	{
		$dql = "SELECT s.* FROM Section s LEFT JOIN s.Pages p WHERE s.order = ? AND s.exhibit_id = ?";
		$q = new Doctrine_Query;
		$q->parseQuery($dql);
		return $q->execute(array($order,$this->id))->getFirst();
	}
	
	/**
	 * The number of sections in the exhibit
	 *
	 * @return int
	 **/
	public function getSectionCount()
	{
		$sql = "SELECT COUNT(*) FROM sections WHERE exhibit_id = ?";
		$res = $this->getTable()->getConnection()->execute($sql,array($this->id));
		$count = $res->fetch();
		return $count[0];
	}
}

?>