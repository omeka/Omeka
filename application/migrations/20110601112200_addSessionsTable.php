<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2011
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Add a 'sessions' table.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2011
 */
class addSessionsTable extends Omeka_Db_Migration
{
    public function up()
    {
        $this->db->execBlock(<<<SQL
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
