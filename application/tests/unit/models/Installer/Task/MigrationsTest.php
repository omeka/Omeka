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
class Installer_Task_MigrationsTest extends PHPUnit_Framework_TestCase
{
    const DB_PREFIX = 'test_';
    
    public function setUp()
    {
        $this->dbAdapter = new Zend_Test_DbAdapter;
        $this->db = new Omeka_Db($this->dbAdapter, self::DB_PREFIX);
        $this->profiler = $this->dbAdapter->getProfiler();
    }
        
    public function testCreatesMigrationSchema()
    {
        $task = new Installer_Task_Migrations();
        $task->install($this->db);        
        // Queries that this task runs (via Omeka_Db_Migration_Manager):
        // 
        // Delete existing 'migration' option
        // Create 'schema_migrations' table
        // Insert 'omeka_version' option
        // Select all versions from 'schema_migrations'
        // Insert pre-existing migration versions into schema_migrations.
        $queryProfiles = $this->profiler->getQueryProfiles();
        $this->assertThat($queryProfiles[0], $this->isInstanceOf('Zend_Db_Profiler_Query'),
            "Task should have run at least one database query.");
        // Don't need to test all of Migration_Manager's logic, so detecting that
        // roughly certain queries were run should be fine.
        $ranCreate = false;
        $ranOptionInsert = false;
        $ranVersionInsert = false;
        foreach ($queryProfiles as $profile) {
            if (strstr($profile->getQuery(), "CREATE TABLE IF NOT EXISTS `test_schema_migrations`")) {
                $ranCreate = true;
            } else if (strstr($profile->getQuery(), "INSERT INTO `test_options`")) {
                $ranOptionInsert = true;
            } else if (strstr($profile->getQuery(), "INSERT INTO test_schema_migrations")) {
                $ranVersionInsert = true;
            }
        }
        $this->assertTrue($ranCreate, "Task should have run an SQL query to create the schema_migrations table.");
        $this->assertTrue($ranOptionInsert, "Task should have run an SQL query to insert the 'omeka_version' option.");
        $this->assertTrue($ranVersionInsert, "Task should have run an SQL query to insert versions into the schema_migrations table.");        
    }
}
