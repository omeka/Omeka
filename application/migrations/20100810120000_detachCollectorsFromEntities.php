<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Removes all associations between Entities and Collections.
 * 
 * @package Omeka\Db\Migration
 */
class detachCollectorsFromEntities extends Omeka_Db_Migration_AbstractMigration
{
    
    public function up()
    {
        $this->_migrateCollections($this->getDb());
    }
    
    public function down()
    {
        throw new RuntimeException("Cannot reverse this migration.");
    }
    
    private function _migrateCollections($db)
    {
        $db->queryBlock(<<<COL
ALTER TABLE `$db->Collection` ADD `added` TIMESTAMP NOT NULL default '0000-00-00 00:00:00';
ALTER TABLE `$db->Collection` ADD `modified` TIMESTAMP NOT NULL;   
ALTER TABLE `$db->Collection` ADD `owner_id` INT( 10 ) UNSIGNED NOT NULL;
ALTER TABLE `$db->Collection` ADD INDEX ( `owner_id` );
ALTER TABLE `$db->Collection` ADD `collectors` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL AFTER `description` ;
COL
);
        $this->_setCollectionAddedStamps($db);
        $this->_setCollectionModifiedStamps($db);
        $this->_setCollectionOwnerIds($db);
        $this->_setCollectionCollectors($db);
    }
    
    private function _setCollectionAddedStamps($db)
    {
        // Get all the metadata for `added` timestamps.
        $addedTimestamps = $db->fetchAll(<<<ADDED
SELECT er.relation_id as collection_id, er.time as `added`
FROM $db->EntitiesRelations er 
INNER JOIN $db->EntityRelationships err ON err.id = er.relationship_id AND err.name = "added" 
WHERE er.type = "Collection"
GROUP BY collection_id
ORDER BY added DESC            
ADDED
);
        foreach ($addedTimestamps as $timestamp) {
            $db->update("$db->Collection", array('added' => $timestamp['added']), 'id = ' . (int)$timestamp['collection_id']);
        }
    }

    private function _setCollectionModifiedStamps($db)
    {
        // Same for modified timestamps.
        $modifiedTimestamps = $db->fetchAll(<<<MOD
SELECT er.relation_id as collection_id, er.time as `modified`
FROM $db->EntitiesRelations er 
INNER JOIN $db->EntityRelationships err ON err.id = er.relationship_id AND err.name = "modified" 
WHERE er.type = "Collection"       
GROUP BY collection_id
ORDER BY modified DESC     
MOD
);       
        foreach ($modifiedTimestamps as $timestamp) {
            $db->update("$db->Collection", array('modified' => $timestamp['modified']), 'id = ' . (int)$timestamp['collection_id']);
        }
    }

    private function _setCollectionOwnerIds($db)
    {
        // Same for the ID of the user who added the collection.
        $ownerIds = $db->fetchAll(<<<OWNERS
SELECT er.relation_id as collection_id, u.id as owner_id
FROM $db->EntitiesRelations er 
INNER JOIN $db->EntityRelationships err ON err.id = er.relationship_id AND err.name = "added" 
INNER JOIN $db->Entity e ON e.id = er.entity_id
INNER JOIN $db->User u ON u.entity_id = e.id
WHERE er.type = "Collection"
GROUP BY collection_id
OWNERS
);
        foreach ($ownerIds as $owner) {
            $db->update("$db->Collection", array('owner_id' => $owner['owner_id']), 'id = ' . (int)$owner['collection_id']);
        }     
    }

    /**
     * Set the collectors into a serialized array in the 'collectors' column.
     */
    private function _setCollectionCollectors($db)
    {
        $collectors = $db->fetchAll(<<<COLLECTORS
SELECT CONCAT_WS(' ', e.first_name, e.middle_name, e.last_name) AS name, e.institution AS institution, er.relation_id as collection_id
FROM $db->EntitiesRelations er 
INNER JOIN $db->EntityRelationships err ON err.id = er.relationship_id AND err.name = "collector" 
INNER JOIN $db->Entity e ON e.id = er.entity_id
WHERE er.type = "Collection"            
COLLECTORS
);
        $indexedCollectors = array();
        foreach ($collectors as $collector) {
            $name = trim($collector['name']);
            $institution = trim($collector['institution']);

            // If we have a name and institution, include both, with
            // institution in parens. If only one or the other exists, include
            // only that one.  If neither is there, discard the collector.
            if (!empty($name)) {
                $newCollector = $name;
                if (!empty($institution)) {
                    $newCollector .= " ($institution)";
                }
            } else {
                if (!empty($institution)) {
                    $newCollector = $institution;
                } else {
                    $newCollector = null;
                }
            }

            if ($newCollector) {
                $indexedCollectors[(int)$collector['collection_id']][] = $newCollector;
            }
        }

        foreach ($indexedCollectors as $collectionId => $collectorArray) {
            $db->update($db->Collection, array('collectors' => implode("\n", $collectorArray)), 'id = ' . (int)$collectionId);
        }    
    }
}
