<?php
class updateDublinCoreDataTypes extends Omeka_Db_Migration
{
    public function up()
    {
        $db = get_db();
        $sql = "
        UPDATE `{$db->prefix}elements` e 
        SET e.`data_type_id` = (
            SELECT dt.`id` 
            FROM `{$db->prefix}data_types` dt 
            WHERE dt.`name` = 'Tiny Text'
        ) 
        WHERE e.`name` = 'Date'
        AND e.`element_set_id` = (
            SELECT es.`id` 
            FROM `{$db->prefix}element_sets` es 
            WHERE es.`name` = 'Dublin Core'
        )";
        $db->query($sql);
    }
}