<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * @package Omeka\Db\Migration
 */
class setNonextantReferencesToNull extends Omeka_Db_Migration_AbstractMigration
{
    public function up()
    {
        $sql = <<<SQL
UPDATE `{$this->db->Item}` i
LEFT JOIN `{$this->db->Collection}` c
ON i.collection_id = c.id 
SET i.collection_id = NULL 
WHERE c.id IS NULL;
SQL;
        $this->db->query($sql);
        
        $sql = <<<SQL
UPDATE `{$this->db->Item}` i 
LEFT JOIN `{$this->db->ItemType}` it
ON i.item_type_id = it.id
SET i.item_type_id = NULL
WHERE it.id IS NULL;
SQL;
        $this->db->query($sql);
    }
}
