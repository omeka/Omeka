<?php 
/**
 *	Working model for base-level permissions settings
 * 
 **/

$acl = new Omeka_Acl();

$acl->addRole(new Zend_Acl_Role('super'));
$acl->addRole(new Zend_Acl_Role('researcher'));
$acl->addRole(new Zend_Acl_Role('contributor'));
$acl->addRole(new Zend_Acl_Role('admin'));

//The default role has no special permissions and is used for site visitors who are not logged in
//This should be referenced as little as possible in the event that the implementation changes (likely)
$acl->addRole(new Zend_Acl_Role('default'));

$acl->registerRule(new Zend_Acl_Resource('Items'), array('add','editSelf',  'editAll', 'deleteSelf', 'deleteAll', 'tag', 'showNotPublic', 'showSelfNotPublic', 'untagOthers', 'makePublic', 'makeFeatured'));
$acl->registerRule(new Zend_Acl_Resource('Collections'), array('add','edit','delete', 'showNotPublic'));
$acl->registerRule(new Zend_Acl_Resource('Entities'), array('add','edit','displayEmail','delete'));
$acl->registerRule(new Zend_Acl_Resource('Files'), array('edit','delete'));
$acl->registerRule(new Zend_Acl_Resource('Plugins'), array('browse','show','config', 'install'));
$acl->registerRule(new Zend_Acl_Resource('Settings'), array('edit'));
$acl->registerRule(new Zend_Acl_Resource('Tags'), array('rename','remove'));
$acl->registerRule(new Zend_Acl_Resource('Themes'), array('browse','switch'));
$acl->registerRule(new Zend_Acl_Resource('Types'), array('add','edit','delete'));
$acl->registerRule(new Zend_Acl_Resource('Users'), array('browse','show','add','edit','delete','showRoles','editRoles','makeSuperUser', 'changeRole', 'deleteSuperUser'));
$acl->registerRule(new Zend_Acl_Resource('Exhibits'), array('add', 'edit', 'delete', 'addPage', 'editPage', 'deletePage', 'addSection', 'editSection', 'deleteSection', 'save', 'showNotPublic'));

$acl->allow('super'); 

$acl->allow('researcher','Items',array('showNotPublic'));
$acl->allow('researcher','Collections',array('showNotPublic'));

$acl->allow('contributor', 'Items', array('tag', 'add', 'editSelf', 'deleteSelf', 'showSelfNotPublic'));

$acl->allow('admin','Items',array('add','editAll','deleteAll','tag', 'showNotPublic', 'untagOthers', 'makePublic', 'makeFeatured'));
$acl->allow('admin','Collections',array('add','edit','delete', 'showNotPublic'));
$acl->allow('admin','Entities',array('add','edit','delete', 'displayEmail'));
$acl->allow('admin','Files',array('edit','delete'));
$acl->allow('admin','Tags',array('rename','remove'));
$acl->allow('admin','Types',array('add','edit','delete'));
$acl->allow('admin','Users',array('browse','show','add','edit','delete','showRoles', 'changeRole'));
$acl->allow('admin','Exhibits',array('add', 'edit', 'delete', 'addPage', 'editPage', 'deletePage', 'addSection', 'editSection', 'deleteSection', 'save', 'showNotPublic'));
?>