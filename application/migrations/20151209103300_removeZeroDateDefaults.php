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
class removeZeroDateDefaults extends Omeka_Db_Migration_AbstractMigration
{
    public function up()
    {
        $this->db->query("ALTER TABLE {$this->db->Collection} ALTER `added` SET DEFAULT '2000-01-01 00:00:00', ALTER `modified` SET DEFAULT '2000-01-01 00:00:00'");
        $this->db->query("ALTER TABLE {$this->db->File} ALTER `added` SET DEFAULT '2000-01-01 00:00:00'");
        $this->db->query("ALTER TABLE {$this->db->Item} ALTER `added` SET DEFAULT '2000-01-01 00:00:00'");
        $this->db->query("ALTER TABLE {$this->db->Process} ALTER `started` SET DEFAULT '2000-01-01 00:00:00', MODIFY `stopped` TIMESTAMP NULL DEFAULT NULL");
    }
}
