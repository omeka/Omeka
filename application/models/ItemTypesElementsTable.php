<?php

/**
* 
*/
class ItemTypesElementsTable extends Omeka_Db_Table
{
    protected $_alias = 'ite';
    
    /**
     * Retrieve the element IDs and names that correspond to elements contained 
     * in the item types.
     * @internal Sub-classed and kind of duplicated from Omeka_Db_Table::findPairsForSelectForm().
     * 
     * @param string
     * @return array
     **/
    public function findPairsForSelectForm()
    {
        $db = $this->getDb();
        $select = $this->getSelect();
        $select->reset('columns');
        // This is the only line that is different from findPairsForSelectForm() in Omeka_Db_Table.
        $select->joinInner(array('e'=>$db->Element), 'e.id = ite.element_id', array('e.id', 'e.name'));
        $pairs = $this->getDb()->fetchPairs($select);
        return $pairs;
    }
}
