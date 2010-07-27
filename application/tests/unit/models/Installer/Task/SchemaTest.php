<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * 
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2007-2010
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
        
    }
    
    public function testFailsWithoutSchemaFile()
    {
        $task = new Installer_Task_Schema();
        
        try {
            $task->install($this->db);
            $this->fail("Task should have thrown an exception when not given a valid schema file.");
        } catch (Exception $e) {
            $this->assertContains("Schema file was not given.", $e->getMessage());
        }
    }
    
    public function testLoadsSchema()
    {
        $task = new Installer_Task_Schema();
        $schemaFile = dirname(__FILE__) . '/_files/schema.sql';
        $task->setSchemaFile($schemaFile);
        $task->install($this->db);
        $this->profilerHelper->assertDbQuery("CREATE TABLE `test_table` (`id` int(11), `name` varchar(20))");
    }
    
    public function testLoadsDefaultOmekaSchema()
    {
        $task = new Installer_Task_Schema();
        $schemaFile = CORE_DIR . '/schema.sql';
        $task->setSchemaFile($schemaFile);
        $task->install($this->db);
        $lastSchemaQueryTableName = 'taggings';
        $expectedTables = array(
            'test_collections',
            'test_data_types',
            'test_elements',
            'test_element_sets',
            'test_element_texts',
            'test_entities',
            'test_entities_relations',
            'test_entity_relationships',
            'test_files',
            'test_items',
            'test_item_types',
            'test_item_types_elements',
            'test_mime_element_set_lookup',
            'test_options',
            'test_plugins',
            'test_processes',
            'test_record_types',
            'test_tags',
            'test_taggings',
            'test_users',
            'test_users_activations'
        );
        foreach ($expectedTables as $tableName) {
            $this->profilerHelper->assertDbQuery("CREATE TABLE IF NOT EXISTS `$tableName`");
        }        
    }
}