<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Add a 'display_system_info' option.
 * 
 * @package Omeka\Db\Migration
 */
class addSystemInfoOption extends Omeka_Db_Migration_AbstractMigration
{
    public function up()
    {
        set_option('display_system_info', true);
    }
}
