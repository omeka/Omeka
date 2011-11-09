<?php 
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @subpackage Mixins
 * @author CHNM
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @deprecated
 */
class Relatable extends Omeka_Record_Mixin
{
    protected $record;
    
    public function __construct($record)
    {
        $this->record = $record;
        $this->type = get_class($record);
    }

    /**
     * After updating records, add a stamp that the logged-in entity has modified it
     *
     * @return void
     */
    public function afterUpdate()
    {
        $user = Omeka_Context::getInstance()->getCurrentUser();
        if ($user && ($entity = $user->Entity) && ($entity instanceof Entity)) {
            $this->setModifiedBy($entity);
        }        
    }
    
    /**
     * After inserting records, add a stamp that the logged-in entity has added it
     *
     * @return void
     */
    public function afterInsert()
    {
        $user = Omeka_Context::getInstance()->getCurrentUser();
        if ($user && ($entity = $user->Entity) && ($entity instanceof Entity)) {
            $this->setAddedBy($entity);
        }
    }
    
    public function beforeDelete()
    {
        $this->deleteRelations();
    }
    
    public function deleteRelations()
    {
        /**
         * @duplication 
         * @see Taggable::deleteTaggings()
         * @since 9/13/07
         */
        
        $id = (int) $this->record->id;
        
        //What table should we be deleting taggings for
        
        $db = $this->getDb();
        
        //Polymorphic 'type' column in this table
        $type = (string) get_class($this->record);
        
        $model_table = $db->$type;
        
        $er = $db->EntitiesRelations;
        
        $delete = "
        DELETE $er 
        FROM $er
        LEFT JOIN $model_table 
        ON $er.relation_id = $model_table.id
        WHERE $model_table.id = $id 
        AND $er.type = '$type'";
        
        $db->exec($delete);
    }
    
    /**
     * Get the last date the item was modified, etc.
     * 
     * For instance, <code>$record->getLastRelationship('modified');</code>
     * would return the date of the last modification.
     * 
     * @return string|null
     */
    public function timeOfLastRelationship($rel)
    {
        $db = $this->getDb();
        
        $sql = "
        SELECT ie.time as time
        FROM {$db->EntitiesRelations} ie 
        JOIN {$db->EntityRelationships} er 
        ON er.id = ie.relationship_id
        WHERE ie.relation_id = ? 
        AND er.name = ? 
        AND ie.type = ?
        ORDER BY time DESC
        LIMIT 1";
        
        $relation_id = $this->_getRelationId();
                
        return $db->fetchOne($sql, array($relation_id, $rel, $this->type));
    }
    
    /**
     * Retrieve Entity records that have the given relationship with the 
     * current Entity.
     * 
     * For example, <code>$item->getRelatedEntities('modified');</code>
     * would return an array of Entity records corresponding to entities that
     * had modified the item.
     *
     * @return array
     */
    public function getRelatedEntities($rel)
    {
        $db = $this->getDb();
                        
        $sql = "
        SELECT e.* FROM {$db->Entity} e 
        INNER JOIN {$db->EntitiesRelations} r ON r.entity_id = e.id
        INNER JOIN {$db->EntityRelationships} er ON er.id = r.relationship_id
        WHERE r.relation_id = ? AND r.type = ? AND er.name = ? GROUP BY e.id";
        
        $entities = $this->getTable('Entity')->fetchObjects($sql, array($this->_getRelationId(), $this->type, $rel));
        
        return !$entities ? array() : $entities;
    }
    
    /**
     * Adds an relation between the relatable and the entity.
     * 
     * For example, <code>$item->addRelatedTo($user, 'added');</code>.
     * 
     * @param Entity|int $entity
     * @param string $relationship
     * @return boolean
     */
    public function addRelatedTo($entity, $relationship )
    {        
        $entity_id = (int) ($entity instanceof Omeka_Record) ? $entity->id : $entity;        
        
        //If the entity_id is 0, die because that won't work
        if ($entity_id == 0) {
            throw new Omeka_Record_Exception( __('Invalid entity provided!') );
            
            //For now, fail silently because there's no use in bitching about it
            return false;
        }
        
        $relation_id = $this->_getRelationId();
        
        $relationship_id = $this->getRelationshipId($relationship);
        
        if (!$relationship_id) {
            throw new Omeka_Record_Exception( __('Relationship called %s does not exist.', $relationship) );
        }
        
        $er = new EntitiesRelations;
        $er->entity_id = $entity_id;
        $er->relation_id = $relation_id;
        $er->relationship_id = $relationship_id;
        $er->type = $this->type;
        $er->forceSave();
    }
    
