<?php
class DcRewriteFix extends Omeka_Db_Migration
{
    public function up()
    {
        $db = $this->db;
        $sql = "
        UPDATE `{$db->prefix}element_sets` 
        SET `name` = 'Omeka Legacy Item Elements' 
        WHERE `name` = 'Omeka Legacy Elements';";
        $db->execBlock($sql);
    }
    public function down(){}
}
