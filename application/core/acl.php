<?php 
/**
 * @version $Id$
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 **/
 
$resources = array(
    'Items', 
    'Collections', 
    'ElementSets', 
    'Files', 
    'Plugins', 
    'Settings', 
    'Security', 
    'Upgrade', 
    'Tags', 
    'Themes', 
    'SystemInfo',
    'ItemTypes', 
    'Users',
);

//Each entry in this array is the set of the values passed to $acl->allow()
$allowList = array(
    // Anyone can browse Items, Item Types, Tags and Collections
    array(null, array('Items', 'ItemTypes', 'Tags', 'Collections'), array('index','browse', 'show')),
    // Anyone can browse items by tags or use advanced search for items
    array(null, array('Items'), array('tags', 'advanced-search')),
    // Super user can do anything
    array('super'),
    // Researchers can view items and collections that are not yet public
    array('researcher',array('Items', 'Collections'),array('showNotPublic')),
    // Contributors can add and tag items, edit or delete their own items, and see their items that are not public
    array('contributor', 'Items', array('tag', 'add', 'editSelf', 'deleteSelf', 'showSelfNotPublic')),
    // Non-authenticated users can access the upgrade script (for logistical reasons).
    array(null, 'Upgrade')
); 

/* $acl = new Omeka_Acl($roles, $resources, $allowList);  */

$acl = new Omeka_Acl;

foreach ($resources as $resourceName) {
    $acl->addResource($resourceName);
}

$acl->addRole(new Zend_Acl_Role('super'));

// Admins inherit privileges from super users.
$acl->addRole(new Zend_Acl_Role('admin'), 'super');

//Contributors and researchers do not inherit from the other roles.
$acl->addRole(new Zend_Acl_Role('contributor'));
$acl->addRole(new Zend_Acl_Role('researcher'));


$acl->loadAllowList($allowList);

//Deny a couple of specific privileges to admin users
$acl->deny('admin', array(
    'Settings', 
    'Plugins', 
    'Themes', 
    'ElementSets', 
    'Security', 
    'SystemInfo'
));
$acl->deny(array(null, 'researcher', 'contributor', 'admin', 'super'), 'Users');
// For some unknown reason, this assertion must be associated with named roles 
// (i.e., not null) in order to work correctly.  Allowing the null role causes 
// it to fail.
$acl->allow(array('contributor', 'researcher', 'admin', 'super'), 'Users', null,
    new User_AclAssertion());
$acl->deny('admin', 'ItemTypes', array('delete', 'delete-element'));

// Because Users resource was denied to admins, it must be explicitly allowed here.
$acl->allow(array(null, 'admin'), 'Users', array('edit', 'show', 'change-password', 'delete'), new User_AclAssertion());
// Always allow users to login, logout and send forgot-password notifications.
$acl->allow(array(null, 'admin'), 'Users', array('login', 'logout', 'forgot-password', 'activate'));
?>
