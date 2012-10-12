<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Consolidate mime types into one field.
 * 
 * @package Omeka\Db\Migration
 */
class consolidateMimeTypes extends Omeka_Db_Migration_AbstractMigration
{
    public function up()
    {
        // Change mime_browser to mime_type, the definitive MIME type.
        $sql = "ALTER TABLE `{$this->db->File}` CHANGE `mime_browser` `mime_type` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL";
        $this->db->query($sql);
        
        // Drop the unneeded mime_os.
        $sql = "ALTER TABLE `{$this->db->File}` DROP `mime_os`";
        $this->db->query($sql);
    }
}
