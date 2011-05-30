<?php 
/**
 * @version $Id$
 * @copyright Roy Rosenzweig Center for History and New Media, 2009-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * Initialize the session and customize the session name to prevent session
 * overlap between different applications that operate on the same server.
 * 
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2009-2010
 */
class Omeka_Core_Resource_Session extends Zend_Application_Resource_Session
{
    /**
     * @return void
     */
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
        $bootstrap->bootstrap('Config');
        $config = $bootstrap->getResource('Config');
        $sessionConfig = isset($config->session) 
                       ? $config->session->toArray()
                       : array();
        
        if (!array_key_exists('name', $sessionConfig) || empty($sessionConfig['name'])) {
            $sessionConfig['name'] = $this->_buildSessionName();
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
