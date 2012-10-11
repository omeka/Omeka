<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Convert the 'modified' column to a bigint to avoid the Y2K38 problem.
 * 
 * @package Omeka\Db\Migration
 */
class convertSessionsModifiedToBigInt extends Omeka_Db_Migration_AbstractMigration
{
    public function up()
    {
        $this->db->query(
            "ALTER TABLE {$this->db->Session} MODIFY `modified` bigint;"
        );
    }
}
