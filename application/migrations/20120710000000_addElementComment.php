<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Add element comment to schema.
 * 
 * @package Omeka\Db\Migration
 */
class addElementComment extends Omeka_Db_Migration_AbstractMigration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE {$this->db->Element} 
ADD comment TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL
SQL;
        $this->db->query($sql);
    }
}
