<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/
 
/**
 * @package Omeka
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class ItemsElementsTable extends Omeka_Db_Table
{
    
    protected $_alias = 'ie';
    
    public function findByItem($itemId)
    {
        $select = $this->getSelect()->where('ie.item_id = ?', $itemId);
        return $this->fetchObjects($select);
    }
    
    /**
     * @see Element::saveTextFor()
     * @param integer
     * @param integer
     * @param integer
     * @return array Set of ItemsElements records, some of which may be new.
     **/
    public function findOrNewByItemAndElement($itemId, $elementId, $numToFind)
    {
        $records = array();
        $select = $this->getSelect();
        
        $select->where('ie.item_id = ?', $itemId);
        $select->where('ie.element_id = ?', $elementId);
        
        $select->limit($numToFind);
        
        $records = $this->fetchObjects($select);
        
        for ($i=0; $i < $numToFind; $i++) { 
            if(!$records[$i]) {
                $record = new ItemsElements;
                $record->item_id = $itemId;
                $record->element_id = $elementId;
                $records[$i] = $record;
            }
        }
                
        return $records;
    }
}
