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
class Omeka_File_MimeType_Detect_Strategy_Browser 
    implements Omeka_File_MimeType_Detect_StrategyInterface
{
    public function detect($file)
    {
        if (empty($_FILES)) {
            // No files have been uploaded.
            return false;
        }
        // If an uploaded file's tmp_name matches the passed file, return the 
        // MIME type set by the browser.
        foreach ($_FILES['file']['tmp_name'] as $key => $tmpName) {
            if ($file == $tmpName) {
                return $_FILES['file']['type'][$key];
            }
        }
        // No uploaded files match the passed file.
        return false;
    }
}
