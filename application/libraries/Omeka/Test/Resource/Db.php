<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2009-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * Get the install form to get current default values.
 */
require_once APP_DIR . '/forms/Install.php';

/**
 * Set up the database test environment by wiping and resetting the database to
 * a recently-installed state.
 *
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
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
    
    private $_runInstaller = true;

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
        $omekaDb = $this->getDb();
        if (!Omeka_Test_AppTestCase::dbChanged()) {
            $this->setInstall(false);
            Omeka_Test_AppTestCase::dbChanged(true);
        }
        if ($this->_runInstaller) {
            $this->_truncateTables($omekaDb);
            $installer = new Installer_Test($omekaDb);
            $installer->install();
        }
        return $omekaDb;
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
        $params['driver_options']['MYSQLI_INIT_COMMAND'] = "SET SESSION sql_mode='STRICT_ALL_TABLES'";
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
        
    /**
     * Truncate all the tables in the test database.
     *
     * @param Omeka_Db $db
     * @return void
     */
    private function _truncateTables(Omeka_Db $db)
    {
        $dbHelper = new Omeka_Test_Helper_Db($db->getAdapter());
        $dbHelper->truncateTables($db->prefix);
    }    
}
