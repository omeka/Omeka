<?php
/**
 * Remove Data Types.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2011
 */
class removeDataTypes extends Omeka_Db_Migration_AbstractMigration
{
    public function up()
    {
        $this->db->query("DROP TABLE`{$this->db->DataType}`");
        $this->db->query("ALTER TABLE `{$this->db->Element}` DROP `data_type_id`");
    }
}
