<?php 

/**
* 
*/
class Omeka_Core_Resource_Db extends Zend_Application_Resource_Db
{
    public function init()
    {
        $db_file = BASE_DIR . DIRECTORY_SEPARATOR . 'db.ini';
        
        if (!file_exists($db_file)) {
            throw new Zend_Config_Exception('Your Omeka database configuration file is missing.');
        }
        
        if (!is_readable($db_file)) {
            throw new Zend_Config_Exception('Your Omeka database configuration file cannot be read by the application.');
        }
        
        $dbIni = new Zend_Config_Ini($db_file, 'database');
        
        // Fail on improperly configured db.ini file
        if (!isset($dbIni->host) || ($dbIni->host == 'XXXXXXX')) {
            throw new Zend_Config_Exception('Your Omeka database configuration file has not been set up properly.  Please edit the configuration and reload this page.');
        }
        
        $connectionParams = array('host'     => $dbIni->host,
                                                'username' => $dbIni->username,
                                                'password' => $dbIni->password,
                                                'dbname'   => $dbIni->name);
        
        // 'port' parameter was introduced in 0.10, conditional check needed
        // for backwards compatibility.
        if (isset($db->port)) {
            $connectionParams['port'] = $db->port;
        }
        
        $dbh = Zend_Db::factory('Mysqli', $connectionParams);
        
        $db_obj = new Omeka_Db($dbh, $dbIni->prefix);
        
        return $db_obj;
    }
}
