<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * @package Omeka\Db\Migration
 */
class editDublinCoreDescription extends Omeka_Db_Migration_AbstractMigration
{
    public function up()
    {
        $dcDescription = 
        'The Dublin Core metadata element set. These elements are common to all ' 
      . 'Omeka records, including items, files, and collections. See ' 
      . 'http://dublincore.org/documents/dces/.';
        
        $sql = "UPDATE {$this->db->ElementSet} SET description = ? WHERE name = ?";
        $this->db->query($sql, array($dcDescription, 'Dublin Core'));
    }
}
