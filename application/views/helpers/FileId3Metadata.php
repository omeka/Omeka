<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Helper used to retrieve file metadata for display.
 * 
 * @package Omeka\View\Helper
 */
class Omeka_View_Helper_FileId3Metadata extends Zend_View_Helper_Abstract
{   
    public function fileId3Metadata($file, $options) 
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
            if (is_array($value)) {
                $output .= '<li><span class="id3-property-name">' . $key . '</span>' . $this->_arrayToList($value) . '</li>';
            } else {
                $output .= '<li>';
                if (!is_int($key)) {
                    $output .= '<li><span class="id3-property-name">' . $key . '</span>: ';
                }
                $output .= (is_bool($value) ? ($value ? 'true' : 'false') : $value) . '</li>';
            }
        }
        $output .= '</ul>';
        return $output;
    }
}
