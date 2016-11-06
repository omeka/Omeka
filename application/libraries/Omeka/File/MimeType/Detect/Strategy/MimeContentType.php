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
        $mimetype = mime_content_type($file);
        // Detect some common "plain text" via the extension. Most important ones
        // are json and xml.
        if ($mimetype == 'text/plain') {
            $extensions = array(
                'css' => 'text/css',
                'csv' => 'text/csv',
                'htm' => 'text/html',
                'html' => 'text/html',
                'json' => 'application/json',
                'marc' => 'application/marc',
                'md' => 'text/markdown',
                'rtf' => 'text/rtf',
                'tsv' => 'text/tab-separated-values',
                'xhtml' => 'application/xhtml+xml',
                'xml' => 'text/xml',
            );
            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (isset($extensions[$extension])) {
                $mimetype = $extensions[$extension];
            }
        }
        return $mimetype;
    }
}
