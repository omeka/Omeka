<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/
 
/**
 * Set up the controller plugin that determines theme view script paths.
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2007-2010
 **/
class Omeka_Core_Resource_Theme extends Zend_Application_Resource_ResourceAbstract
{
    
    protected $_basePath;
    
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
    
    public function setbasepath($basePath)
    {
        $this->_basePath = $basePath;
    }
    
    public function setwebbasepath($webBasePath)
    {
        $this->_webBasePath = $webBasePath;
    }
}