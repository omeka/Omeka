<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2011
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Convert the 'modified' column to a bigint to avoid the Y2K38 problem.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2011
 */
class convertSessionsModifiedToBigInt extends Omeka_Db_Migration
{
    public function up()
    {
        $this->db->query(
            "ALTER TABLE {$this->db->Session} MODIFY `modified` bigint;"
        );
    }
}
