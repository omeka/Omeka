<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * 
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2009
 **/
class Omeka_Core_Resource_Acl extends Omeka_Core_Resource_ResourceAbstract
{
    protected $_acl;
    
    public function init()
    {
        // Setup the ACL
        include CORE_DIR . DIRECTORY_SEPARATOR . 'acl.php';
        
        $this->_acl = $acl;
        
        Omeka_Context::getInstance()->setAcl($acl);
        
        if ($this->getBootstrap()->hasResource('PluginBroker')) {
            $broker = $this->getBootstrap()->getResource('PluginBroker');
            $broker->define_acl($acl);
        }
                
        return $acl;
    }
}
