<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

require_once 'CollectionPermissions.php';
require_once 'CollectionTable.php';
require_once 'PublicFeatured.php';

/**
 * @package Omeka
 * @subpackage Models
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class Collection extends Omeka_Record
{        
    public $name;
    public $description = '';
    public $public = 0;
    public $featured = 0;
    
    protected $_related = array('Collectors' => 'getCollectors');
    
    private $_oldCollectorsToAdd = array(); 
    private $_newCollectorsToAdd = array();
    
    protected function _initializeMixins()
    {
        $this->_mixins[] = new Relatable($this);
        $this->_mixins[] = new PublicFeatured($this);
    }
    
    /**
     * Returns whether or not the collection has collectors
     * 
     * @return boolean
     **/
    public function hasCollectors()
    {
        $db = $this->getDb();
        $id = (int) $this->id;
                
        $sql = "
        SELECT COUNT(er.entity_id) 
        FROM $db->EntitiesRelations er 
        INNER JOIN $db->EntityRelationships err 
        ON err.id = er.relationship_id
        WHERE er.relation_id = ? 
        AND er.type = 'Collection' 
        AND err.name = 'collector'";

        $count = $db->fetchOne($sql, array($id));
        
        return $count > 0;    
    }
    
    public function totalItems()
    {
        // This will query the ItemTable for a count of all items associated with 
        // the collection
        return $this->getDb()->getTable('Item')->count(array('collection' => $this->id));
    }
    
    public function getCollectors()
    {
        return ($this->exists()) ? $this->getRelatedEntities('collector') : array();
    }

    protected function _validate()
    {
        if (empty($this->name)) {
            $this->addError('name', 'Collection must be given a valid name.');
        }
        
        if (strlen($this->name) > 255) {
            $this->addError('name', 'Collection name must be less than 255 characters.');
        }
    }
    
    /**
     * Remove a collector's name from being associated with the collection.
     * 
     * @param Entity|integer
     * @return boolean Was successful or not.
     **/
    public function removeCollector($collector)
    {
        $result = $this->removeRelatedTo($collector, 'collector', 1);
        return $result->rowCount() == 1;
    }
    
    protected function afterSaveForm($post)
    {
        // Process the collectors that have been provided on the form
        $collectorsPost = $post['collectors'];
        
        foreach ($collectorsPost as $k => $c) {
            
            if (!empty($c)) {
                
                // Numbers mean that an entity_id has been passed, so add the 
                // relation
                if (is_numeric($c)) {
                    $entity_id = $c;
                    $this->addRelatedIfNotExists($entity_id, 'collector');
                } else {
                    //@todo Add support for entering a string name (this is 
                    // thorny b/c of string splitting and unicode)
                    throw new Omeka_Record_Exception( 'Cannot enter a collector by name.' );
                }
            }
        }
    }
    
    /**
     * Adds a collector
     * 
     * @param Entity|integer|array of entity properties $collector
     * 
     * You can specify collectors in several ways.
     *
     * You can provide an array of entity properties:
     * <code>
     * insert_collection(array('collectors'=>array(
     *   array('first_name' => $entityFirstName1,
     *         'middle_name' => $entityMiddleName1, 
     *         'last_name' => $entityLastName1,
     *          ...
     *         ),
     *   array('first_name' => $entityFirstName2,
     *         'middle_name' => $entityMiddleName2, 
     *         'last_name' => $entityLastName2,
     *         ...
     *         ),
     *   array(...),
     *   ...
     * ));
     * </code>
     *
     * Alternatively, you can use an array of entity objects or entity ids.
     *
     *  insert_collection(array('collectors'=>array($entity1, $entity2, ...));
     *  insert_collection(array('collectors'=>array($entityId1, $entityId2, ...));
     *
     * Also you can mix the parameters:
     *
     * <code>
     * insert_collection(array('collectors'=>array(
     *    array('first_name' => $entityFirstName1,
     *         'middle_name' => $entityMiddleName1, 
     *         'last_name' => $entityLastName1,
     *          ...
     *         ),
     *   $entity2,
     *   $entityId3,
     *   ...
     * ));
     * </code> 
     *
     * @return void
     **/
    public function addCollector($collector)
    {
        if (is_int($collector)) {
            $collector = $this->getTable('Entity')->find($collector);
            $this->_oldCollectorsToAdd[] = $collector;
        } else if (is_array($collector)) {
            $collectorMetadata = $collector;
            // get the collector if it is already in the database, else create a new one
            if (!array_key_exists('id', $collectorMetadata)) {
                if ($collector = $this->getDb()->getTable('Entity')->findUnique($collectorMetadata)) {
                    $this->_oldCollectorsToAdd[] = $collector;
                } else {
                    $collector = new Entity;
                    $collector->setArray($collectorMetadata);
                    $this->_newCollectorsToAdd[] = $collector;
                }
            } else {
                $collector = $this->getTable('Entity')->find($collectorMetadata['id']);
                $this->_oldCollectorsToAdd[] = $collector;
            }
        } else if ($collector instanceof Entity){
            $this->_oldCollectorsToAdd[] = $collector;
        } else {
            throw new Omeka_Record_Exception('Cannot add collector because invalid collector object.');
        }
    }
    
    /**
     * Validates the added collectors, adding validation errors if required.
     * 
     * @return void
     **/
    protected function beforeValidate()
    {
        // Collectors should all be Entity records.
        $collectorsToAdd = array_merge($this->_newCollectorsToAdd, $this->_oldCollectorsToAdd);
        
        foreach ($collectorsToAdd as $collector) {
            if (!$collector->isValid()) {
                $this->addError('Collector', $collector->getErrors());
            }
        }	    
    }
    
    /**
     * Saves collectors which are new to the database, but if there is an exception, 
     * it removes the new collectors before throwing the exception.
     * 
     * @return void
     **/
    protected function beforeSave()
    {
        // Save all the new collectors before saving the collection.
        try {
            foreach ($this->_newCollectorsToAdd as $key => $collector) {
	            $collector->forceSave();
	        }
        } catch (Exception $e) {
            // If something went wrong, delete and forget all of the new collectors
            $this->_deleteNewCollectorsToAdd();
            throw $e;
        }
    }

    /**
     * Relates the new collectors to the collection, but if there is an exception, 
     * it deletes all of the new collectors before throwing the exception.
     * 
     * @return void
     **/
    protected function afterSave()
    {
        // Add the collectors to the collection
        $collectorsToAdd = array_merge($this->_newCollectorsToAdd, $this->_oldCollectorsToAdd);        
        foreach ($collectorsToAdd as $key => $collector) {
            try {
                $this->addRelatedTo($collector, 'collector');
            } catch (Exception $e) {                
                $this->_deleteNewCollectorsToAdd();
                throw $e;
            }
        }
        
        // Remove collectors to add if all collectors were successfully added
        $this->_newCollectorsToAdd = array();
        $this->_oldCollectorsToAdd = array();
    }
    
    /**
     * Deletes all of the new collectors to add
     * 
     * @return void
     **/
    private function _deleteNewCollectorsToAdd() 
    {
        foreach ($this->_newCollectorsToAdd as $newCollector) {
            $newCollector->delete();
        }
    }
}