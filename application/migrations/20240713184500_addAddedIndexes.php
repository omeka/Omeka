<?php
/**
 * Omeka
 *
 * @copyright Copyright 2007-2020 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Add indexes for added, the default sort column for items and collections
 *
 * @package Omeka\Db\Migration
 */
class addAddedIndexes extends Omeka_Db_Migration_AbstractMigration
{
    public function up()
    {
        $this->db->query("ALTER TABLE {$this->db->Item} ADD INDEX `added` (`added`)");
        $this->db->query("ALTER TABLE {$this->db->Collection} ADD INDEX `added` (`added`)");
    }
}
