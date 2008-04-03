<?php 
//Acl dependency
$acl = new MockOmeka_Acl;

//For testing purposes, all permissions checks should be OK'ed
$acl->setReturnValue('checkUserPermission', true);
Zend_Registry::set('acl', $acl);

//Plugin broker dependency
$broker = new MockPluginBroker;
Zend_Registry::set('plugin_broker', $broker); 

//Database connection dependency
$db = new MockOmeka_Db;

//All queries should return a PDO Statement object unless told otherwise
$stmt = new PDOStatement;
$db->setReturnValue('query', $stmt);


$db->Tag = 'tags';
$db->Taggings = 'taggings';
$db->Item = 'items';
$db->Entity = 'entities';
$db->User = 'users';

Zend_Registry::set('db', $db);
$this->db = $db;

?>
