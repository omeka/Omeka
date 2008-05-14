<?php 
/**
 *	Working model for base-level permissions settings
 * 
 **/

//Define our resources/privileges in a flat list here
$resources = array(
    'Items'         =>  array('add','editSelf',  'editAll', 'deleteSelf', 'deleteAll', 'tag', 'showNotPublic', 'showSelfNotPublic', 'untagOthers', 'makePublic', 'makeFeatured', 'modifyPerPage', 'browse'),
    'Collections'   =>  array('add','edit','delete', 'showNotPublic', 'browse'),
    'Entities'      =>  array('add','edit','displayEmail','delete', 'browse'),
    'Files'         =>  array('edit','delete'),
    'Plugins'       =>  array('browse','config', 'install'),
    'Settings'      =>  array('edit'),
    'Upgrade'       =>  array('migrate'),
    'Tags'          =>  array('rename','remove', 'browse'),
    'Themes'        =>  array('browse','switch'),
    'Types'         =>  array('add','edit','delete', 'browse'),
    'Users'         =>  array('browse','show','add','edit','delete','showRoles','editRoles','makeSuperUser', 'changeRole', 'deleteSuperUser', 'togglePrivilege')
);

//Each entry in this array is the set of the values passed to $acl->allow()
$allowList = array(
    //Anyone can login, logout, retrieve lost password and activate their accounts
    array(null,'Users',array('login', 'logout', 'forgot-password', 'activate')),
    //Anyone can browse Items, Item Types, Tags and Collections
    array(null, array('Items', 'Types', 'Tags', 'Collections'), array('browse')),
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
    array('admin','Types',array('add','edit','delete')),
    array('admin','Users',array('browse','show','add','edit','delete','showRoles', 'changeRole')) 
); 

/* $acl = new Omeka_Acl($roles, $resources, $allowList);  */

$acl = new Omeka_Acl;

$acl->loadResourceList($resources);

$acl->addRole(new Zend_Acl_Role('researcher'));

$acl->addRole(new Zend_Acl_Role('super'));
$acl->addRole(new Zend_Acl_Role('admin'), 'super');

//Contributors do not inherit from the other roles
$acl->addRole(new Zend_Acl_Role('contributor'));

$acl->loadAllowList($allowList);

?>