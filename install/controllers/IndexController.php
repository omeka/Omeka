<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * 
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2009
 **/
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
            } catch (Zend_Config_Exception $e) {
                // A Zend_Config_Exception means it couldn't read the db.ini file.
                $this->getRequest()->setParam('exception', $e);
                return $this->getRequest()->setActionName('fatal-error')->setDispatched(false);
            }
            
            // Don't attempt to forward exceptions to the ErrorController.
            Zend_Controller_Front::getInstance()->setParam('noErrorHandler', true);

            // If Omeka is not already installed, forward to the action that displays that error.
            if ($this->getRequest()->getActionName() !== 'already-installed' && Installer::isInstalled()) {
                return $this->getRequest()->setActionName('already-installed')->setDispatched(false);
            }
        }
    }
    
    public function installAction()
    {
        
    }
    
    public function alreadyInstalledAction()
    {
        
    }
    
    public function fatalErrorAction()
    {
        $this->view->e = $this->getRequest()->getParam('exception');
    }
}