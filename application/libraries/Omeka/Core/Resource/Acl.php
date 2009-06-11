<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * Initializes Omeka's ACL
 *
 * Checks to see if there is a serialized copy of the ACL in the database, 
 * then use that.  If not, then set up the ACL based on the hard-coded 
 * settings.
 * 
 * @since 0.10 Plugins must use the 'define_acl' hook to modify ACL definitions.
 * @uses Omeka_Acl
 * @todo ACL settings should be stored in the database.  When ACL settings
 * are properly stored in a normalized database configuration, then this
 * method should populate a new Acl instance with those settings and store
 * that Acl object in a session for quick access.
 * @package Omeka
 * @copyright Center for History and New Media, 2009
 **/
class Omeka_Core_Resource_Acl extends Zend_Application_Resource_ResourceAbstract
{
    protected $_acl;
    
    public function init()
    {
        // Setup the ACL
        include CORE_DIR . DIRECTORY_SEPARATOR . 'acl.php';
        
        $this->_acl = $acl;
                
        if ($this->getBootstrap()->hasResource('PluginBroker')) {
            $broker = $this->getBootstrap()->getResource('PluginBroker');
            $broker->define_acl($acl);
        }
        
        // Set up the action helper for MVC.
        $aclChecker = new Omeka_Controller_Action_Helper_Acl($acl);
        Zend_Controller_Action_HelperBroker::addHelper($aclChecker);
                
        return $acl;
    }
}
