<?php

/**
* 
*/
class ElementTable extends Omeka_Db_Table
{
    /**
     * Retrieve a set of Element records for the item. This set of records
     * will be indexed by the name of the element.
     * 
     * @param integer ID of the item
     * @return array
     **/
    public function findByItem($id)
    {
        $select = $this->getSelectForItem($id);
        $objs = $this->fetchObjects($select);
        return $this->indexRecordsByName($objs);
    }
    
    /**
     * Overriding getSelect() to always return the type_name and type_regex
     * for retrieved elements.
     * 
     * @return Omeka_Db_Select
     **/
    public function getSelect()
    {
        $select = parent::getSelect();
        $db = $this->getDb();
        $select->joinLeft(array('et'=>$db->ElementType), 'et.id = e.element_type_id', 
            array('type_name'=>'et.name', 'type_regex'=>'et.regular_expression'));
        return $select;
    }
    
    public function getSelectForItem($itemId)
    {
        $select = $this->getSelect();
        $db = $this->getDb();
        
        $select->joinInner(array('il' => $db->ItemsElements), 
                           'il.element_id = e.id', 
                           array('il.text'));
        $select->joinInner(array('i' => $db->Item), 
                           'il.item_id = i.id', 
                           array());
        
        $select->where('i.id = ?', $itemId);
        
        return $select;       
    }
    
    /**
     * Index a set of Elements based on their name.
     * 
     * @param array
     * @return array
     **/
    protected function indexRecordsByName(array $objs)
    {
        $indexed = array();
        foreach($objs as $obj) {
            $indexed[$obj->name][] = $obj;
        }
        
        return $indexed;        
    }
    
    /**
     * Retrieve the names of all the elements for a given Item Type
     * 
     * @see item_type_elements()
     * @param integer
     * @return array
     **/
    public function findNamesByItemType($itemTypeId)
    {
        //Retrieve dummy data
        return array('Physical Dimensions', 'Original Transcript');
    }
    
    /**
     * Return the element's name and id for <select> tags on it.
     * 
     * @see Omeka_Db_Table::findPairsForSelectForm()
     * @param string
     * @return void
     **/
    protected function _getColumnPairs()
    {
        return array('e.id', 'e.name');
    }
    
    /**
     * Overridden to natsort() the columns.
     * 
     * @return array
     **/
    public function findPairsForSelectForm()
    {
        $pairs = parent::findPairsForSelectForm();
        natsort($pairs);
        return $pairs;
    }
    
    /**
     * Retrieve all elements for a set (containing text only for the item)
     * 
     * @see items/form.php
     * @see display_form_input_for_element()
     * @param Item
     * @param string The name of the set it belongs to.
     * @return Element
     **/
    public function findForItemBySet($item, $elementSet)
    {
        // Select all the elements for a given set
        $select = $this->getSelect();
        $db = $this->getDb();
        
        // Join on the element_sets table
        $select->joinInner(array('es'=>$db->ElementSet), 'es.id = e.element_set_id', array());
        $select->where('es.name = ?', (string) $elementSet);
        
        $elements = $this->fetchObjects($select);
       
       // Populate those element records with the values for a given item
       
       // Get the IDs of all the elements we pulled (no need for another query)
       $elementIds = array();
       foreach ($elements as $key => $element) {
        $elementIds[$key] = $element->id;
       }

       // Select all the values for an item (grouped by element_id)        
        $select = new Omeka_Db_Select;
        $select->from(array('ie'=>$db->ItemsElements), array('ie.text', 'ie.element_id'));
        $select->where('ie.item_id = ?', $item->id);
        
        $select->where('ie.element_id IN (?)', array($elementIds));
        
        $resultSet = $db->fetchAll($select);
        
        /* Maybe there is a quicker way to do this?  I'm a little rusty on my 
        set theory [KBK] */
        foreach ($elements as $elementKey => $element) {
            foreach ($resultSet as $row) {
                if($row['element_id'] == $element->id) {
                    $element->addText($row['text']);
                }
            }
        }
        
        return $elements;
    }
    
    /**
     * Retrieve a set of Element records that are indexed by the name of the element.
     * 
     * @see Item::getTypeElements()
     * @todo Make this use live data instead of dummy data.
     * @param integer
     * @param integer
     * @return array
     **/
    public function findByItemAndType($itemId, $itemTypeId)
    {
/*         $select = $this->getSelectForItem($itemId);
        $db = $this->getDb();
        $select->joinInner(array('ite'=>$db->ItemTypesElements), 'ite.item_type_id = i.item_type_id', array());
        $select->where('i.item_type_id = ?');
echo $select;exit;        
       return $this->indexRecordsByName( $this->fetchObjects($select, array($itemTypeId)) ); */
       
       $elements = array();
       for ($i=0; $i < 10; $i++) { 
           $element = new Element;
           $element->id = $i + 1;
           $element->name = "Dummy Element " . $i;
           $element->description = "Dummy Description for Dummy Element " . $i;
           $element->setText(array('Dummy Text ' . $i));
           $element->element_type_id = 1;

           //'type_name' doesn't exist in the table but assume that it comes from
           //the element_types table.  
       
           //Alternate between small and large elements for testing purposes
           $element->type_name = ($i % 2) ? 'tinytext' : 'text';
           //$element->type_regex = "/(?<!.).{0,255}(?!.)/s";

           $elements[] = $element;
       }

       return $elements;
    }
}
