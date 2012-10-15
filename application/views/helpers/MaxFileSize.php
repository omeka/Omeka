<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * @package Omeka\View\Helper
 */
class Omeka_View_Helper_MaxFileSize extends Zend_View_Helper_Abstract
{
    /**
     * @var Zend_Measure_Binary
     */
    protected $_maxFileSize;
    
    /**
     * Set the maximum file size.
     * 
     * The maximum file size is the least of the configurations that affect 
     * maximum file size.
     */
    public function __construct()
    {
        // Compare core php.ini directives that affect maximum file size.
        $postMaxSize = $this->_getSizeMeasure(ini_get('post_max_size'));
        $uploadMaxFilesize = $this->_getSizeMeasure(ini_get('upload_max_filesize'));
        $maxFileSize = (0 > $postMaxSize->compare($uploadMaxFilesize)) 
                     ? $postMaxSize : $uploadMaxFilesize;
        
        // Compare to file.maxSize in config.ini.
        $config = Zend_Registry::get('bootstrap')->getResource('Config');
        if (isset($config->upload->maxFileSize)) {
            $configMaxSize = $this->_getSizeMeasure($config->upload->maxFileSize);
            if ($configMaxSize) {
                $maxFileSize = (0 > $maxFileSize->compare($configMaxSize)) 
                             ? $maxFileSize : $configMaxSize;
            }
        }
        
        $this->_maxFileSize = $maxFileSize;
    }
    
    /**
     * Return the maximum file size.
     * 
     * @return Zend_Measure_Binary
     */
    public function maxFileSize()
    {
        return $this->_maxFileSize;
    }
    
    /**
     * Get the binary measurements for file size.
     * 
     * @param string|int $size
     * @return Zend_Measure_Binary
     */
    protected function _getSizeMeasure($size)
    {
        // Check for a valid size.
        if (!preg_match('/(\d+)([KMG]?)/i', $size, $matches)) {
            return false;
        }
        
        // The default size type is bytes.
        $sizeType = Zend_Measure_Binary::BYTE;
        
        // Check for larger size types.
        $sizeTypes = array(
            'K' => Zend_Measure_Binary::KILOBYTE,
            'M' => Zend_Measure_Binary::MEGABYTE,
            'G' => Zend_Measure_Binary::GIGABYTE,
        );
        if (array_key_exists($matches[2], $sizeTypes)) {
            $sizeType = $sizeTypes[$matches[2]];
        }
        
        return new Zend_Measure_Binary($matches[1], $sizeType);
    }
}
