<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * @see Omeka_Controller_Action
 */
require_once 'Omeka/Controller/Action.php';
 
/**
 * @package Omeka
 * @subpackage Controllers
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/ 
class UpgradeController extends Omeka_Controller_Action
{
    /**
     * Silence all exceptions that might occur when activating this controller, 
     * since all of these exceptions would come from having an outdated database anyway
     *
     **/
    public function __construct(Zend_Controller_Request_Abstract $request,
                                Zend_Controller_Response_Abstract $response,
                                array $invokeArgs = array())
    {
        try {
            parent::__construct($request, $response, $invokeArgs);
        } catch (Exception $e) {
        }
        
        //Make sure we only load the built-in view scripts when upgrading
        $this->view->setScriptPath(VIEW_SCRIPTS_DIR);
        $this->view->setAssetPath(VIEW_SCRIPTS_DIR, WEB_VIEW_SCRIPTS);
    }
    
    protected function getStartMigration()
    {
        return (int) get_option('migration');
    }
        
    /**
     * Run the migration script, obtain any success/error output and display it in a pretty way
     *
     * @return void
     **/
    public function migrateAction()
    {        
        require_once 'Omeka/Upgrader.php';
        
        $from = $this->getStartMigration();
        
        //The version# to migrate to can be set in the query string
        $to = $this->_getParam('to', OMEKA_MIGRATION);
        
        $output  = array();
        $errors  = array();
        $success = false;
        
        if (!is_numeric($to)) {
            $errors[] = "A valid migration # must be passed to upgrade Omeka!";
        } else {
               //If we don't have to migrate the data, show the 'completed' page instead
            if ($from == $to) {
                $this->redirect->goto('completed');
            }
            
            $upgrader = new Omeka_Upgrader($from, $to);
            $upgrader->run();
            
            $output = $upgrader->getOutput();
            $errors = $upgrader->getErrors();
        }
        
        $success = (bool) !count($errors);
        
        $this->view->assign(compact('output', 'errors', 'success'));
    }
    
    public function completedAction(){}
}