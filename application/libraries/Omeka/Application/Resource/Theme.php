<?php 
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Set up the controller plugin that determines theme view script paths.
 * 
 * @package Omeka\Application\Resource
 */
class Omeka_Application_Resource_Theme extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * Theme base path.
     * Set by application config.
     *
     * @var string
     */
    protected $_basePath;
    
    /**
     * Theme base URI.
     * 
     * Set by application config.
     *
     * @var string
     */
    protected $_webBasePath;
    
    public function init()
    {
        // We need the front controller to be set up if we're initializing the
        // Theme component.
        $bootstrap = $this->getBootstrap();
        $bootstrap->bootstrap('FrontController');
        $front = $bootstrap->getResource('FrontController');
        
        // This requires that the options have been properly instantiated.
        $bootstrap = $this->getBootstrap();
        $bootstrap->bootstrap('Options');
        
        // This needs the plugin broker to be built out already.
        $bootstrap->bootstrap('Pluginbroker');
        $dbOptions = $bootstrap->getResource('Options');
        $pluginOptions = array('dbOptions' => $dbOptions, 'baseThemePath'=> $this->_basePath, 'webBaseThemePath' => $this->_webBasePath);
        $front->registerPlugin(new Omeka_Controller_Plugin_ViewScripts($pluginOptions, Zend_Registry::get('plugin_mvc')));
    }
    
    /**
     * Set the base path for themes.
     * Used to allow {@link $_basePath} to be set by application config.
     *
     * @param string $basePath
     */
    public function setbasepath($basePath)
    {
        $this->_basePath = $basePath;
    }
    
    /**
     * Set the base URI for themes.
     * Used to allow {@link $_webBasePath} to be set by application config.
     *
     * @param string $webBasePath
     */
    public function setwebbasepath($webBasePath)
    {
        $this->_webBasePath = $webBasePath;
    }
}
