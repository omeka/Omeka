<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2009-2011
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Default installer controller.
 *
 * @package Omeka
 */
class IndexController extends Zend_Controller_Action
{
    // Load the database.  If not possible, redirect to the fail script.
    // Check if Omeka is installed already.  If so, redirect accordingly.
    public function preDispatch()
    {
        // No extra processing if we are just displaying the 'fatal-error' action.
        if ($this->getRequest()->getActionName() !== 'fatal-error') {
            $bootstrap = $this->getInvokeArg('bootstrap');
            // Try to load the database connection, otherwise redirect to 'fatal-error'.
            try {
                $bootstrap->bootstrap('Db');
                if (!$bootstrap->hasResource('db')) {
                    throw new Exception("Could not load database resource!");
                }
            } catch (Zend_Config_Exception $e) {
                // A Zend_Config_Exception means it couldn't read the db.ini file.
                $this->getRequest()->setParam('exception', $e);
                return $this->getRequest()->setActionName('fatal-error')->setDispatched(false);
            }
            
            // Don't attempt to forward exceptions to the ErrorController.
            Zend_Controller_Front::getInstance()->setParam('noErrorHandler', true);
            
            $this->installer = new Installer_Default($bootstrap->getResource('db'));
            
            // If Omeka is not already installed, forward to the action that displays that error.
            if ($this->installer->isInstalled() && ($this->getRequest()->getActionName() !== 'installed')) {
                return $this->getRequest()->setActionName('installed')->setDispatched(false);
            }
        }
    }
    
    public function indexAction()
    {
        $bootstrap = $this->getInvokeArg('bootstrap');
        $db = $bootstrap->db;
        $storage = $bootstrap->bootstrap('Storage')->storage;
        $requirements = new Installer_Requirements;
        $requirements->setDbAdapter($db->getAdapter());
        $requirements->setStorage($storage);
        $requirements->check();
        require_once APP_DIR . '/forms/Install.php';
        $form = new Omeka_Form_Install;
        $form->setDefault('path_to_convert',Omeka_File_Derivative_Image::getDefaultConvertDir());
        if ($requirements->hasError()) {
            return $this->_forward('errors', null, null, array('installer'=>$requirements));
        } else if ($this->getRequest()->isPost() && $form->isValid($_POST)) {
            $this->installer->setForm($form);
            $this->installer->install();
            return $this->_forward('installed');
        } 
        $this->view->requirements = $requirements;
        $this->view->installer = $this->installer;
        $this->view->form = $form;
    }
        
    public function installedAction()
    {
        
    }
    
    public function errorsAction()
    {
        $this->view->installer = $this->_getParam('installer');
    }
        
    public function fatalErrorAction()
    {
        $this->view->e = $this->getRequest()->getParam('exception');
    }
}
