<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Add a 'tag_delimiter' option.
 * 
 * @package Omeka\Db\Migration
 */
class addTagDelimiterOption extends Omeka_Db_Migration_AbstractMigration
{
    public function up()
    {
        set_option('tag_delimiter', ',');
    }
}
