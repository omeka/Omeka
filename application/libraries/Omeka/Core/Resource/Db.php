<?php 

/**
* 
*/
class Omeka_Core_Resource_Db extends Zend_Application_Resource_Db
{
    private $_iniPath;
    
    public function init()
    {
        $dbFile = $this->_iniPath;
        
        if (!file_exists($dbFile)) {
            throw new Zend_Config_Exception('Your Omeka database configuration file is missing.');
        }
        
        if (!is_readable($dbFile)) {
            throw new Zend_Config_Exception('Your Omeka database configuration file cannot be read by the application.');
        }
        
        $dbIni = new Zend_Config_Ini($dbFile, 'database');
        
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
        if (isset($dbIni->port)) {
            $connectionParams['port'] = $dbIni->port;
        }
        
        $dbh = Zend_Db::factory('Mysqli', $connectionParams);
        
        $db_obj = new Omeka_Db($dbh, $dbIni->prefix);
        
        return $db_obj;
    }
    
    public function setinipath($path)
    {
        $this->_iniPath = $path;
    }
}
