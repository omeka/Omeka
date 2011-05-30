<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2011
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @subpackage Migrations
 */

/**
 * Removes the stored "header check" option for file validation.
 *
 * @package Omeka
 * @subpackage Migrations
 */
class removeHeaderCheckOption extends Omeka_Db_Migration
{
    public function up()
    {
        $db = $this->getDb();

        $db->query("DELETE FROM `{$db->Option}` WHERE `name` = 'enable_header_check_for_file_mime_types'");
    }

    public function down()
    {
        throw new RuntimeException("Cannot reverse this migration.");
    }
}
