<?php 
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */
 
/**
 * Set up the controller plugin that determines theme view script paths.
 *
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Omeka_Core_Resource_Theme extends Zend_Application_Resource_ResourceAbstract
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
     * Set by application config.
     *
     * @var string
     */
    protected $_webBasePath;
    
    /**
     * @return void
     */
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
     * @return void
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
     * @return void
     */
    public function setwebbasepath($webBasePath)
    {
        $this->_webBasePath = $webBasePath;
    }
}
