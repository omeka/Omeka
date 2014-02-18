<?php
/**
 * Omeka
 *
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Initializes Omeka's ACL.
 *
 * @package Omeka\Application\Resource
 */
class Omeka_Application_Resource_Acl extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * Access control list object.
     *
     * @var Zend_Acl
     */
    protected $_acl;

    /**
     * Load the hardcoded ACL definitions, then apply definitions from plugins.
     *
     * @return Zend_Acl
     */
    public function init()
    {
        $this->_acl = $this->getAcl();
        if ($this->getBootstrap()->hasResource('PluginBroker')) {
            $broker = $this->getBootstrap()->getResource('PluginBroker');
            $broker->callHook('define_acl', array('acl' => $this->_acl));
        }
        return $this->_acl;
    }

    public function getAcl()
    {
        $acl = new Zend_Acl;

        // Add roles.

        $acl->addRole('super');
        // Admins inherit privileges from super users.
        $acl->addRole('admin', 'super');
        $acl->addRole('researcher');
        // Contributors inherit privileges from researchers.
        $acl->addRole('contributor', 'researcher');

        // Add resources, corresponding to Omeka controllers.

        $resources = array('Items', 'Collections', 'ElementSets', 'Files', 'Plugins',
                           'Settings', 'Security', 'Upgrade', 'Tags', 'Themes',
                           'SystemInfo', 'ItemTypes', 'Users', 'Search', 'Appearance',
                           'Elements');
        foreach ($resources as $resource) {
            $acl->addResource($resource);
        }

        // Define allow rules for everyone.

        // Everyone can view and browse these resources.
        $acl->allow(null, array('Items', 'ItemTypes', 'Tags', 'Collections', 'Search',
                                'ElementSets', 'Elements'),
                    array('index', 'browse', 'show'));
        // Everyone can view an item's tags and use the item search.
        $acl->allow(null, array('Items'), array('tags', 'search'));
        // Everyone can view files.
        $acl->allow(null, 'Files', 'show');
        // Non-authenticated users can access the upgrade script, for logistical reasons.
        $acl->allow(null, 'Upgrade');

        // Deny privileges from admin users
        $acl->deny('admin', array('Settings', 'Plugins', 'Themes', 'ElementSets',
                                  'Security', 'SystemInfo','Appearance'));

        // Assert ownership for certain privileges.

        // Owners can edit and delete items and collections.
        $acl->allow(null, array('Items', 'Collections'), array('edit', 'delete'),
                    new Omeka_Acl_Assert_Ownership);
        // Owners can edit files.
        $acl->allow(null, 'Files', 'edit', new Omeka_Acl_Assert_Ownership);

        // Define allow rules for specific roles.

        // Super users have full privileges.
        $acl->allow('super');
        // Researchers can view and search items and collections that are not public.
        $acl->allow('researcher', array('Items', 'Collections', 'Search'), 'showNotPublic');
        // Contributors can add and tag items, edit or delete their own items, and see
        // their items that are not public.
        $acl->allow('contributor', 'Items', array('add', 'tag', 'batch-edit', 'batch-edit-save',
                                                  'change-type', 'delete-confirm', 'editSelf',
                                                  'deleteSelf', 'showSelfNotPublic'));
        // Contributors can edit their own files.
        $acl->allow('contributor', 'Files', 'editSelf');
        // Contributors have access to tag autocomplete.
        $acl->allow('contributor', 'Tags', array('autocomplete'));
        // Contributors can add collections, edit or delete their own collections, and
        // see their collections that are not public.
        $acl->allow('contributor','Collections', array('add', 'delete-confirm', 'editSelf',
                                                       'deleteSelf', 'showSelfNotPublic'));
        $acl->allow('contributor', 'Elements', 'element-form');

        // Define deny rules.

        // Deny admins from accessing some resources allowed to super users.
        $acl->deny('admin', array('Settings', 'Plugins', 'Themes', 'ElementSets',
                                  'Security', 'SystemInfo'));
        // Deny admins from deleting item types and item type elements.
        $acl->deny('admin', 'ItemTypes', array('delete', 'delete-element'));

        // Deny Users to admins since they normally have all the super permissions.
        $acl->deny(null, 'Users');
        $acl->allow(array('super', 'admin', 'contributor', 'researcher'), 'Users', null,
                    new Omeka_Acl_Assert_User);

        // Always allow users to login, logout and send forgot-password notifications.
        $acl->allow(array(null, 'admin'), 'Users', array('login', 'logout', 'forgot-password', 'activate'));

        return $acl;
    }
}
