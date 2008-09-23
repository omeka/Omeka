<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/
 
/**
 * Hard-coded baseline resources/privileges for the ACL.
 * 
 * These are loaded into the ACL object if an ACL object
 * does not already exist in the database.  
 *
 * IMPORTANT: If you change this file and want to test new ACL settings, 
 * delete the 'acl' entry in the 'options' table in your Omeka database.  
 * That represents the serialized ACL object, which will be used instead of
 * these hard coded settings if it is available.
 */

//Define our resources/privileges in a flat list here
$resources = array(
    'Items'         =>  array('add','editSelf',  'editAll', 'deleteSelf', 'deleteAll', 'tag', 'showNotPublic', 'showSelfNotPublic', 'untagOthers', 'makePublic', 'makeFeatured', 'modifyPerPage', 'browse'),
    'Collections'   =>  array('add','edit','delete', 'showNotPublic', 'browse'),
    'ElementSets'   =>  array('browse', 'delete'),
    'Entities'      =>  array('add','edit','displayEmail','delete', 'browse'),
    'Files'         =>  array('edit','delete'),
    'Plugins'       =>  array('browse','config', 'install', 'uninstall'),
    'Settings'      =>  array('edit'),
    'Upgrade'       =>  array('migrate'),
    'Tags'          =>  array('rename','remove', 'browse'),
    'Themes'        =>  array('browse','switch'),
    'ItemTypes'     =>  array('add','edit','delete', 'browse'),
    'Users'         =>  array('browse','show','add','edit','delete','showRoles','editRoles','makeSuperUser', 'changeRole', 'deleteSuperUser', 'togglePrivilege')
);

//Each entry in this array is the set of the values passed to $acl->allow()
$allowList = array(
    //Anyone can login, logout, retrieve lost password and activate their accounts
    array(null,'Users',array('login', 'logout', 'forgot-password', 'activate')),
    //Anyone can browse Items, Item Types, Tags and Collections
    array(null, array('Items', 'ItemTypes', 'Tags', 'Collections'), array('browse')),
    //Super user can do anything
    array('super'),
    //Researchers can view items and collections that are not yet public
    array('researcher',array('Items', 'Collections'),array('showNotPublic')),
    //Contributors can add and tag items, edit or delete their own items, and see their items that are not public
    array('contributor', 'Items', array('tag', 'add', 'editSelf', 'deleteSelf', 'showSelfNotPublic')),
    array('admin','Items',array('add','editAll','deleteAll','tag', 'showNotPublic', 'untagOthers', 'makePublic', 'makeFeatured', 'modifyPerPage')),
    array('admin','Collections',array('add','edit','delete', 'showNotPublic')),
    array('admin','Entities',array('add','edit','delete', 'displayEmail', 'browse')),
    array('admin','Files',array('edit','delete')),
    array('admin','Tags',array('rename','remove')),
    array('admin','ItemTypes',array('add','edit','delete')),
    array('admin','Users',array('browse','show','add','edit','delete','showRoles', 'changeRole')) 
); 

/* $acl = new Omeka_Acl($roles, $resources, $allowList);  */

$acl = new Omeka_Acl;

$acl->loadResourceList($resources);

$acl->addRole(new Zend_Acl_Role('researcher'));

$acl->addRole(new Zend_Acl_Role('super'));
// Admins inherit privileges from super users.
$acl->addRole(new Zend_Acl_Role('admin'), 'super');

//Contributors do not inherit from the other roles.
$acl->addRole(new Zend_Acl_Role('contributor'));

$acl->loadAllowList($allowList);

//Deny a couple of specific privileges to admin users
$acl->deny('admin', array('Settings', 'Plugins', 'Themes', 'Upgrade', 'ElementSets'));
$acl->deny('admin', 'Users', array('editRoles','makeSuperUser', 'deleteSuperUser', 'togglePrivilege'));
?>