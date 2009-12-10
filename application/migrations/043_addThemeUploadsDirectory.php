<?php
/**
 * Adds the theme uploads directory
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2009
 **/
class addThemeUploadsDirectory extends Omeka_Db_Migration
{
    public function up()
    {   
        if (!is_dir(THEME_UPLOADS_DIR)) {
            if (is_writable(ARCHIVE_DIR)) {
                mkdir(THEME_UPLOADS_DIR);
            } else {
                throw new Exception('Unable to create ' . THEME_UPLOADS_DIR . ' because ' . ARCHIVE_DIR . ' must be writable.');
            }
        }        
    }
}
