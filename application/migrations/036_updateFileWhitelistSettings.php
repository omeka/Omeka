<?php
/**
 * Updates the default options for extension and MIME type whitelists for uploaded
 * files.
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2009
 **/
class updateFileWhitelistSettings extends Omeka_Db_Migration
{
    public function up()
    {
        $newFileExtensions = array('aac','aif','aiff','mp2','mpa');
        $fileExtensionList = get_option('file_extension_whitelist');
        if(!empty($fileExtensionList)) {
            $currentFileExtensions = explode(',', $fileExtensionList);
        } else {
            $currentFileExtensions = array();
        }
        foreach($newFileExtensions as $fileExtension) {
            if(!in_array($fileExtension, $currentFileExtensions)) {
                $currentFileExtensions[] = $fileExtension;
            }
        }
        
        $newFileExtensionsSettings = implode(',', $currentFileExtensions);
        set_option('file_extension_whitelist', $newFileExtensionsSettings);
        
        
        $newMimeTypes = array('application/ogg','application/x-ms-wmp','application/x-ogg','audio/aac','audio/aiff','audio/mid','audio/mp3','audio/mp4','audio/mpeg3','audio/x-aac','audio/x-aiff','audio/x-midi','audio/x-mp3','audio/x-mp4','audio/x-mpeg','audio/x-mpeg3','audio/x-mpegaudio','audio/x-ms-wax','audio/x-wav','audio/x-wma','image/icon','image/x-ms-bmp','video/mp4','video/msvideo','video/ogg','video/x-ms-wmv','video/x-msvideo');
        
        $mimeTypeList = get_option('file_mime_type_whitelist');
        
        if(!empty($mimeTypeList)) {
            $currentMimeTypes = explode(',', $mimeTypeList); 
        } else {
            $currentMimeTypes = array(); 
        }
                
        foreach($newMimeTypes as $mimeType) {
            if(!in_array($mimeType, $currentMimeTypes)) {
                $currentMimeTypes[] = $mimeType;
            }
        }
        
        $newMimeTypesSetting = implode(',', $currentMimeTypes);
        set_option('file_mime_type_whitelist', $newMimeTypesSetting);
    }
}
