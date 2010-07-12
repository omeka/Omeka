<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Overrides Omeka's normal routing when the database needs to be upgraded. 
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2007-2010
 */
class Omeka_Controller_Plugin_Upgrade extends Zend_Controller_Plugin_Abstract
{
    /**
     * Set upgrade-related flags upon routing system startup.
     *
     * @uses Omeka_Controller_Plugin_Upgrade::_setNeedsUpgrade()
     * @param Zend_Controller_Request_Abstract $request Request object
     * (not used).
     * @return void
     */
    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
        $this->_setNeedsUpgrade();
    }
    
    /**
     * Set up routing for the upgrade controller.
     *
     * Only allows authorized users to upgrade, and blocks the public site when
     * an upgrade is needed.
     *
     * @internal Decision logic for routing to the upgrade controller takes place
     * in dispatchLoopStartup so it OVERRIDES Admin controller plugin logic,
     * which otherwise causes an endless redirect.
     *
     * @param Zend_Controller_Request_Abstract $request Request object.
     * @return void
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
            // This is a workaround to avoid the authentication requirement.
            Zend_Controller_Front::getInstance()->unregisterPlugin('Omeka_Controller_Plugin_Admin');
            
            if ($request->getControllerName() != 'upgrade') {
                $this->_upgrade($request);
            }
        }
    }
    
    /**
     * Set flags indicating whether the DB needs to and can be upgraded.
     *
     * @return void
     */
    private function _setNeedsUpgrade()
    {
        $migrationManager = Omeka_Db_Migration_Manager::getDefault();
        $this->_needsUpgrade = $migrationManager->dbNeedsUpgrade();
        $this->_canUpgrade = $migrationManager->canUpgrade();
    }
    
    /**
     * Redirect to the upgrade controller.
     *
     * @param Zend_Controller_Request_Abstract $request Request object
     * (not used).
     * @return void
     */
    private function _upgrade($request)
    {
        Zend_Controller_Action_HelperBroker::getStaticHelper('redirector')
            ->goto('index', 'upgrade', 'default');
    }
}

