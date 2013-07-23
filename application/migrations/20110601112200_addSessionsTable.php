<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Add a 'sessions' table.
 * 
 * @package Omeka\Db\Migration
 */
class addSessionsTable extends Omeka_Db_Migration_AbstractMigration
{
    public function up()
    {
        $this->db->queryBlock(<<<SQL
CREATE TABLE IF NOT EXISTS `{$this->db->prefix}sessions` (
    `id` char(32),
    `modified` int,
    `lifetime` int,
    `data` text,
    PRIMARY KEY (`id`)
) ENGINE=InnoDb;
SQL
        );
    }
}
