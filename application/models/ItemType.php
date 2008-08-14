<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

require_once 'ItemTypeTable.php';

require_once 'ItemTypesElements.php';

/**
 * @package Omeka
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class ItemType extends Omeka_Record { 
    
    public $name;
    public $description = '';

    protected $_related = array('Elements' => 'getElements', 
                                'Items'=>'getItems');

    public function hasElement($name) {
        var_dump('fix me!');exit;
        $db = $this->getDb();
        
        $sql = "
        SELECT COUNT(m.id) 
        FROM $db->Metafield m 
        INNER JOIN $db->TypesMetafields tm 
        ON tm.metafield_id = m.id
        WHERE tm.type_id = ? 
        AND m.name = ?";
        
        $count = (int) $db->fetchOne($sql, array($this->id, $name));
        return $count > 0;
    }
    
    protected function getElements()
    {
        return $this->getTable('Element')->findByItemType($this->id);
    }
    
    protected function getItems()
    {
        return $this->getTable('Item')->findBy(array('type'=>$this->id));
    }
    
    /**
     * Current validation rules for Type
     * 
     * 1) 'Name' field can't be blank
     * 2) 'Name' field must be unique
     *
     * @return void
     **/
    protected function _validate()
    {
        if (empty($this->name)) {
            $this->addError('name', 'Item type name must not be blank.');
        }
        
        if (!$this->fieldIsUnique('name')) {
            $this->addError('name', 'That name has already been used for a different Type.');
        }
    }
    
    /**
     * Delete all the TypesMetafields joins
     *
     * @return void
     **/
    protected function _delete()
    {
        $tm_objs = $this->getDb()->getTable('ItemTypesElements')->findBySql('item_type_id = ?', array( (int) $this->id));
        
        foreach ($tm_objs as $tm) {
            $tm->delete();
        }
    }
    
    /**
     * Remove a single Element from this item type.
     * 
     * @param string
     * @return void
     **/
    public function removeElement($elementId)
    {
        if (!$this->exists()) {
            throw new Exception('Cannot remove elements from an item type that is not persistent in the database!');
        }
        
        // Find the join record and delete it.
        $iteJoin = $this->getTable('ItemTypesElements')->findBySql('ite.element_id = ? AND ite.item_type_id = ?', array($elementId, $this->id), true);
    
        if (!$iteJoin) {
            throw new Exception('Item type does not contain an element with the ID = "' . $elementId . '"!');
        }
        
        return $iteJoin->delete();
    }
    
    /**
     * Post commit hook that will add metafields to a type
     * This occurs post-commit because that ensures that the Type has a valid ID
     *
     * @return void
     **/
    protected function afterSaveForm($post)
    {
        //Add new metafields
        foreach ($post['NewMetafields'] as $key => $mf_array) {
            
            $mf_name = $mf_array['name'];
            
            if (!empty($mf_name)) {
                $mf = $this->getDb()->getTable('Metafield')->findByName($mf_name);
                if (!$mf) {
                    $mf = new Metafield;
                }
                if (!$this->hasElement($mf_name)) {
                    $mf->setArray($mf_array);
                    $this->addMetafield($mf);
                }
            }
        }
        
        //Add new joins for pre-existing metafields
        if (!empty($post['ExistingMetafields'])) {
            foreach ($post['ExistingMetafields'] as $key => $tm_array) {            
                $tm = new TypesMetafields;
                $tm->metafield_id = $tm_array['metafield_id'];
                $tm->type_id = $this->id;
            
                //Save & suppress duplicate key errors
                try {
                    $tm->save();
                } catch (Exception $e) {}
            }            
        }
        $this->loadMetafields();
    }
}