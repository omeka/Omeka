<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * All database migration classes inherit from this one.
 *
 * While not required, a down() method must be declared within a concrete
 * subclass in order to allow for reversal of database migrations.
 * 
 * @internal This is a pseudo-port of Ruby on Rails' migrations.
 * @abstract
 * @package Omeka
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
abstract class Omeka_Db_Migration
{
    public function __construct($db)
    {
        $this->db = $db;
    }
    
    public function getDb()
    {
        return $this->db;
    } 
    
    //$this->execBlock() instead of $this->getDb()->execBlock()
    
    public function __call($m, $a)
    {
        return call_user_func_array(array($this->getDb(), $m), $a);
    }
    
    abstract public function up();
    
    /**
     * If the migration requires a form submission, here's where to handle display of it
     * 
     * @return void
     **/
    public function form() {}
}
