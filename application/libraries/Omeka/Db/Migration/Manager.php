<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Manages database migrations (both upgrades and downgrades).
 * 
 * @package Omeka\Db\Migration
 */
class Omeka_Db_Migration_Manager
{
    /**
     * @var Omeka_Db
     */
    private $_db;

    /**
     * Directory where migrations scripts are kept.
     *
     * @var string
     */
    private $_migrationsDir;

    /**
     * Name of the migrations table.
     */
    const MIGRATION_TABLE_NAME = 'schema_migrations';

    /**
     * Formatting string to convert dates into YYYYMMDDHHMMSS pattern.
     */
    const MIGRATION_DATE_FORMAT = "YmdHis";

    /**
     * Name of the original database option storing the integer migration number.
     */
    const ORIG_MIGRATION_OPTION_NAME = 'migration';

    /**
     * Name of the new database option storing the core software version number.
     */
    const VERSION_OPTION_NAME = 'omeka_version';

    /**
     * @param Omeka_Db $db
     * @param string $migrationsDir
     */
    public function __construct(Omeka_Db $db, $migrationsDir)
    {
        $this->_db = $db;
        $this->_migrationsDir = $migrationsDir;
    }

    /**
     * Set up Omeka to use timestamped database migrations.
     *
     * This creates the 'schema_migrations' table, drops the 'migration' option
     * and adds the 'omeka_version' option to the database.
     *
     * @return void
     */
    public function setupTimestampMigrations()
    {
        $db = $this->_db;
        $tableSql = "CREATE TABLE IF NOT EXISTS `$db->prefix" . self::MIGRATION_TABLE_NAME
                . "` (`version` varchar(16) NOT NULL, UNIQUE KEY `unique_schema_migrations` (`version`))
                    ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        $optionSql = "DELETE FROM $db->Option WHERE name = '" . self::ORIG_MIGRATION_OPTION_NAME . "' LIMIT 1";
        $db->query($optionSql);
        $db->query($tableSql);
        // Setting an empty value ensures that the database is flagged as
        // needing an upgrade.
        $db->insert('Option', array('name' => self::VERSION_OPTION_NAME, 'value' => ''));
    }

    /**
     * Mark all of the migrations as having been run.  Used by the installer as
     * a way of indicating that the database is entirely up to date.
     *
     * @return void
     */
    public function markAllAsMigrated()
    {
        $pending = $this->_getPendingMigrations(new DateTime);
        foreach ($pending as $time => $migration) {
            $this->_recordMigration($time);
        }
    }

    /**
     * Migrate the database schema.
     *
     * @param string $endTimestamp (optional) Timestamp corresponding to the stop
     * point for the migration.  If older than the current time, database will
     * migrate down to that point.  If newer, the opposite.  Defaults to the
     * current timestamp.
     * @return void
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

    /**
     * Determine whether or not it is possible to migrate the Omeka database up.
     *
     * This is based entirely on whether there exist any migrations that have
     * not yet been applied.
     *
     * @return void
     */
    public function canUpgrade()
    {
       $pendingMigrations = $this->_getPendingMigrations(new DateTime());
       return !empty($pendingMigrations);
    }

    /**
     * Determine whether the database must be upgraded.
     *
     * In order to return true, this requires that canUprade() == true, and also
     * that Omeka's code has recently been upgraded.
     */
    public function dbNeedsUpgrade()
    {
        $omekaVersion = get_option(self::VERSION_OPTION_NAME);
        if (!$omekaVersion || version_compare($omekaVersion, OMEKA_VERSION, '<')) {
            if ($this->canUpgrade()) {
                return true;
            } else {
                // This version has no migrations, just update the stored version.
                $this->finalizeDbUpgrade();
            }
        }

        return false;
    }

    /**
     * Finalize the database upgrade by setting the most up-to-date version
     * of Omeka.
     */
    public function finalizeDbUpgrade()
    {
        set_option(self::VERSION_OPTION_NAME, OMEKA_VERSION);
    }

    /**
     * Return the default configuration of the database migration manager.
     *
     * @param Omeka_Db|null $db
     * @return Omeka_Db_Migration_Manager
     */
    public static function getDefault($db = null)
    {
        if (!$db) {
            $db = Zend_Registry::get('bootstrap')->getResource('Db');
        }
        return new self($db, UPGRADE_DIR);
    }

    /**
     * Retrieve all the versions that have been migrated.
     *
     * @return array
     */
    private function _getAllMigratedVersions()
    {
        // In Rails:
        // table = Arel::Table.new(schema_migrations_table_name)
        // Base.connection.select_values(table.project(table['version']).to_sql).map(&:to_i).sort
        $col = $this->_db->fetchCol("SELECT version FROM " . $this->_getMigrationTableName());
        return $col;
    }

    /**
     * Return the name of the table associated with schema migrations.
     *
     * @return string
     */
    private function _getMigrationTableName()
    {
        return $this->_db->prefix . self::MIGRATION_TABLE_NAME;
    }

    /**
     * Return a list of migration files in the migration directory.
     *
     * @return array An associative array where key = timestamp of migration,
     * value = full filename of the migration.
     */
    private function _getMigrationFileList()
    {
        // In Ruby, you can do this:
        // files = Dir["#{@migrations_path}/[0-9]*_*.rb"]
        $dirIter = new VersionedDirectoryIterator($this->_migrationsDir, false);
        $regexIter = new RegexIterator($dirIter, '/^(\d*)_.*\.php$/', RegexIterator::ALL_MATCHES);
        $fileList = array();
        foreach ($regexIter as $key => $match) {
            if (isset($match[1][0]) && isset($match[0][0])) {
                $fileList[$match[1][0]] = $match[0][0];
            }
        }
        return $fileList;
    }

    /**
     * Migrate upwards to a specific timestamp.
     *
     * @param DateTime $stopAt
     * @return void
     */
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
     *
     * @param string $filename Migration script filename.
     * @return Omeka_Db_Migration_AbstractMigration
     */
    private function _loadMigration($filename)
    {
        if (!preg_match('/^\d{14}_(\w+)\.php$/', $filename, $match)) {
            throw new Omeka_Db_Migration_Exception("Migration file '$filename' does not follow proper naming conventions.");
        }
        $filePath = $this->_migrationsDir . '/' . $filename;
        require_once $filePath;
        $class = $match[1];
        if (!class_exists($class)) {
            throw new Omeka_Db_Migration_Exception("Migration file '$filename' does not contain class '$class'.");
        }
        $inst = new $class;
        $inst->setDb($this->_db);
        return $inst;
    }

    /**
     * Retrieve a list of all migrations that have not been run yet, ending at
     * the latest time given by $until.
     *
     * @param DateTime $until
     * @return array
     */
    private function _getPendingMigrations(DateTime $until)
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
            // Not exactly sure why, but sometimes empty migrations get into this list.
            if (empty($time)) {
                unset($pending[$time]);
            }
        }
        ksort($pending, SORT_NUMERIC);
        return $pending;
    }

    /**
     * Record the migration timestamp in the schema_migrations table.
     *
     * @param string $time
     * @return void
     */
    private function _recordMigration($time)
    {
        $this->_db->getAdapter()->insert($this->_getMigrationTableName(), array('version' => $time));
    }
}
