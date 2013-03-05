<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Initialize the session.
 * 
 * Customizes the session name to prevent session overlap between different 
 * applications that operate on the same server.
 * 
 * @package Omeka\Application\Resource
 */
class Omeka_Application_Resource_Session extends Zend_Application_Resource_Session
{
    public function init()
    {
        $this->_setOptionsFromConfig();
        return parent::init();
    }
    
    /**
     * Retrieve global session configuration options.
     * 
     * @link http://framework.zend.com/manual/en/zend.session.global_session_management.html#zend.session.global_session_management.configuration_options
     * @return array An array containing all the global configuration options 
     * for sessions.  This array contains at least one key, 'name', corresponding
     * to the name of the session, which is generated automatically if not 
     * provided.
     */
    private function _getSessionConfig()
    {
        $bootstrap = $this->getBootstrap();
        $bootstrap->bootstrap(array('Config', 'Db'));
        $config = $bootstrap->getResource('Config');
        $sessionConfig = isset($config->session) 
                       ? $config->session->toArray()
                       : array();
        
        if (!array_key_exists('name', $sessionConfig) 
            || empty($sessionConfig['name'])
        ) {
            $sessionConfig['name'] = $this->_buildSessionName();
        }
        
        // Default is store sessions in the sessions table.
        if (!array_key_exists('saveHandler', $sessionConfig)) {
            $db = $bootstrap->db;
            $sessionConfig['saveHandler'] = array(
                'class' => "Omeka_Session_SaveHandler_DbTable",
                'options' => array(
                    'name' => $db->Session,
                    'primary' => "id",
                    'modifiedColumn' => "modified",
                    'dataColumn' => "data",
                    'lifetimeColumn' => "lifetime",
                ),
            );
        } else if (!$sessionConfig['saveHandler']) {
            // Set session.saveHandler = false to use the filesystem for storing
            // sessions.
            unset($sessionConfig['saveHandler']);
        }
        
        return $sessionConfig;
    }
    
    /**
     * Create a unique session name.
     *
     * Hashes the base directory, this ensures that session names differ between
     * Omeka instances on the same server.
     *
     * @return string
     */
    private function _buildSessionName()
    {
        return md5(BASE_DIR);
    }
    
    private function _setOptionsFromConfig()
    {
        $this->setOptions($this->_getSessionConfig());
    }
}
