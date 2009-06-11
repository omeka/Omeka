<?php 

/**
* 
*/
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
        
        $dbOptions = $bootstrap->getResource('Options');
        $pluginOptions = array('dbOptions' => $dbOptions, 'baseThemePath'=> $this->_basePath, 'webBaseThemePath' => $this->_webBasePath);
        $front->registerPlugin(new Omeka_Controller_Plugin_ViewScripts($pluginOptions));
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