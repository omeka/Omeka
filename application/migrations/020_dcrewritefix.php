<?php
class DcRewriteFix extends Omeka_Db_Migration
{
    private $_et = array(
        array('name' => 'date', 'regular_expression' => '/^\-?[0-9]{1,9}(?:\-\b(?:0[1-9]|1[0-2])\b(?:\-\b(?:0[1-9]|[1-2][0-9]|3[0-1])\b)?)$/', 'description' => 'A date in format yyyy-mm-dd: 
    * No hyphen before a year indicates C.E.
    * A hyphen before a year indicates B.C.E.
    * The year must exist
    * Months are optional
    * The day is optional
    * The month must precede the day
    * The year must precede the month
    * The year must be between -999,999,999 and 999,999,999
    * The month must be between 01 and 12 (zerofill)
    * The day must be between 01 and 31 (zerofill)
There are some bugs in this regex:
    * A match on \"0\" (zero) results true (there is no year zero)'), 
        array('name' => 'integer', 'regular_expression' => '/^\-?[1-9]\d*$/', 'description' => 'Set of numbers consisting of the natural numbers including 0 (0, 1, 2, 3, ...) and their negatives (0, −1, −2, −3, ...).'), 
        array('name' => 'boolean', 'regular_expression' => '/^(?:true|false|off|on|1|0)$/i', 'description' => 'A primitive datatype having one of two values: true or false, off or on, 1 or 0.'), 
        array('name' => 'datetime', 'regular_expression' => '', 'description' => 'A date and time combination in the format: yyyy-mm-dd hh:mm:ss')
    );
    
    public function up()
    {
        $this->_updateElementSets();
        $this->_alterElementSets();
        $this->_insertElementTypes();
        $this->_alterElementTypes();
    }
    
    public function down(){}
    
    private function _updateElementSets()
    {
        $db = $this->db;
        $sql = "
        UPDATE `{$db->prefix}element_sets` 
        SET `name` = 'Omeka Legacy Item Elements' 
        WHERE `name` = 'Omeka Legacy Elements'";
        $db->exec($sql);
    }
    
    private function _alterElementSets()
    {
        $db = $this->db;
        $sql = "ALTER TABLE `{$db->prefix}element_sets` ADD UNIQUE (`name`)";
        $db->exec($sql);
    }
    
    private function _insertElementTypes()
    {
        $db = $this->db;
        $sql = "INSERT INTO `{$db->prefix}element_types` (
            `name`, 
            `description`, 
            `regular_expression`
        ) VALUES (?, ?, ?)";
        foreach ($this->_et as $elementType) {
            $name              = $elementType['name'];
            $description       = $elementType['description'];
            $regularExpression = $elementType['regular_expression'];
            $db->exec($sql, array($name, $description, $regularExpression));
        }
    }
    
    private function _alterElementTypes()
    {
        $db = $this->db;
        $sql = "ALTER TABLE `{$db->prefix}element_types` ADD UNIQUE (`name`)";
        $db->exec($sql);
    }
    
}
