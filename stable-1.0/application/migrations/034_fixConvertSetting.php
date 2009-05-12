<?php 
/**
 * Migrate the 'path_to_convert' setting so that it no longer contains the 
 * security hole that allowed executing arbitrary commands.
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2009
 **/
class fixConvertSetting extends Omeka_Db_Migration
{
    /**
     * Change the 'path_to_convert' setting so that it only contains the 
     * path to the directory and not the full path to the convert binary.
     * 
     * See [3706] for details.
     * 
     * @return void
     **/
    public function up()
    {
        $pathToConvert = get_option('path_to_convert');
        
        // If we're not using ImageMagick, skip this processing.
        if (empty($pathToConvert)) {
            return;
        }

        // Only directories can be valid from now on, so everything else will
        // be fixed.        
        if (!is_dir($pathToConvert)) {
            $newConvertSetting = dirname(realpath($pathToConvert));
            set_option('path_to_convert', $newConvertSetting);
        }
    }
}
