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
class TimestampMigrationConversionTest extends Omeka_Test_AppTestCase
{
    public function setUp()
    {
        $this->core = new Omeka_Core('testing', array(
            'config' => CONFIG_DIR . '/' . 'application.ini'));
            
        $this->db = $this->core->getBootstrap()->bootstrap('Db')->db;
        
        $this->db->query("DELETE FROM omeka_options WHERE name = 'omeka_version' LIMIT 1");
        $this->db->query("INSERT INTO omeka_options (name, value) VALUES ('migration', '47')");
        $this->db->query("DROP TABLE omeka_schema_migrations");    
    }
    
    public function assertPreConditions()
    {
        $this->assertNotNull($this->db->fetchOne("SELECT value FROM omeka_options WHERE name = 'migration'"),
                             "There should be a 'migration' option in the database.");
        $this->assertFalse($this->db->fetchOne("SELECT value FROM omeka_options WHERE name = 'omeka_version'"),
                             "There should not be an 'omeka_version' option in the database.");
        $this->assertEquals($this->db->fetchCol("SHOW TABLES LIKE 'omeka_schema_migrations'"), array(),
                            "There should not be an 'omeka_schema_migrations' table.");                     
    }
    
    public function testTimestampSchemaMigration()
    {
        $this->core->getBootstrap()->bootstrap('Options');
        $this->assertFalse($this->db->fetchOne("SELECT value FROM omeka_options WHERE name = 'migration'"),
                             "There should not be a 'migration' option in the database.");
        $this->assertEquals($this->db->fetchOne(
            "SELECT value FROM omeka_options WHERE name = 'omeka_version'"
        ), 
        '',
        "There should be an empty string for 'omeka_version' that signals the " .
        "need to continue upgrading the database.");
        $this->assertEquals($this->db->fetchCol("SHOW TABLES LIKE 'omeka_schema_migrations'"), array('omeka_schema_migrations'),
                            "There should be an 'omeka_schema_migrations' table.");    
    }
}
