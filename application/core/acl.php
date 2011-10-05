<?php 
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 */
 
//Define our resources/privileges in a flat list here
$resources = array(
    'Items'         =>  array('add', 'batch-edit', 'batch-edit-save', 'edit', 'editSelf', 'editAll', 'delete', 'deleteSelf', 'deleteAll', 'tag', 'showNotPublic', 'showSelfNotPublic', 'untagOthers', 'makePublic', 'makeFeatured', 'modifyPerPage', 'browse'),
    'Collections'   =>  array('add','edit','delete', 'showNotPublic', 'browse'),
    'ElementSets'   =>  array('browse', 'delete'),
    'Files'         =>  array('edit','delete'),
    'Plugins'       =>  array('browse','config', 'install', 'uninstall', 'upgrade', 'activate'),
    'Settings'      =>  array('edit', 'check-imagemagick'),
    'Security'      =>  array('edit'),
    'Upgrade'       =>  array('migrate', 'completed'),
    'Tags'          =>  array('rename','remove', 'browse'),
    'Themes'        =>  array('browse','switch', 'config'),
    'SystemInfo'    =>  array('index'),
    // 'delete-element' and 'add-element' are actions that allow AJAX requests to manipulate the elements for an item type.
    'ItemTypes'     =>  array('add','edit','delete', 'browse', 'delete-element', 'add-element'),
    // 'makeSuperUser' should be deprecated, since it can only be called if non-super users can choose the roles for user accounts.
    // 'changeRole' determines whether the role of a user account can be changed.  only super users can do this.
    'Users'         =>  array('browse','show','add','edit','delete','makeSuperUser', 'changeRole')
);

//Each entry in this array is the set of the values passed to $acl->allow()
$allowList = array(
    //Anyone can browse Items, Item Types, Tags and Collections
    array(null, array('Items', 'ItemTypes', 'Tags', 'Collections'), array('browse')),
    //Super user can do anything
    array('super'),
    //Researchers can view items and collections that are not yet public
    array('researcher',array('Items', 'Collections'),array('showNotPublic')),
    //Contributors can add and tag items, edit or delete their own items, and see their items that are not public
    array('contributor', 'Items', array('tag', 'add', 'batch-edit', 'batch-edit-save', 'editSelf', 'deleteSelf', 'showSelfNotPublic')),
    //Non-authenticated users can access the upgrade script (for logistical reasons).
    array(null, 'Upgrade')
); 

/* $acl = new Omeka_Acl($roles, $resources, $allowList);  */

$acl = new Omeka_Acl;

$acl->loadResourceList($resources);

$acl->addRole(new Zend_Acl_Role('super'));

// Admins inherit privileges from super users.
$acl->addRole(new Zend_Acl_Role('admin'), 'super');

// Contributors inherit researcher permissions.
$acl->addRole(new Zend_Acl_Role('researcher'));
$acl->addRole(new Zend_Acl_Role('contributor'), 'researcher');

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
$acl->allow(array('contributor', 'researcher', 'admin', 'super'),
    'Items', array('edit', 'delete'), new Item_OwnershipAclAssertion());
$acl->deny('admin', 'ItemTypes', array('delete', 'delete-element'));
?>
