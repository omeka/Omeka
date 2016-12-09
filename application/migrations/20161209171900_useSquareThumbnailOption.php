<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2016 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * @package Omeka\Db\Migration
 */
class useSquareThumbnailOption extends Omeka_Db_Migration_AbstractMigration
{
    public function up()
    {
        set_option('use_square_thumbnail', '1');
    }
}
