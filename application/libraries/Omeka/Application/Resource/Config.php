<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Load the default configuration file for Omeka.
 * 
 * @package Omeka\Application\Resource
 */
class Omeka_Application_Resource_Config extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * @return Zend_Config_Ini
     */
    public function init()
    {        
        $configFile = CONFIG_DIR . '/config.ini';
        
        if (!file_exists($configFile)) {
            throw new Zend_Config_Exception('Your Omeka configuration file is missing.');
        }
        
        return new Zend_Config_Ini($configFile, 'site');     
    }
}
