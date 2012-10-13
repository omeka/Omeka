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
class Omeka_File_MimeType_Detect_Strategy_MimeContentType 
    implements Omeka_File_MimeType_Detect_StrategyInterface
{
    public function detect($file)
    {
        if (!function_exists('mime_content_type')) {
            // The mime_content_type has been deprecated and will be removed in 
            // future versions of PHP.
            return false;
        }
        return mime_content_type($file);
    }
}
