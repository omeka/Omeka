<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2011
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Add a 'display_system_info' option.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2011
 */
class addSystemInfoOption extends Omeka_Db_Migration
{
    public function up()
    {
        set_option('display_system_info', true);
    }
}
