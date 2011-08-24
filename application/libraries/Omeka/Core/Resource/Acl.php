<?php 
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2009-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * Initializes Omeka's ACL
 * 
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @since 0.10 Plugins must use the 'define_acl' hook to modify ACL definitions.
 * @uses Omeka_Acl
 * @todo ACL settings should be stored in the database.  When ACL settings
 * are properly stored in a normalized database configuration, then this
 * method should populate a new Acl instance with those settings and store
 * that Acl object in a session for quick access.
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2009-2010
 */
class Omeka_Core_Resource_Acl extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * Access control list object.
     *
     * @var Omeka_Acl
     */
    protected $_acl;
    
    /**
     * Load the hardcoded ACL definitions, then apply definitions from plugins.
     *
     * @return Omeka_Acl
     */
    public function init()
    {
        // Setup the ACL
        include CORE_DIR . '/' . 'acl.php';
        
        $this->_acl = $acl;
                
        if ($this->getBootstrap()->hasResource('PluginBroker')) {
            $broker = $this->getBootstrap()->getResource('PluginBroker');
            $broker->define_acl($acl);
        }
                
        return $acl;
    }
}
