<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Adds a salt for all the user passwords in Omeka.
 * 
 * @package Omeka\Db\Migration
 */
class addPasswordSalt extends Omeka_Db_Migration_AbstractMigration
{
    
    public function up()
    {
        // The migrations will be a bit messed up because of the conversion to 
        // using timestamps.  Check if the salt column already exists before 
        // attempting to alter.
        
        $columns = $this->db->fetchAll("DESCRIBE `{$this->db->prefix}users`");
        foreach ($columns as $col) {
            if ($col['Field'] == 'salt') {
                return;
            }
        }
        
        $this->db->query("ALTER TABLE `{$this->db->prefix}users` ADD `salt` VARCHAR( 16 ) 
            CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL AFTER `password` ;");
    }
}
