<?php
/**
 * Add the default options for extension and MIME type whitelists for uploaded
 * files.
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2009
 **/
class addFileWhitelistSettings extends Omeka_Db_Migration
{
    public function up()
    {
        set_option('file_extension_whitelist', Omeka_Validate_File_Extension::DEFAULT_WHITELIST);
        set_option('file_mime_type_whitelist', Omeka_Validate_File_MimeType::DEFAULT_WHITELIST);
        set_option('disable_default_file_validation', 0);
    }
}
