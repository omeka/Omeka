<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2012
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @subpackage Omeka_View_Helper
 */

/**
 * Helper used to retrieve file metadata for display.
 *
 * @package Omeka
 * @subpackage Omeka_View_Helper
 */
class Omeka_View_Helper_FileMetadataList extends Omeka_View_Helper_RecordMetadataList
{
    public function fileMetadataList($file, $options = array()) 
    {
        $output = '<div class="file-metadata"';
        if ($file->exists()) {
            $output .= ' id="file-metadata-' . $file->id . '"';
        }
        $output .= '>';
        
        $output .= $this->recordMetadataList($file, $options);
                
        $output .= '</div>';
        return $output;
    }
}