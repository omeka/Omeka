<?php
/**
 * Adds a new mime type to the default list of allowed mime types.
 *
 * @package Omeka\Db\Migration
 * 
 */
class addNewMimeType extends Omeka_Db_Migration_AbstractMigration
{
    public function up()
    {
        $mimeType = 'image/vnd.adobe.photoshop';
        $options = get_option(Omeka_Validate_File_MimeType::WHITELIST_OPTION);

        if (strpos($options, $mimeType) === false ){
            $options .=  ',' . $mimeType;
            set_option(Omeka_Validate_File_MimeType::WHITELIST_OPTION, $options);
        }
    }
}
