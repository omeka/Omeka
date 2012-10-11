<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Database migration classes may inherit from this one.
 * 
 * @package Omeka\Db\Migration
 */
abstract class Omeka_Db_Migration_AbstractMigration implements Omeka_Db_Migration_MigrationInterface
{
    protected $db;
        
    /**
     * Set the database to migrate.
     * 
     * @param Omeka_Db $db
     * @return void
     */
    public function setDb(Omeka_Db $db)
    {
        $this->db = $db;
    }
    
    /**
     * @return Omeka_Db
     */
    public function getDb()
    {
        return $this->db;
    } 
    
    /**
     * Template method for reversing the migration.
     * 
     * This is defined as a template method instead of leaving it abstract because
     * pre-existing implementations of Omeka_Db_Migration were not required to
     * implement the down() method.  This ensures backwards compatibility for 
     * those migrations. 
     */
    public function down() {}
    
    /**
     * Proxy calls to Omeka_Db.
     *
     * Allows migration writers to call db methods directly on $this.
     *
     * @param string $m Method name.
     * @param array $a Method arguments.
     */
    public function __call($m, $a)
    {
        return call_user_func_array(array($this->getDb(), $m), $a);
    }
        
    /**
     * If the migration requires a form submission, here's where to handle display of it
     * 
     * @return void
     */
    public function form() {}
}
