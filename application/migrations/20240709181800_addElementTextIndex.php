<?php
/**
 * Omeka
 *
 * @copyright Copyright 2007-2020 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Add better index for sorting/filtering element texts by specific element
 *
 * @package Omeka\Db\Migration
 */
class addElementTextIndex extends Omeka_Db_Migration_AbstractMigration
{
    public function up()
    {
        $this->db->query("ALTER TABLE {$this->db->ElementText} ADD INDEX `record_element_text` (`record_type`, `record_id`, `element_id`, `text`(20))");
    }
}
