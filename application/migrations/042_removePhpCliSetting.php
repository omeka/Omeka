<?php
/**
 * Removes the 'path_to_php_cli' site setting.
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2009
 **/
class removePhpCliSetting extends Omeka_Db_Migration
{
    public function up()
    {   
        delete_option('path_to_php_cli');
    }
}
