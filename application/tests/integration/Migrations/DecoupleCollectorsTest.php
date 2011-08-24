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
class Omeka_Migrations_DecoupleCollectorsTest extends Omeka_Test_AppTestCase
{
    const MIGRATION_TIMESTAMP = '20100810120000';
    const COLLECTIONS_TABLE = 'omeka_collections';

    public function setUp()
    {
        parent::setUp();    
        $this->db->query("DROP TABLE `" . self::COLLECTIONS_TABLE . "`");
        // Recreate the collections table in its previous state.
        // This will allow the migration to run properly.
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . self::COLLECTIONS_TABLE . "` (
          `id` int(10) unsigned NOT NULL auto_increment,
          `name` varchar(255) collate utf8_unicode_ci NOT NULL,
          `description` text collate utf8_unicode_ci NOT NULL,
          `public` tinyint(1) NOT NULL,
          `featured` tinyint(1) NOT NULL,
          PRIMARY KEY  (`id`),
          KEY `public` (`public`),
          KEY `featured` (`featured`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
    
        $this->db->query("DELETE FROM omeka_schema_migrations WHERE version = ? LIMIT 1", array(self::MIGRATION_TIMESTAMP));
    }

    public function testMigration()
    {
        $migrationManager = Omeka_Db_Migration_Manager::getDefault();
        // If this doesn't throw any exceptions, we can assume that all went well.
        $migrationManager->migrate();
    }

    public function tearDown()
    {
        if ($this->db) {
            $this->db->query("DROP TABLE IF EXISTS `" . self::COLLECTIONS_TABLE . "`");
        }
        parent::tearDown();
    }
}
