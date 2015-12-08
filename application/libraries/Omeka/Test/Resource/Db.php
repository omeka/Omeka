<?php
require_once APP_DIR . '/forms/Install.php';

/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Set up the database test environment by wiping and resetting the database to
 * a recently-installed state.
 * 
 * @package Omeka\Test\Resource
 */
class Omeka_Test_Resource_Db extends Zend_Application_Resource_Db
{
    const SUPER_USERNAME = 'foobar123';
    const SUPER_PASSWORD = 'foobar123';
    const SUPER_EMAIL    = 'foobar@example.com';
    
    const DEFAULT_USER_ID  = 1;
    
    const DEFAULT_SITE_TITLE    = 'Automated Test Installation';
    const DEFAULT_AUTHOR        = 'CHNM';
    const DEFAULT_COPYRIGHT     = '2010';
    const DEFAULT_DESCRIPTION   = 'This database will be reset after every test run.  DO NOT USE WITH PRODUCTION SITES';

    /**
     * Flag to determine whether the tables need to be dropped. This is a slow
     * process, and really should only be happening once, when the tests are
     * first run.
     */
    public static $dropTables = true;

    /**
     * Flag to determine whether the installer needs to be run.
     */
    public static $runInstaller = true;

    /**
     * Avoid issues with database connections not closing properly after each 
     * test run.
     */
    private static $_cachedAdapter;

    /**
     * Load and initialize the database.
     *
     * @return Omeka_Db
     */
    public function init()
    {   
        $db = $this->getDb();
        $helper = Omeka_Test_Helper_Db::factory($this);
        if (self::$dropTables) {
            $helper->dropTables();
            self::$dropTables = false;
        }
        if (self::$runInstaller) {
            if (!self::$dropTables) {
                $helper->truncateTables();
            }
            $helper->install();
            self::$runInstaller = false;
        }
        $db->beginTransaction();
        return $db;
    }
    
    /**
     * @return Omeka_Db
     */
    public function getDb()
    {
        $this->getBootstrap()->bootstrap('Config');
        $this->useTestConfig();
        return $this->_getOmekaDb();
    }
        
    public function useTestConfig()
    {
        $this->setAdapter('Mysqli');
        $params = Zend_Registry::get('test_config')->db->toArray();
        $params['driver_options']['MYSQLI_INIT_COMMAND'] =
            "SET SESSION sql_mode='STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION'";
        $this->setParams($params);
    }
        
    /**
     * Set the flag that indicates whether or not to run the installer during 
     * init().
     * 
     * @param boolean $flag
     */
    public function setInstall($flag)
    {
        $this->_runInstaller = (boolean)$flag;
    }

    public function getDbAdapter()
    {
        if (self::$_cachedAdapter instanceof Zend_Db_Adapter_Abstract) {
            $adapter = self::$_cachedAdapter;
        } else {
            $adapter = parent::getDbAdapter();
        }
        return $adapter;
    }

    public static function setDbAdapter(Zend_Db_Adapter_Abstract $dbAdapter)
    {
        self::$_cachedAdapter = $dbAdapter;
    }
    
    /**
     * Create a DB instance with the omeka_ prefix.
     *
     * @return Omeka_Db
     */
    private function _getOmekaDb()
    {
        $adapter = $this->getDbAdapter();
        Zend_Db_Table_Abstract::setDefaultAdapter($adapter);
        $omekaDb = new Omeka_Db($adapter, 'omeka_');
        $this->_enableSqlLogging($omekaDb);
        return $omekaDb;
    }
    
    private function _enableSqlLogging(Omeka_Db $db)
    {
        $bs = $this->getBootstrap();
        $loggingEnabled = ($config = $bs->getResource('Config'))
                        && ($config->log->sql);
        if ($loggingEnabled) {
            $bs->bootstrap('Logger');
            $db->setLogger($bs->getResource('Logger'));
        }
    }
}
