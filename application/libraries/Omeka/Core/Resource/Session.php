<?php 

/**
 * Initialize the session and customize the session name to prevent session
 * overlap between different applications that operate on the same server.
 * @package Omeka
 * @copyright Center for History and New Media, 2009
 **/
class Omeka_Core_Resource_Session extends Zend_Application_Resource_ResourceAbstract
{
    public function init()
    {
        // Look for the session name as the 'session.name' value in the 
        // config.ini file.  If it can't find that value (or it is blank), it
        // will automatically generate the session name based on the root URL
        // of this particular installation.
        $bootstrap = $this->getBootstrap();
        $bootstrap->bootstrap('Config');
        $basicConfig = $bootstrap->getResource('Config');
        $sessionName = (isset($basicConfig->session) && !empty($basicConfig->session->name)) 
                       ? $basicConfig->session->name
                       : $this->_buildSessionName();
    
        Zend_Session::start(array(
            'name'=>$sessionName));
    }
    
    private function _buildSessionName()
    {
        return md5(BASE_DIR);
    }
}
