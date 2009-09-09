<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initDb()
    {
        $dbIni = new Zend_Config_Ini(BASE_DIR . '/db.ini', 'database');
        $dbh = Zend_Db::factory('Mysqli', array(
            'host' => $dbIni->host,
            'username' => $dbIni->username,
            'password' => $dbIni->password,
            'dbname' => $dbIni->name,
            'port' => $dbIni->port
        ));
        
        // Zend_Registry::set('zend_db', $dbh);
        // 
        // // And the decorator as well.
        $db = new Omeka_Db($dbh, $dbIni->prefix);
        // Zend_Registry::set('db', $db);
        
        Omeka_Context::getInstance()->setDb($db);
        
        return $db;
    }
}