<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Retrive the User record corresponding to the authenticated user.
 *
 * If the user record is not retrievable (invalid ID), then the authentication 
 * ID will be cleared.
 * 
 * @package Omeka\Application\Resource
 */
class Omeka_Application_Resource_Currentuser extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * Retrieve the User record associated with the authenticated user.
     *
     * @return User|null
     */
    public function init()
    {
        $this->getBootstrap()->bootstrap('Auth');
        $auth = $this->getBootstrap()->getResource('Auth');
        $this->getBootstrap()->bootstrap('Db');
        $db = $this->getBootstrap()->getResource('Db');
        $front = Zend_Controller_Front::getInstance();
        $request = new Zend_Controller_Request_Http;
        
        // REST API requests require a slightly different authentication 
        // strategy. They use non-persistant, key-based authentication 
        if ($front->getParam('api')) {
            // Authenticate against the API key in a non-persistent way.
            $auth->setStorage(new Zend_Auth_Storage_NonPersistent);
            $authAdapter = new Omeka_Auth_Adapter_KeyTable($request->getParam('key'));
            $auth->authenticate($authAdapter);
        }
        
        if (!$auth->hasIdentity()) {
            // There is no user if there is no identity.
            return null;
        }
        
        try {
            // Get the user ID for REST API or standard requests.
            if ($front->getParam('api')) {
                // Update the key row.
                $key = $auth->getIdentity();
                $key->ip = inet_pton($request->getClientIp());
                $key->accessed = date('Y-m-d H:i:s');
                $key->save();
                $userId = $key->user_id;
            } else {
                $userId = $auth->getIdentity();
            }
            $user = $db->getTable('User')->findActiveById($userId);
        } catch (Zend_Db_Statement_Exception $e) {
            // Exceptions may be thrown because the database is out of sync with 
            // the code.  Suppress errors and skip authentication, but only 
            // until the database is properly upgraded.
            if (Omeka_Db_Migration_Manager::getDefault()->dbNeedsUpgrade()) {
                $user = null;
            } else {
                throw $e;
            }
        }
        
        if (!$user) {
            // If we can't retrieve the User from the database, it likely means 
            // that this user has been deleted.  In this case, do not allow the 
            // user to stay logged in.
            $auth->clearIdentity();
        }
        
        return $user;
    }
}
