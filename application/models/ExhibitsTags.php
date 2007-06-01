<?php
require_once 'Exhibit.php';
/**
 * ExhibitsTags
 * @package: Omeka
 */
class ExhibitsTags extends Kea_JoinRecord
{
    public function setTableDefinition()
    {
		$this->hasColumn("exhibit_id", "integer",null,"notnull");
		$this->hasColumn("tag_id", "integer",null,"notnull");
		$this->hasColumn("user_id", "integer", null,"notnull");
		
		$this->unique(array('tag_id','user_id', 'exhibit_id'));
    }
    public function setUp()
    {
		$this->hasOne("User","ExhibitsTags.user_id");
		$this->hasOne("Exhibit","ExhibitsTags.exhibit_id");
		$this->hasOne("Tag","ExhibitsTags.tag_id");
    }
}

?>