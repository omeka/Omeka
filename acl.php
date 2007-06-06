<?php 
/**
 *	Working model for base-level permissions settings
 * 
 **/

$acl = new Kea_Acl();

$acl->setAutoSave(false);

$acl->addRole(new Zend_Acl_Role('super'));
$acl->addRole(new Zend_Acl_Role('researcher'));
$acl->addRole(new Zend_Acl_Role('admin'));

//The default role has no special permissions and is used for site visitors who are not logged in
//This should be referenced as little as possible in the event that the implementation changes (likely)
$acl->addRole(new Zend_Acl_Role('default'));

$acl->registerRule(new Zend_Acl_Resource('Items'), array('add','edit','delete', 'tag', 'showNotPublic', 'untagOthers'));
$acl->registerRule(new Zend_Acl_Resource('Collections'), array('add','edit','delete', 'showInactive'));
$acl->registerRule(new Zend_Acl_Resource('Files'), array('edit','delete'));
$acl->registerRule(new Zend_Acl_Resource('Plugins'), array('browse','edit','show'));
$acl->registerRule(new Zend_Acl_Resource('Settings'), array('edit'));
$acl->registerRule(new Zend_Acl_Resource('Static'), array('browse'));
$acl->registerRule(new Zend_Acl_Resource('Tags'), array('rename','delete'));
$acl->registerRule(new Zend_Acl_Resource('Themes'), array('browse','switch'));
$acl->registerRule(new Zend_Acl_Resource('Types'), array('add','edit','delete'));
$acl->registerRule(new Zend_Acl_Resource('Users'), array('browse','show','add','edit','delete','showRoles','editRoles','makeSuperUser'));

$acl->allow('super'); 

$acl->allow('researcher','Items',array('showNotPublic', 'tag'));
$acl->allow('researcher','Collections',array('showInactive'));

$acl->allow('admin','Items',array('add','edit','delete','tag', 'showNotPublic', 'untagOthers'));
$acl->allow('admin','Collections',array('add','edit','delete', 'showInactive'));
$acl->allow('admin','Files',array('edit','delete'));
$acl->allow('admin','Tags',array('rename','delete'));
$acl->allow('admin','Themes',array('browse'));
$acl->allow('admin','Types',array('add','edit','delete'));
$acl->allow('admin','Users',array('browse','show','add','edit','delete','showRoles'));

?>
