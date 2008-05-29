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
           $text = array('Dummy Text ' . $i);
           $element->text = $text;
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
