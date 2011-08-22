<?php 
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * Load the default configuration file for Omeka.
 *
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Omeka_Core_Resource_Config extends Zend_Application_Resource_ResourceAbstract
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
