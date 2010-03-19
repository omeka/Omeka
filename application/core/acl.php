<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
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
    'ItemTypes', 
    'Users'
);

//Each entry in this array is the set of the values passed to $acl->allow()
$allowList = array(
    //Anyone can browse Items, Item Types, Tags and Collections
    array(null, array('Items', 'ItemTypes', 'Tags', 'Collections'), array('index','browse', 'show')),
    //Super user can do anything
    array('super'),
    //Researchers can view items and collections that are not yet public
    array('researcher',array('Items', 'Collections'),array('showNotPublic')),
    //Contributors can add and tag items, edit or delete their own items, and see their items that are not public
    array('contributor', 'Items', array('tag', 'add', 'editSelf', 'deleteSelf', 'showSelfNotPublic'))
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

$acl->allow(null, 'Users', array('edit', 'show', 'change-password', 'delete'), new Omeka_Acl_Assertion_UserAccount);
// Always allow users to login, logout and send forgot-password notifications.
$acl->allow(null, 'Users', array('login', 'logout', 'forgot-password', 'activate'));

//Deny a couple of specific privileges to admin users
$acl->deny('admin', array('Settings', 'Plugins', 'Themes', 'Upgrade', 'ElementSets', 'Security'));
$acl->deny('admin', 'ItemTypes', array('delete', 'delete-element'));
$acl->deny('admin', 'Users', array('add', 'browse'));
?>