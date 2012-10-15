<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * @package Omeka\File\MimeType\Detect\Strategy
 */
class Omeka_File_MimeType_Detect_Strategy_GetId3 
    implements Omeka_File_MimeType_Detect_StrategyInterface
{
    public function detect($file)
    {
        if (!extension_loaded('exif')) {
            // The getid3 library requires the exif extension.
            return false;
        }
        require_once 'getid3/getid3.php';
        $id3 = new getID3;
        $id3->encoding = 'UTF-8';
        try {
            $id3->Analyze($file);
        } catch (getid3_exception $e) {
            return false;
        }
        if (isset($id3->info['mime_type'])) {
            return $id3->info['mime_type'];
        }
        return false;
    }
}
