<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Migration interface.
 * 
 * @package Omeka\Db\Migration
 */
interface Omeka_Db_Migration_MigrationInterface
{
    public function up();
    public function down();
    public function setDb(Omeka_Db $db);
}
