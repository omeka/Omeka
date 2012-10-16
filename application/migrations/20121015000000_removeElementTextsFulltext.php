<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Change the FULLTEXT index on element_texts to a normal index.
 * 
 * @package Omeka\Db\Migration
 */
class removeElementTextsFulltext extends Omeka_Db_Migration_AbstractMigration
{
    public function up()
    {
        $sql = "ALTER TABLE `{$this->db->ElementText}` DROP INDEX `text`";
        $this->db->query($sql);
        $sql = "ALTER TABLE `{$this->db->ElementText}` ADD INDEX `text` (`text`(20))";
        $this->db->query($sql);
    }
}
