<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * 
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2007-2010
 **/
class Omeka_Controller_Plugin_Upgrade extends Zend_Controller_Plugin_Abstract
{
    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
        $this->_setNeedsUpgrade();
    }
    
    /**
     * @internal Decision logic for routing to the upgrade controller takes place
     * in dispatchLoopStartup so it OVERRIDES Admin controller plugin logic,
     * which otherwise causes an endless redirect.
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        // Block access to the upgrade controller.
        if (!$this->_canUpgrade 
            && ($request->getControllerName() == 'upgrade')
            && ($request->getModuleName() == 'default')) {
            $request->setControllerName('index')
                    ->setActionName('index')
                    ->setDispatched(false);
        }        
        if ($this->_needsUpgrade) {
            if (!is_admin_theme()) {
                die("Public site is unavailable until the upgrade completes.");
            }
            // This is a workaround to avoid the authentication bullshit.
            Zend_Controller_Front::getInstance()->unregisterPlugin('Omeka_Controller_Plugin_Admin');
            
            if ($request->getControllerName() != 'upgrade') {
                $this->_upgrade($request);
            }
        }
    }
        
    private function _setNeedsUpgrade()
    {
        $migrationManager = Omeka_Db_Migration_Manager::getDefault();
        $this->_needsUpgrade = $migrationManager->dbNeedsUpgrade();
        $this->_canUpgrade = $migrationManager->canUpgrade();
    }
    
    private function _upgrade($request)
    {
        Zend_Controller_Action_HelperBroker::getStaticHelper('redirector')
            ->goto('index', 'upgrade', 'default');
        // var_dump(Zend_Controller_Front::getInstance()->getPlugins());exit;
    }
}
