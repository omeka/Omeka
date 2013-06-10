<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Encapsulates the permissions check for a record that can be public or private.
 * 
 * @package Omeka\Db
 */
class Omeka_Db_Select_PublicPermissions
{
    protected $_allPermission;
    protected $_selfPermission;
    protected $_currentUser;

    /**
     * Create the permissions object and perform the ACL checks.
     *
     * The permissions check relies on 'showNotPublic' and (optionally)
     * 'showSelfNotPublic' privileges on the give resource.
     *
     * @param string $resource ACL resource name to check.
     */
    public function __construct($resource)
    {
        $bootstrap = Zend_Registry::get('bootstrap');
        $acl = $bootstrap->getResource('Acl');
        if (!$acl || !$acl->has($resource)) {
            return;
        }

        $this->_currentUser = $bootstrap->getResource('CurrentUser');
        $this->_allPermission = $acl->isAllowed($this->_currentUser, $resource, 'showNotPublic');
        $this->_selfPermission = $acl->isAllowed($this->_currentUser, $resource, 'showSelfNotPublic');
    }

    /**
     * Apply the permissions to an SQL select object.
     *
     * @param Omeka_Db_Select $select
     * @param string $alias Table alias to query against
     * @param string $ownerColumn Optional column for checking for ownership. If
     *  falsy, the ownership check is skipped.
     * @param string $publicColumn Optional column for storing public status. The
     *  column must represent "public" status as the value 1.
     */
    public function apply(Omeka_Db_Select $select, $alias, $ownerColumn = 'owner_id',
        $publicColumn = 'public')
    {
        // If the current user has the 'all' permission, we don't need to do
        // anything. If the 'all' permission's neither true nor false, we
        // _shouldn't_ do anything, because the ACL isn't loaded.
        if ($this->_allPermission || $this->_allPermission === null) {
            return;
        }
        
        if ($ownerColumn && $this->_selfPermission) {
            $select->where("$alias.$publicColumn = 1 OR $alias.$ownerColumn = ?", $this->_currentUser->id);
        } else {
            $select->where("$alias.$publicColumn = 1");
        }
    }
}
