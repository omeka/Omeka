<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Customized subclass of Zend Framework's View class.
 *
 * This adds the correct script paths for themes and plugins so that controllers 
 * can render the appropriate scripts.
 *
 * This will also inject directly into the view scripts all variables that have 
 * been assigned to the view, so that theme writers can access them as $item 
 * instead of $this->item, for example.
 * 
 * @package Omeka\View
 */
class Omeka_View extends Zend_View_Abstract
{    
    const THEME_HOOK_NAMESPACE = '__global__';

    /**
     * Maintains a key => value pairing corresponding to hard path => web path 
     * for possible assets for Omeka views.
     *
     * @var array
     */
    protected $_asset_paths = array();
    
    /**
     * Flag indicated whether theme custom scripts have been loaded.
     *
     * @var boolean
     */
    private $_customScriptsLoaded = false;
    
    /**
     * @param array $config View configuration.
     */
    public function __construct($config = array())
    {
        parent::__construct($config);         
        
        // Setting the XHTML1_STRICT doctype fixes validation errors for ZF's form elements
        $this->doctype()->setDoctype('HTML5');
        
        $this->addHelperPath(VIEW_HELPERS_DIR, 'Omeka_View_Helper');

        try {
            $mvc = Zend_Registry::get('plugin_mvc');
            foreach ($mvc->getHelpersDirs() as $pluginDirName => $dir) {
                $this->addHelperPath($dir, "{$pluginDirName}_View_Helper"); 
            }
        } catch (Zend_Exception $e) {
            // no plugins or MVC component, so we can't add helper paths
        }
    }
    
    /**
     * Get the currently-configured asset paths.
     *
     * @return array
     */
    public function getAssetPaths()
    {
        return $this->_asset_paths;
    }
    
    /**
     * Add an asset path to the view.
     *
     * @param string $physical Local filesystem path.
     * @param string $web URL path.
     * @return void
     */
    public function addAssetPath($physical, $web)
    {
        array_unshift($this->_asset_paths, array($physical, $web));
    }
    
    /**
     * Remove the existing asset paths and set a single new one.
     * 
     * @param string $physical Local filesystem path.
     * @param string $web URL path.
     * @return void 
     */
    public function setAssetPath($physical, $web)
    {
        $this->_asset_paths = array();
        $this->_asset_paths[] = array($physical, $web);
    }
        
    /**
     * Allow for variables set to the view object
     * to be referenced in the view script by their actual name.
     *
     * Also allows access to theme helpers.
     * 
     * For example, in a controller you might do something like:
     * $view->assign('themes', $themes);
     * Normally in the view you would then reference $themes through:
     * $this->themes;
     * 
     * Now you can reference it simply by using:
     * $themes;
     *
     * @return void
     */
    public function _run() {
        $this->_loadCustomThemeScripts();
        $vars = $this->getVars();
        extract($vars);
        include func_get_arg(0);
    }
    
    /**
     * Look for a 'custom.php' script in all script paths and include the file if it exists.
     * 
     * @internal This must 'include' (as opposed to 'require_once') the script because
     * it typically contains executable code that modifies global state.  These
     * scripts need to be loaded only once per request, but multiple times in
     * the test environment.  Hence the flag for making sure that it runs only
     * once per View instance.
     * @return void
     */
    private function _loadCustomThemeScripts()
    {
        if ($this->_customScriptsLoaded) {
            return;
        }

        $pluginBroker = get_plugin_broker();
        if ($pluginBroker) {
            $tmpPluginDir = $pluginBroker->getCurrentPluginDirName();
            $newPluginDir = $pluginBroker->setCurrentPluginDirName(
                self::THEME_HOOK_NAMESPACE);
        }
        foreach ($this->getScriptPaths() as $path) {
            $customScriptPath = $path . 'custom.php';
            if (file_exists($customScriptPath)) {
                include $customScriptPath;
            }
        }
        if ($pluginBroker) {
            $pluginBroker->setCurrentPluginDirName($tmpPluginDir);
        }
        $this->_customScriptsLoaded = true;
    }
    
    /**
     * Add a script path to the view.
     * 
     * @internal Overrides Zend_View_Abstract to ensure that duplicate paths
     * are not added to the stack.  Fixes a bug where include'ing the same 
     * script twice causes fatal errors.
     * @param string $path Local filesystem path.
     */
    public function addScriptPath($path)
    {
        // For some unknown reason, Zend_View adds a trailing slash to paths.
        // Need to add that for the purposes of comparison.
        $path = rtrim($path, '/') . '/';
        
        if (!in_array($path, $this->getScriptPaths())) {
            return parent::addScriptPath($path);
        }
    }
}
