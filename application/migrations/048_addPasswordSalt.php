<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * Adds a salt for all the user passwords in Omeka.
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2007-2010
 **/
class addPasswordSalt extends Omeka_Db_Migration
{
    
    public function up()
    {
        $this->db->query("ALTER TABLE `{$this->db->User}` ADD `salt` VARCHAR( 16 ) 
            CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL AFTER `password` ;");
    }
}
