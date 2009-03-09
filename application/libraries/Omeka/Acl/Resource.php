<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/
 
/**
 * @package Omeka
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class Omeka_Acl_Resource extends Zend_Acl_Resource
{
    protected $_privileges = array();
    
    public function add(array $privileges)
    {
        $this->_privileges = array_merge($this->_privileges, $privileges);
    }
    
    public function has($privilege)
    {
        return in_array($privilege, $this->_privileges);
    }
}
