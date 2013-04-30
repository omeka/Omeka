<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * 
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Installer_Task_SchemaTest extends PHPUnit_Framework_TestCase
{
    const DB_PREFIX = 'test_';
    
    public function setUp()
    {
        $this->dbAdapter = new Zend_Test_DbAdapter;
        $this->db = new Omeka_Db($this->dbAdapter, self::DB_PREFIX);
        $this->profilerHelper = new Omeka_Test_Helper_DbProfiler($this->dbAdapter->getProfiler(),
            $this);
        $this->schemaTask = new Installer_Task_Schema;
    }
        
    public function testAddTable()
    {
        $collectionSql = SCHEMA_DIR . '/collections.sql';
        $this->schemaTask->addTable('collections', $collectionSql);
        $this->assertEquals(array(
            'collections' => $collectionSql
        ),$this->schemaTask->getTables());
    }
    
    /**
     * @expectedException Installer_Task_Exception
     */
    public function testAddNonExistentTable()
    {
        try {
            $this->schemaTask->addTable('foobar', '/fake/path/to/no/file.sql');
        } catch (Installer_Task_Exception $e) {
            $this->assertContains("Invalid SQL file", $e->getMessage());
            throw $e;
        }
    }
            
    public function testUseDefaultTables()
    {
        $this->assertEquals(0, count($this->schemaTask->getTables()));
        $this->schemaTask->useDefaultTables();
        $this->assertEquals(array(
            'collections' => SCHEMA_DIR . '/collections.sql',
            'element_texts' => SCHEMA_DIR . '/element_texts.sql',
            'item_types' => SCHEMA_DIR . '/item_types.sql',
            'processes' => SCHEMA_DIR . '/processes.sql',
            'tags' => SCHEMA_DIR . '/tags.sql',
            'elements' => SCHEMA_DIR . '/elements.sql',
            'item_types_elements' => SCHEMA_DIR . '/item_types_elements.sql',
            'options' => SCHEMA_DIR . '/options.sql',
            'users' => SCHEMA_DIR . '/users.sql',
            'element_sets' => SCHEMA_DIR . '/element_sets.sql',
            'files' => SCHEMA_DIR . '/files.sql',
            'items' => SCHEMA_DIR . '/items.sql',
            'plugins' => SCHEMA_DIR . '/plugins.sql',
            'records_tags' => SCHEMA_DIR . '/records_tags.sql',
            'users_activations' => SCHEMA_DIR . '/users_activations.sql',
            'sessions' => SCHEMA_DIR . '/sessions.sql',
            'search_texts' => SCHEMA_DIR . '/search_texts.sql',
            'keys' => SCHEMA_DIR . '/keys.sql'
        ), $this->schemaTask->getTables());
    }
        
    public function testAddTables()
    {
        $expectedTables = array(
            'collections' => SCHEMA_DIR . '/collections.sql',
            'items' => SCHEMA_DIR . '/items.sql'
        );
        $this->schemaTask->addTables($expectedTables);
        $this->assertEquals($expectedTables, $this->schemaTask->getTables());
    }
    
    public function testSetTables()
    {
        $expectedTables = array(
            'collections' => SCHEMA_DIR . '/collections.sql',
            'items' => SCHEMA_DIR . '/items.sql'
        );
        $this->schemaTask->setTables($expectedTables);
        $this->assertEquals($expectedTables, $this->schemaTask->getTables());
    }
    
    public function testRemoveTable()
    {
        $someTables = array(
            'collections' => SCHEMA_DIR . '/collections.sql',
            'items' => SCHEMA_DIR . '/items.sql'
        );
        $this->schemaTask->addTables($someTables);
        $this->schemaTask->removeTable('collections');
        $this->assertEquals(array(
            'items' => $someTables['items']
        ), $this->schemaTask->getTables());
    }
    
    public function testInstallFailsWithNoTables()
    {
        $task = new Installer_Task_Schema();        
        try {
            $task->install($this->db);
            $this->fail("Task should have thrown an exception when not given a valid schema file.");
        } catch (Exception $e) {
            $this->assertContains("No SQL files were given to create the schema.", $e->getMessage());
        }
    } 
    
    public function testInstall()
    {
        $task = new Installer_Task_Schema();
        $schemaFile = dirname(__FILE__) . '/_files/schema.sql';
        $task->addTable('test_table', $schemaFile);
        $task->install($this->db);
        $this->profilerHelper->assertDbQuery("CREATE TABLE `test_table` (`id` int(11), `name` varchar(20))");
    }
    
    public function testLoadsDefaultOmekaSchema()
    {
        $task = new Installer_Task_Schema();
        $task->useDefaultTables();
        $task->install($this->db);
        $expectedTables = array(
            'test_collections',
            'test_elements',
            'test_element_sets',
            'test_element_texts',
            'test_files',
            'test_items',
            'test_item_types',
            'test_item_types_elements',
            'test_options',
            'test_plugins',
            'test_processes',
            'test_tags',
            'test_records_tags',
            'test_users',
            'test_users_activations'
        );
        foreach ($expectedTables as $tableName) {
            $this->profilerHelper->assertDbQuery("CREATE TABLE IF NOT EXISTS `$tableName`");
        }        
    }
}
