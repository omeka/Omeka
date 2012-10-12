<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Removes the stored "header check" option for file validation.
 * 
 * @package Omeka\Db\Migration
 */
class removeHeaderCheckOption extends Omeka_Db_Migration_AbstractMigration
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
