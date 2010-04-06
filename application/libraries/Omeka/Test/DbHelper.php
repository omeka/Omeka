<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * Catch-all class for database helper methods that are shared across test cases.
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2009
 **/
class Omeka_Test_DbHelper
{
    private $_dbAdapter;
    
    public function __construct(Zend_Db_Adapter_Abstract $dbAdapter)
    {
        $this->_dbAdapter = $dbAdapter;
    }
    
    /**
     * Proxy to the db adapter object for all other requests.
     */
    public function __call($method, $args)
    {
        return call_user_func_array(array($this->_dbAdapter, $method), $args);
    }
    
    /**
     * Create an instance of the helper that is configured for the correct 
     * database.
     * 
     * @param array|Zend_Config|Zend_Application If an array or Zend_Config, uses the
     * Zend_Db factory to create a new Db_Adapter instance.  If Zend_Application
     * is given, it will attempt to retrieve the existing database adapter instance.
     **/
    public static function factory($dbConfig)
    {
        if ($dbConfig instanceof Zend_Application) {
            return new self($dbConfig->getBootstrap()->getResource('Db')->getAdapter());
        } else if (is_array($dbConfig) || ($dbConfig instanceof Zend_Config)){
            return new self(Zend_Db::factory('Mysqli', $dbConfig));
        } else {
            throw new InvalidArgumentException("\$dbConfig must be an array, Zend_Config or Zend_Application!");
        }
    }
        
    public function tableExists($tableName)
    {
        $result = $this->_dbAdapter->fetchOne("SHOW TABLES LIKE '$tableName'");
        return (boolean)$result;
    }
    
    public function getTableCount($prefix = null)
    {
        $sql = "SHOW TABLES " . ($prefix ? "LIKE '$prefix%'" : '');
        return count($this->_dbAdapter->fetchCol($sql));
    }
        
    /**
     * Truncate all of the tables in the test database.
     */
    public function truncateDbTables()
    {
        $tables = $this->_dbAdapter->fetchCol("SHOW TABLES");
        if ($tables) {
            $dropSql = "TRUNCATE `" . join($tables, '`,`') . '`';
            $this->_dbAdapter->query($dropSql);
        }
    }
        
    public function loadDbSchema($pathToSchemaFile, $tablePrefix = 'omeka_')
    {
        $omekaDb = new Omeka_Db($this->_dbAdapter, $tablePrefix);
        $omekaDb->loadSqlFile($pathToSchemaFile);
    }
    
    public function dropTables($prefix = null)
    {
        if (is_array($prefix)) {
            $tableNames = $prefix;
        } else {
            $tableNames = $this->getTableNames($prefix);
        }
        if ($tableNames) {
            $dropSql = "DROP TABLE `" . join($tableNames, '`,`') . '`';
            $this->_dbAdapter->query($dropSql);
        }
    }
        
    public function getTableNames($prefix)
    {
        $sql = "SHOW TABLES " . ($prefix ? "LIKE '$prefix%'" : '');
        return $this->_dbAdapter->fetchCol($sql);
    }    
    
    public function getRowCount($tableName)
    {
        $sql = "SELECT COUNT(*) FROM $tableName";
        return $this->_dbAdapter->fetchOne($sql);
    }
}