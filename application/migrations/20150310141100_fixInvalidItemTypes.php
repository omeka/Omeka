<?php
/**
 * Omeka
 *
 * @copyright Copyright 2007-2015 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * @package Omeka\Db\Migration
 */
class fixInvalidItemTypes extends Omeka_Db_Migration_AbstractMigration
{
    public function up()
    {
        $sql = <<<SQL
UPDATE `{$this->db->Item}` i
LEFT JOIN `{$this->db->ItemType}` it
ON i.item_type_id = it.id
SET i.item_type_id = NULL
WHERE it.id IS NULL AND i.item_type_id IS NOT NULL;
SQL;
        $this->db->query($sql);
    }
}
