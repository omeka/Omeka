<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */
 
/**
 * Extension of Zend_Acl_Resource, adds the concept of privileges within
 * resources.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Omeka_Acl_Resource extends Zend_Acl_Resource
{
    /**
     * Named privileges for this resource.
     *
     * @var array
     */
    protected $_privileges = array();
    
    /**
     * Add privileges.
     *
     * @param array $privileges
     * @return void
     */
    public function add(array $privileges)
    {
        $this->_privileges = array_merge($this->_privileges, $privileges);
    }
    
    /**
     * Check if this resource has a given privilege.
     *
     * @param string $privilege
     * @return boolean
     */
    public function has($privilege)
    {
        return in_array($privilege, $this->_privileges);
    }
}
