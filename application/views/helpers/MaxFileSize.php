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
    const MEGABYTE_BYTES = 1048576;

    /**
     * @var string
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
        $postMaxSize = $this->_parseSize(ini_get('post_max_size'));
        $uploadMaxFilesize = $this->_parseSize(ini_get('upload_max_filesize'));
        $maxFileSize = min($postMaxSize, $uploadMaxFilesize);

        // Compare to file.maxSize in config.ini.
        $config = Zend_Registry::get('bootstrap')->getResource('Config');
        if (isset($config->upload->maxFileSize)) {
            $configMaxSize = $this->_parseSize($config->upload->maxFileSize);
            if ($configMaxSize) {
                $maxFileSize = min($maxFileSize, $configMaxSize);
            }
        }

        $megabytes = round($maxFileSize / self::MEGABYTE_BYTES);
        $this->_maxFileSize = __('%s MB', $megabytes);
    }

    /**
     * Return the maximum file size.
     * 
     * @return string
     */
    public function maxFileSize()
    {
        return $this->_maxFileSize;
    }

    /**
     * Get the size in bytes represented by the given php ini config string
     *
     * @param string $sizeString
     * @return int Size in bytes
     */
    protected function _parseSize($sizeString)
    {
        $value = intval($sizeString);
        $lastChar = substr($sizeString, -1);
        // Note: these cases fall through purposely
        switch ($lastChar) {
            case 'g':
            case 'G':
                $value *= 1024;
            case 'm':
            case 'M':
                $value *= 1024;
            case 'k':
            case 'K':
                $value *= 1024;
        }

        return $value;
    }
}
