<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Overrides Omeka's normal routing when the database needs to be upgraded.
 * 
 * @package Omeka\Controller\Plugin
 */
class Omeka_Controller_Plugin_Upgrade extends Zend_Controller_Plugin_Abstract
{
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
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        // Block access to the upgrade controller.
        if ($request->getControllerName() == 'upgrade'
         && $request->getModuleName() == 'default'
         && !$this->_dbCanUpgrade()
        ) {
            $request->setControllerName('index')
                    ->setActionName('index')
                    ->setDispatched(false);
        }
        if ($this->_dbNeedsUpgrade()) {
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

    private function _dbNeedsUpgrade()
    {
        $migrationManager = Omeka_Db_Migration_Manager::getDefault();
        return $migrationManager->dbNeedsUpgrade();
    }

    private function _dbCanUpgrade()
    {
        $migrationManager = Omeka_Db_Migration_Manager::getDefault();
        return $migrationManager->canUpgrade();
    }

    /**
     * Redirect to the upgrade controller.
     *
     * @param Zend_Controller_Request_Abstract $request Request object
     * (not used).
     */
    private function _upgrade($request)
    {
        Zend_Controller_Action_HelperBroker::getStaticHelper('redirector')
            ->goto('index', 'upgrade', 'default');
    }
}
