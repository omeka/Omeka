<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * Manages migrating up or down.  Partially ported from Ruby on Rails.
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2007-2010
 **/
class Omeka_Db_Migration_Manager
{
    /**
     * @var Omeka_Db
     */
    private $_db;
    
    /**
     * @var string
     */
    private $_migrationsDir;
    
    const MIGRATION_TABLE_NAME = 'schema_migrations';
    
    /**
     * @var string Formatting string to convert dates into YYYYMMDDHHMMSS pattern.
     */
    // const MIGRATION_DATE_FORMAT = '%Y%m%d%H%M%S';
    const MIGRATION_DATE_FORMAT = "YmdHis";
    
    const ORIG_MIGRATION_OPTION_NAME = 'migration';
    
    const VERSION_OPTION_NAME = 'omeka_version';
    
    public function __construct(Omeka_Db $db, $migrationsDir)
    {
        $this->_db = $db;
        $this->_migrationsDir = $migrationsDir;
    }

    public static function createMigrationsTable(Omeka_Db $db)
    {
        $tableSql = "CREATE TABLE IF NOT EXISTS `$db->prefix" . self::MIGRATION_TABLE_NAME 
                . "` (`version` varchar(16) NOT NULL, UNIQUE KEY `unique_schema_migrations` (`version`))
                    ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        $optionSql = "DELETE FROM $db->Option WHERE name = '" . self::ORIG_MIGRATION_OPTION_NAME . "' LIMIT 1";
        $db->query($optionSql);
        $db->query($tableSql);
        $db->insert('Option', array('name' => self::VERSION_OPTION_NAME, 'value' => OMEKA_VERSION));
    }
    
    /**
     * Migrate the database schema.
     * 
     * @param string $endTimestamp Optional Timestamp corresponding to the stop point for
     * the migration.  If older than the current time, database will migrate down
     * to that point.  If newer, the opposite.  Defaults to the current timestamp.
     */
    public function migrate($endTimestamp = null)
    {
        ini_set('max_execution_time', 0);
        
        $stop = new DateTime($endTimestamp);        
        $direction = 'up';
        
        if ($direction == 'up') {
            $this->_migrateUp($stop);
        } else {
            $this->_migrateDown($stop);
        }
    }
    
    public function canUpgrade()
    {
       $pendingMigrations = $this->_getPendingMigrations(new DateTime());
       return !empty($pendingMigrations);
    }
    
    public function dbNeedsUpgrade()
    {
        return get_option(self::VERSION_OPTION_NAME) 
            && version_compare(get_option(self::VERSION_OPTION_NAME), OMEKA_VERSION, '<')
            && $this->canUpgrade();
    }
    
    public static function factory($db = null)
    {
        if (!$db) {
            $db = Omeka_Context::getInstance()->getDb();
        }
        return new self($db, UPGRADE_DIR);
    }
                    
    /**
     * Retrieve all the versions that have been migrated.
     */
    private function _getAllMigratedVersions()
    {
        // In Rails:
        // table = Arel::Table.new(schema_migrations_table_name)
        // Base.connection.select_values(table.project(table['version']).to_sql).map(&:to_i).sort
        $col = $this->_db->fetchCol("SELECT version FROM " . $this->_getMigrationTableName());
        return $col;
    }
    
    private function _getMigrationTableName()
    {
        return $this->_db->prefix . self::MIGRATION_TABLE_NAME;
    }
    
    private function _getMigrationFileList()
    {
        // In Ruby, you can do this:
        // files = Dir["#{@migrations_path}/[0-9]*_*.rb"]
        $dirIter = new VersionedDirectoryIterator($this->_migrationsDir, false);
        $regexIter = new RegexIterator($dirIter, '/([0-9]*)_.*\.php/', RegexIterator::ALL_MATCHES);        
        $fileList = array();
        foreach ($regexIter as $key => $match) {
            $fileList[$match[1][0]] = $match[0][0];
        }        
        return $fileList;
    }
            
    private function _migrateUp($stopAt)
    {        
        $pending = $this->_getPendingMigrations($stopAt);
        foreach ($pending as $time => $filename) {
            $migration = $this->_loadMigration($filename);
            $migration->up();
            $this->_recordMigration($time);
        }
    }
    
    /**
     * Require the migration file and return an instance of the class associated
     * with it.
     */
    private function _loadMigration($filename)
    {
        $filePath = $this->_migrationsDir . DIRECTORY_SEPARATOR . $filename;
        require_once $filePath;
	    if (!preg_match('/^\d{14}_(\w+)\.php$/', $filename, $match)) {
	        throw new Omeka_Db_Migration_Exception("Migration file '$filename' does not follow proper naming conventions.");
	    }
	    $class = $match[1];
	    if (!class_exists($class)) {
	        throw new Omeka_Db_Migration_Exception("Migration file '$filename' does not contain class '$class'.");
	    }
	    return new $class($this->_db);	    
    }
    
    /**
     * Retrieve a list of all migrations that have not been run yet, ending at
     * the latest time given by $untilTimestamp.
     */
    private function _getPendingMigrations($until)
    {
        $stopAt = $until->format(self::MIGRATION_DATE_FORMAT);
        $files = $this->_getMigrationFileList();
        $migrated = $this->_getAllMigratedVersions();
        $pending = array_diff_key($files, array_flip($migrated));
        // Now remove from this list any migrations that are too new.
        foreach ($pending as $time => $filename) {
            // Too big to use int.
            if ((double)$time > (double)$stopAt) {
                unset($pending[$time]);
            }
        }
        ksort($pending, SORT_NUMERIC);
        return $pending;
    }
    
    private function _recordMigration($time)
    {
        $this->_db->getAdapter()->insert($this->_getMigrationTableName(), array('version' => $time));
    }
}
