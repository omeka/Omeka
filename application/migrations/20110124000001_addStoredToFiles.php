<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Add stored flag to files table.
 * 
 * @package Omeka\Db\Migration
 */
class addStoredToFiles extends Omeka_Db_Migration_AbstractMigration
{
    public function up()
    {
        $db = $this->getDb();

        $db->query("ALTER TABLE `{$db->File}` ADD `stored` TINYINT(1) NOT NULL DEFAULT '0'");
        // All files present at upgrade are already "stored."
        $db->query("UPDATE `{$db->File}` SET `stored` = '1'");
    }

    public function down()
    {
        $db = $this->getDb();

        $db->query("ALTER TABLE `{$db->File}` DROP `stored`");
    }
}
