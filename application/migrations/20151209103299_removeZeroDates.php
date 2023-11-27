<?php
/**
 * Omeka
 *
 * @copyright Copyright 2007-2017 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Remove any existing zero dates.
 *
 * Note: this migration was added "out of order" during the 2.5.1 cycle,
 * its date is chosen to make it run just before the removeZeroDateDefaults
 * migration if both need to be done.
 *
 * @package Omeka\Db\Migration
 */
class removeZeroDates extends Omeka_Db_Migration_AbstractMigration
{
    public function up()
    {
        // MySQL 8 is even stricter on zero dates, so set the mode to nothing
        // to remove NO_ZERO_DATE, then set back to what we had after
        $stmt = $this->db->query('SELECT @@sql_mode');
        $origMode = $stmt->fetchColumn();
        $this->db->query("SET SESSION sql_mode=''");

        $this->db->query("UPDATE IGNORE {$this->db->Collection} SET `added` = '2000-01-01 00:00:00' WHERE `added` = '0000-00-00 00:00:00'");
        $this->db->query("UPDATE IGNORE {$this->db->Collection} SET `modified` = '2000-01-01 00:00:00' WHERE `modified` = '0000-00-00 00:00:00'");
        $this->db->query("UPDATE IGNORE {$this->db->File} SET `added` = '2000-01-01 00:00:00' WHERE `added` = '0000-00-00 00:00:00'");
        $this->db->query("UPDATE IGNORE {$this->db->Item} SET `added` = '2000-01-01 00:00:00' WHERE `added` = '0000-00-00 00:00:00'");
        $this->db->query("UPDATE IGNORE {$this->db->Process} SET `started` = '2000-01-01 00:00:00' WHERE `started` = '0000-00-00 00:00:00'");
        $this->db->query("UPDATE IGNORE {$this->db->Process} SET `stopped` = '2000-01-01 00:00:00' WHERE `stopped` = '0000-00-00 00:00:00'");

        $this->db->query('SET SESSION sql_mode=' . $this->db->quote($origMode));
    }
}
