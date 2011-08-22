<?php 
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2009-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Catch-all class for database helper methods that are shared across test cases.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2009-2010
 */
class Omeka_Test_Helper_Db
{
    /**
     * Database adapter object.
     *
     * @var Zend_Db_Adapter_Abstract
     */
    private $_dbAdapter;
    
    /**
     * @param Zend_Db_Adapter_Abstract $dbAdapter
     */
    public function __construct(Zend_Db_Adapter_Abstract $dbAdapter)
    {
        $this->_dbAdapter = $dbAdapter;
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
            return new self($dbConfig->getBootstrap()->getResource('Db')->getAdapter());
        } else if (is_array($dbConfig) || ($dbConfig instanceof Zend_Config)){
            return new self(Zend_Db::factory('Mysqli', $dbConfig));
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
     * Truncate all of the tables in the test database.
     *
     * @return void
     */
    public function truncateTables($prefix = null)
    {
        if ($tables = $this->getTableNames($prefix)) {
            foreach ($tables as $tableName) {
                $this->_dbAdapter->query("TRUNCATE TABLE `$tableName`");
            }
        }
    }
    
    /**
     * Initialize the database schema.
     *
     * @param string $pathToSchemaFile
     * @param string $tablePrefix
     * @return void
     */
    public function loadDbSchema($pathToSchemaFile, $tablePrefix = 'omeka_')
    {
        $omekaDb = new Omeka_Db($this->_dbAdapter, $tablePrefix);
        $omekaDb->loadSqlFile($pathToSchemaFile);
    }

    /**
     * Set up one or more database tables according to the given XML file,
     * which follows the structure of PHPUnit's XmlDataSet (or FlatXmlDataSet).
     *
     * @see PHPUnit_Extensions_Database_DataSet_XmlDataSet
     * @param string $xmlFile Path to XML file.
     * @param string $schemaName Name of database schema for which to load the XML file.
     * @param boolean $flat Whether or not the file is in PHPUnit's Flat XML format.
     */
    public function loadXmlSchema($xmlFile, $schemaName, $flat = false)
    {
        $conn = new Zend_Test_PHPUnit_Db_Connection($this->_dbAdapter, $schemaName); 
        $tester = new Zend_Test_PHPUnit_Db_SimpleTester($conn);
        if ($flat) {
            $dataSet = new PHPUnit_Extensions_Database_DataSet_FlatXmlDataSet($xmlFile);
        } else {
            $dataSet = new PHPUnit_Extensions_Database_DataSet_XmlDataSet($xmlFile);
        }
        $tester->setUpDatabase($dataSet);
    }

    /**
     * Drop the tables from the database.
     *
     * @param string $prefix Optionally, delete only tables with this prefix.
     * @return void
     */
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
    
    /**
     * Get the tables in the database.
     *
     * @param string $prefix Optionally, show only tables with this prefix.
     * @return array
     */
    public function getTableNames($prefix)
    {
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
}
