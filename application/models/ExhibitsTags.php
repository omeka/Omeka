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
		$this->option('type', 'MYISAM');
		$this->hasColumn("exhibit_id", "integer",null,"notnull");
		$this->hasColumn("tag_id", "integer",null,"notnull");
		$this->hasColumn("user_id", "integer", null,"notnull");
		
		//The join for this table should just be for tags and exhibits (without users)
		$this->unique(array('tag_id','exhibit_id'));
    }
    public function setUp()
    {
		$this->hasOne("User","ExhibitsTags.user_id");
		$this->hasOne("Exhibit","ExhibitsTags.exhibit_id");
		$this->hasOne("Tag","ExhibitsTags.tag_id");
    }
}

?>