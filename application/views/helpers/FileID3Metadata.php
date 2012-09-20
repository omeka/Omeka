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
class Omeka_View_Helper_FileID3Metadata extends Zend_View_Helper_Abstract
{   
    public function fileID3Metadata($file, $options) 
    {
        $output = '';
        if ($file->metadata) {
            $metadataArray = json_decode($file->metadata, true);
            ob_start();
            echo $this->_arrayToList($metadataArray);
            $output .= ob_get_contents();
            ob_end_clean();
        }
        return $output;
    }
    
    private function _arrayToList($array) 
    {
        $output = '<ul>';
        foreach ($array as $key => $value)
        {
            if (is_array($value))
            {
                $output .= '<li><span class="id3-property-name">' . $key . '</span>' . $this->_arrayToList($value) . '</li>';
            } else {
                $output .= '<li><span class="id3-property-name">' . $key . '</span> => ' . $value . '</li>';
            }
        }
        $output .= '</ul>';
        return $output;
    }
}