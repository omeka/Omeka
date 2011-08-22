<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2011
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Add a 'tag_delimiter' option.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2011
 */
class addTagDelimiterOption extends Omeka_Db_Migration
{
    public function up()
    {
        set_option('tag_delimiter', ',');
    }
}
