<?php 
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2011
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */
 
/**
 * Database upgrade controller.
 * 
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private 
 * @package Omeka
 * @subpackage Controllers
 */ 
class UpgradeController extends Zend_Controller_Action
{
    public function __construct(Zend_Controller_Request_Abstract $request,
                                Zend_Controller_Response_Abstract $response,
                                array $invokeArgs = array())
    {
        parent::__construct($request, $response, $invokeArgs);
        
        //Make sure we only load the built-in view scripts when upgrading
        $this->view->setScriptPath(VIEW_SCRIPTS_DIR);
        $this->view->setAssetPath(VIEW_SCRIPTS_DIR, WEB_VIEW_SCRIPTS);
    }
    
    public function indexAction()
    {
        
    }
                
    /**
     * Run the migration script, obtain any success/error output and display it in a pretty way
     *
     * @return void
     **/
    public function migrateAction()
    {        
        $manager = Omeka_Db_Migration_Manager::getDefault();
        if (!$manager->canUpgrade()) {
            return $this->_forward('completed');
        }
        
        $debugMode = (boolean)$this->getInvokeArg('bootstrap')->config->debug->exceptions;
        $this->view->debugMode = $debugMode;
        try {
            $manager->migrate();
            $manager->finalizeDbUpgrade();
            $this->view->success = true;            
        } catch (Omeka_Db_Migration_Exception $e) {
            $this->view->error = $e->getMessage();
            $this->view->trace = $e->getTraceAsString();
        } catch (Zend_Db_Exception $e) {
            $this->view->error = __("SQL error in migration: ") . $e->getMessage();
            $this->view->trace = $e->getTraceAsString();
        }
    }
    
    public function completedAction()
    {
        
    }
}
