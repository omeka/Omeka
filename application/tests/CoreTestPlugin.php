<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

require_once 'Omeka/Core.php'; 
require_once 'Omeka/Controller/Action/Helper/Acl.php';
 
/**
 * Extends off the Core plugin to implement behavior that is specific to the plugin initializer.
 *
 * @package Omeka
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class CoreTestPlugin extends Omeka_Core
{
    protected $_envName = 'adminTheme';
    
    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
        $front = Zend_Controller_Front::getInstance();
        
        switch ($this->_envName) {
            case 'publicTheme':
                $this->sanitizeMagicQuotes();
                $this->initializeClassLoader(); 
                $this->initializeConfigFiles(); 
                // $this->initializeLogger(); 
                // $this->initializeDb(); 
                $this->loadModelClasses(); 
                // $this->initializeOptions(); 
                $this->setOptions(array('public_theme'=>'default'));
            
                $this->initializeAcl(); 
                // $this->initializePluginBroker(); 
                // $this->initializeAuth(); 
                            
                // $this->initializeCurrentUser(); 
                $this->initializeFrontController();

                // Initialize the paths within the view scripts. We do this here instead
                // of allowing the view object to take care of it, because the view object
                // uses database options and hard coded constants that don't translate
                // well into the testing environment. Specifically, the view object uses a
                // THEME_DIR constant that doesn't work well with the testing
                // environment, because you can't change it to use the admin theme instead
                // of the public theme midway through testing.
            
                // Get the view object and initialize the script path to the theme.
                $view = Zend_Registry::get('view');
                $themeName = 'default';
                $this->setThemePath($view, 'themes' . DIRECTORY_SEPARATOR . $themeName);
            
                $this->initializeRoutes();
                // $this->initializeDebugging();                
            break;
            
            case 'adminTheme':
                // define('ADMIN', true);
            
                require_once 'Omeka/Controller/Plugin/Admin.php';
                $front->registerPlugin(new Omeka_Controller_Plugin_Admin);
            
                // Custom load sequence for testing the admin theme.
                
                $this->sanitizeMagicQuotes();
                $this->initializeClassLoader(); 
                $this->initializeConfigFiles(); 
                // $this->initializeLogger(); 
                // $this->initializeDb(); 
                $this->loadModelClasses(); 
                // $this->initializeOptions(); 
                $this->setOptions(array('admin_theme'=>'default'));
                
                // $this->initializeAcl(); 
                // $this->initializePluginBroker(); 
                // $this->initializeAuth(); 
                                
                // $this->initializeCurrentUser(); 
                $this->initializeFrontController();

        
                // Initialize the paths within the view scripts. We do this here instead
                // of allowing the view object to take care of it, because the view object
                // uses database options and hard coded constants that don't translate
                // well into the testing environment. Specifically, the view object uses a
                // THEME_DIR constant that doesn't work well with the testing
                // environment, because you can't change it to use the admin theme instead
                // of the public theme midway through testing.
                
                // Get the view object and initialize the script path to the theme.
                $view = Zend_Registry::get('view');
                $themeName = 'default';
                $this->setThemePath($view, 'admin' . DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR . $themeName);
                
                $this->initializeRoutes();
                // $this->initializeDebugging();
                break;
            default:
                throw new Exception("Start-up environment called '{$this->_envName}' doesn't exist!");
                break;
        }
    }
    
    /**
     * @todo This can actually be abstracted to the view object itself in order
     * to eventually bypass the use of constants.
     * 
     * @param Omeka_View
     * @param string
     * @return void
     **/
    public function setThemePath($view, $physicalPath)
    {
        $webPath = join('/', explode(DIRECTORY_SEPARATOR, $physicalPath));
        
        $view->addScriptPath(BASE_DIR . DIRECTORY_SEPARATOR . $physicalPath);
        
        $view->addAssetPath(BASE_DIR . DIRECTORY_SEPARATOR . $physicalPath, WEB_ROOT . DIRECTORY_SEPARATOR . $webPath);
    }
    
    public function setStartupEnvironment($envName)
    {
        $this->_envName = $envName;
    }
    
    // public function routeShutdown()
    // {
    //     Zend_Controller_Action_HelperBroker::resetHelpers();
    // }
}
