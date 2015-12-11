<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * @package Omeka\Controller
 */
class UpgradeController extends Zend_Controller_Action
{
    public function __construct(Zend_Controller_Request_Abstract $request,
                                Zend_Controller_Response_Abstract $response,
                                array $invokeArgs = array())
    {
        parent::__construct($request, $response, $invokeArgs);

    }
    
    public function indexAction()
    {
        if (!$this->_satisfiesPhpRequirement()) {
            $this->_displayPhpRequirementMessage();
            return;
        }
    }

    /**
     * Run the migration script, obtain any success/error output and display it in a pretty way
     *
     * @return void
     **/
    public function migrateAction()
    {
        if (!$this->_satisfiesPhpRequirement()) {
            $this->_displayPhpRequirementMessage();
            return;
        }

        $manager = Omeka_Db_Migration_Manager::getDefault();
        if (!$manager->canUpgrade()) {
            throw new Omeka_Db_Migration_Exception('Omeka is unable to upgrade.');
        }

        $this->view->success = false;
        try {
            $manager->migrate();
            $manager->finalizeDbUpgrade();
            $this->view->success = true;
        } catch (Omeka_Db_Migration_Exception $e) {
            $this->view->error = $e->getMessage();
            $this->view->exception = $e;
        } catch (Zend_Db_Exception $e) {
            $this->view->error = __("SQL error in migration: ") . $e->getMessage();
            $this->view->exception = $e;
        }
    }

    protected function _satisfiesPhpRequirement()
    {
        return version_compare(PHP_VERSION, Installer_Requirements::OMEKA_PHP_VERSION, '>=');
    }

    public function _displayPhpRequirementMessage()
    {
        $this->view->success = false;
        $this->view->error = __('Omeka requires PHP version %1$s or higher, but this server is ' .
            'running PHP version %2$s. Please update the installed version of PHP and try again.',
            Installer_Requirements::OMEKA_PHP_VERSION, PHP_VERSION);
        $this->_helper->viewRenderer->render('migrate');
    }
}
