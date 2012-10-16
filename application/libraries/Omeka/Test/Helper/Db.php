<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Catch-all class for database helper methods that are shared across test cases.
 * 
 * @package Omeka\Test\Helper
 */
class Omeka_Test_Helper_Db
{
    /**
     * Database adapter object.
     *
     * @var Zend_Db_Adapter_Abstract
     */
    private $_dbAdapter;

    private $_prefix;
    
    /**
     * @param Zend_Db_Adapter_Abstract $dbAdapter
     */
    public function __construct(Zend_Db_Adapter_Abstract $dbAdapter, $prefix)
    {
        $this->_dbAdapter = $dbAdapter;
        $this->_prefix = $prefix;
    }
    
    /**
     * Proxy to the db adapter object for all other requests.
     *
     * @param string $method Method name.
     * @param array $args Method arguments.
     * @return array
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
     */
    public static function factory($dbConfig)
    {
        if ($dbConfig instanceof Zend_Application) {
            $dbResource = $dbConfig->getBootstrap()->getResource('Db');
            return new self($dbResource->getAdapter(), $dbResource->prefix);
        } else if ($dbConfig instanceof Omeka_Test_Resource_Db) {
            return new self($dbConfig->getDb()->getAdapter(), $dbConfig->getDb()->prefix);
        } else if (is_array($dbConfig) || ($dbConfig instanceof Zend_Config)){
            return new self(Zend_Db::factory('Mysqli', $dbConfig), $dbConfig->prefix);
        } else {
            throw new InvalidArgumentException("\$dbConfig must be an array, Zend_Config or Zend_Application!");
        }
    }
    
    /**
     * Check whether a table exists in the database.
     *
     * @param string $tableName
     * @return boolean
     */
    public function tableExists($tableName)
    {
        $result = $this->_dbAdapter->fetchOne("SHOW TABLES LIKE '$tableName'");
        return (boolean)$result;
    }
    
    /**
     * Get the number of tables in the database.
     * 
     * @param string $prefix
     * @return integer
     */
    public function getTableCount($prefix = null)
    {
        $sql = "SHOW TABLES " . ($prefix ? "LIKE '$prefix%'" : '');
        return count($this->_dbAdapter->fetchCol($sql));
    }

    /**
     * Drop the tables from the database.
     *
     * @param string $prefix Optionally, delete only tables with this prefix.
     * @return void
     */
    public function dropTables($tables = null)
    {
        if (is_array($tables)) {
            $tableNames = $tables;
        } else {
            $tableNames = $this->getTableNames($this->getPrefix());
        }
        if ($tableNames) {
            $dropSql = "DROP TABLE `" . join($tableNames, '`,`') . '`';
            $this->_dbAdapter->query($dropSql);
        }
    }

    /**
     * Truncate the tables from the database.
     *
     * @param string $prefix Optionally, delete only tables with this prefix.
     * @return void
     */
    public function truncateTables($tables = null)
    {
        if (is_array($tables)) {
            $tableNames = $tables;
        } else {
            $tableNames = $this->getTableNames($this->getPrefix());
        }
        if ($tableNames) {
            foreach ($tableNames as $name) {
                $truncateSql = "TRUNCATE TABLE `$name`";
                $this->_dbAdapter->query($truncateSql);
            }
        }
    }

    public function install()
    {
        $installer = new Installer_Test(new Omeka_Db($this->getAdapter(), $this->getPrefix()));
        $installer->install();
    }
    
    /**
     * Get the tables in the database.
     *
     * @param string $prefix Optionally, show only tables with this prefix.
     * @return array
     */
    public function getTableNames()
    {
        $prefix = $this->getPrefix();
        $sql = "SHOW TABLES " . ($prefix ? "LIKE '$prefix%'" : '');
        return $this->_dbAdapter->fetchCol($sql);
    }    
    
    /**
     * Get the number of rows in a table.
     *
     * @param string $tableName
     * @return integer
     */
    public function getRowCount($tableName)
    {
        $sql = "SELECT COUNT(*) FROM $tableName";
        return $this->_dbAdapter->fetchOne($sql);
    }

    public function getAdapter()
    {
        return $this->_dbAdapter;
    }

    public function getPrefix()
    {
        return $this->_prefix;
    }
}
