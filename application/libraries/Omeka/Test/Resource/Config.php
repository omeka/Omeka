<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * Load the default config for the application, but *also* load the test config
 * into Zend_Registry.
 *
 * @internal Ideally this should operate so that only one config.ini is needed
 * for the entire test environment, instead of having the settings be spread 
 * across both.
 * 
 * @package Omeka
 * @copyright Center for History and New Media, 2007-2010
 **/
class Omeka_Test_Resource_Config extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * @var Omeka_Core_Resource_Autoloader
     */
    private $_coreResource;
    
    public function __construct($options = null)
    {
        $this->_coreResource = new Omeka_Core_Resource_Config($options);
    }
    
    public function init()
    {
        $mainConfig = $this->_coreResource->init();
        if (!Zend_Registry::isRegistered('test_config')) {
            //Config dependency
            $config = new Zend_Config_Ini(APP_DIR . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR.'config.ini', 'testing');
            Zend_Registry::set('test_config', $config);
        }
        return $mainConfig;
    }
}
