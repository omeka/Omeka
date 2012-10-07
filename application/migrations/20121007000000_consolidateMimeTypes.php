<?php
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
