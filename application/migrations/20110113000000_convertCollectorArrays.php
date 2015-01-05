<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Converts mis-migrated collector arrays to plain strings.
 * 
 * @package Omeka\Db\Migration
 */
class convertCollectorArrays extends Omeka_Db_Migration_AbstractMigration
{
    public function up()
    {
        $db = $this->getDb();
        $collectorColumns = $db->fetchPairs("SELECT `id`, `collectors` FROM `{$db->Collection}`");

        foreach ($collectorColumns as $id => $column) {
            // If we can succesfully unserialize this as an array, convert it
            // back to a string.  Otherwise, leave it alone.
            $array = @unserialize($column);
            if(is_array($array)) {
                $id = (int) $id;
                $db->update($db->Collection, array('collectors' => implode("\n", $array)), "id = $id");
            }
        }
    }
    
    public function down()
    {
        throw new RuntimeException("Cannot reverse this migration.");
    }
} 
