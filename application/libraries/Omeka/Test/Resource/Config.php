<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * Load the default config for the application, but *also* load the test config
 * into Zend_Registry.
 *
 * @internal Ideally this should operate so that only one config.ini is needed
 * for the entire test environment, instead of having the settings be spread 
 * across both.
 * 
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Omeka_Test_Resource_Config extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * @var Omeka_Core_Resource_Config
     */
    private $_coreResource;
    
    /**
     * @param array $options Options for resource.
     */
    public function __construct($options = null)
    {
        $this->_coreResource = new Omeka_Core_Resource_Config($options);
    }
    
    /**
     * Load both config files.
     *
     * @return Zend_Config
     */
    public function init()
    {
        $mainConfig = $this->_coreResource->init();
        $testConfigPath = APP_DIR . '/tests/config.ini';
        $config = new Zend_Config_Ini($testConfigPath);
        if (!Zend_Registry::isRegistered('test_config')) {
            Zend_Registry::set('test_config', $config->testing);
        }

        // Merging the configs allows us to override settings only for tests.
        if ($config->site instanceof Zend_Config) {
            $mainCopy = new Zend_Config($mainConfig->toArray(), true);
            $mainCopy->merge($config->site);
            $mainCopy->setReadOnly(true);
            $mainConfig = $mainCopy;
        }
        return $mainConfig;
    }
}
