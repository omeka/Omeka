<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Encapsulates testing functionality for Omeka plugins.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Omeka_Test_Helper_Plugin
{
    /**
     * Install and initialize a plugin.
     *
     * Note: Normally used in the setUp() method of plugin tests.
     *
     * @param string $pluginName
     */
    public function setUp($pluginName)
    {
        $this->install($pluginName);
        $this->initialize($pluginName);
    }

    /**
     * Install a plugin
     *
     * @param string $pluginName The name of the plugin to install.
     * @return Plugin
     */
    public function install($pluginName)
    {
        if (!($plugin = $this->pluginLoader->getPlugin($pluginName))) {
            $plugin = new Plugin;
            $plugin->name = $pluginName;
        }

        $this->pluginIniReader->load($plugin);

        $pluginInstaller = new Omeka_Plugin_Installer($this->pluginBroker, $this->pluginLoader);
        $pluginInstaller->install($plugin);

        return $plugin;
    }

    /**
     * Initializes the plugin hooks and filters fired in the core resources for a plugin
     * Note: Normally used in the setUp() function of the subclasses that test plugins.
     *
     * @internal This is a workaround for the fact that Omeka will already be
     * bootstrapped when plugins are installed by the tests.  This manually
     * re-runs the hooks and filters normally run during bootstrap.
     *
     * @param string $pluginName If omitted, initialize all installed plugins.
     * @return void
     */
    public function initialize($pluginName = null)
    {
        $this->_defineResponseContexts();

        $this->pluginBroker->callHook('initialize', array(), $pluginName);
        $this->pluginBroker->callHook('define_acl', array(Omeka_Context::getInstance()->acl), $pluginName);
        $this->pluginBroker->callHook('define_routes', array(Omeka_Context::getInstance()->router), $pluginName);
    }

    /**
     * Run the define_response_contexts filter.
     *
     * @return void
     */
    protected function _defineResponseContexts()
    {
        Zend_Controller_Action_HelperBroker::removeHelper('contextSwitch');
        Zend_Controller_Action_HelperBroker::addHelper(new Omeka_Controller_Action_Helper_ContextSwitch);
        $contexts = Zend_Controller_Action_HelperBroker::getStaticHelper('contextSwitch');

        $contexts->setContextParam('output');

        $contextArray = Omeka_Core_Resource_Frontcontroller::getDefaultResponseContexts();
        $contextArray = $this->pluginBroker->applyFilters('define_response_contexts', $contextArray);

        $contexts->addContexts($contextArray);
    }

    /**
     * Set the plugin loader for the helper to use.
     *
     * @param Omeka_Plugin_Loader $pluginLoader
     */
    public function setPluginLoader($pluginLoader)
    {
        $this->pluginLoader = $pluginLoader;
    }

    /**
     * Set the plugin INI reader for the helper to use.
     *
     * @param Omeka_Plugin_Ini $pluginIniReader
     */
    public function setPluginIniReader($pluginIniReader)
    {
        $this->pluginIniReader = $pluginIniReader;
    }

    /**
     * Set the plugin broker for the helper to use.
     *
     * @param Omeka_Plugin_Broker $pluginBroker
     */
    public function setPluginBroker($pluginBroker)
    {
        $this->pluginBroker = $pluginBroker;
    }

    /**
     * Set the ACL for the helper to use.
     *
     * @param Omeka_Acl $acl
     */
    public function setAcl($acl)
    {
        $this->acl = $acl;
    }

    /**
     * Set the router for the helper to use.
     *
     * @param Zend_Controller_Router_Interface $router
     */
    public function setRouter($router)
    {
        $this->router = $router;
    }

    /**
     * Lazy-loading for helper properties.
     *
     * When a property is not set, attempts to load a default through standard
     * Omeka global state.  If this state is unavailable or undesireable,
     * use the set*() methods before calling any of the other public methods
     * of this class.
     *
     * @param string Name of property
     * @return mixed
     */
    public function __get($name)
    {
        switch ($name) {
            case 'pluginLoader':
                return $this->pluginLoader = Zend_Registry::get('pluginloader');
            case 'pluginIniReader':
                return $this->pluginIniReader = Zend_Registry::get('plugin_ini_reader');
            case 'pluginBroker':
                return $this->pluginBroker = Omeka_Context::getInstance()->pluginbroker;
            case 'acl':
                return $this->acl = Omeka_Context::getInstance()->acl;
            case 'router':
                return $this->router = Omeka_Context::getInstance()->router;
            default:
                return null;
        }
    }
}
