<?php
/**
 * Add the default options for extension and MIME type blacklists for uploaded
 * files.
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2009
 **/
class addFileBlacklistSetting extends Omeka_Db_Migration
{
    public function up()
    {
        set_option('file_extension_blacklist', FILE_EXTENSION_BLACKLIST);
        set_option('file_mime_type_blacklist', FILE_MIME_TYPE_BLACKLIST);
    }
}