    public function removeRelatedTo($entity, $rel, $limit = null)
    {
        $entity_id = ($entity instanceof Omeka_Record) ? $entity->id : $entity;
        
        $relation_id = $this->_getRelationId();
        
        $relationship_id = $this->getRelationshipId($rel);
        
        $limit = (!empty($limit)) ? (int) $limit : null;
        
        $db = $this->getDb();
        
        $sql = "
        DELETE FROM {$db->EntitiesRelations}
        WHERE entity_id = ? 
        AND relation_id = ? 
        AND relationship_id = ? 
        AND type = ?";
        
        if ($limit) {
            $sql .= " LIMIT $limit";
        }
        
        return $db->exec($sql, array($entity_id, $relation_id, $relationship_id, $this->type));
        
    }
    
    protected function getRelationshipId($rel)
    {
        $db = $this->getDb();
        $sql = "
        SELECT r.id 
        FROM {$db->EntityRelationships} r 
        WHERE r.name = ?";
        return $db->fetchOne($sql, array($rel));
    }
    
    private function _getRelationId()
    {
        $id =  $this->record->id;
        
        if (!$id) {
            throw new Omeka_Record_Exception(__('Record must exist before relations can be set.'));
        }
        
        return $id;
    }
    
    public function isRelatedTo($entity_id, $rel=null)
    {
        $db = $this->getDb();
        $select = new Omeka_Db_Select;
        
        $relation_id = $this->_getRelationId();
                
        $db = $this->getDb();        
                
        $select->from(array('ie'=>$db->EntitiesRelations), "COUNT(ie.id)")
               ->joinInner(array('e'=>$db->Entity), "e.id = ie.entity_id", array())
               ->where("ie.relation_id = ?", $relation_id)
               ->where("ie.entity_id = ?", $entity_id)
               ->where("ie.type = ?", $this->type); 
                                        
        if (!empty($rel)) {
            $select->joinInner(array('ier'=>$db->EntityRelationships), 
                               "ier.id = ie.relationship_id", 
                               array());
            $select->where("ier.name = ?", $rel);
        }
        
        $count = $db->fetchOne($select);
                
        return $count > 0;
    }
    
    public function toggleRelatedTo($entity_id, $rel) {
        
        if ($this->isRelatedTo($entity_id, $rel)) {
            $this->removeRelatedTo($entity_id, $rel, 1);
        } else {
            $this->addRelatedTo($entity_id, $rel);
        }
    }    
    
    public function isRelatedToUser($user, $relationship)
    {
        if (!($user instanceof User)) {
            $entity_id = $user;
        } else {
            $entity_id = $user->entity_id;
        }
        
        if (!$this->exists()) {
            return false;
        }
        
        return $this->isRelatedTo($entity_id, $relationship);
    }
        
    public function wasAddedBy($user)
    {
        return $this->isRelatedToUser($user, 'added');
    }
    
    public function wasModifiedBy($user)
    {
        return $this->isRelatedToUser($user, 'modified');
    }
    
    public function isFavoriteOf($user) {
        $entity_id = $user->entity_id;
        return $this->isRelatedTo($entity_id, 'favorite');
    }
    
    public function toggleFavorite($user) {
        $entity_id = $user->entity_id;
        return $this->toggleRelatedTo($entity_id, 'favorite');
    }
    
    public function setAddedBy($entity) {
        return $this->addRelatedTo($entity, 'added');
    }
    
    public function setModifiedBy($entity) {        
        return $this->addRelatedTo($entity, 'modified');
    }
    
    public function addRelatedIfNotExists($entity, $rel) {
        if (!$this->isRelatedTo($entity, $rel)) {
            return $this->addRelatedTo($entity, $rel);
        }
        return false;
    }
}
