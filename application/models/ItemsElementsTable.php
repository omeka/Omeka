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
    
    /**
     * @see Element::saveTextFor()
     * @param integer
     * @param integer
     * @return array Set of ItemsElements records.
     **/
    public function findOrNewByItemAndElement($itemId, $elementId)
    {
        $select = $this->getSelect();
        
        $select->where('ie.item_id = ?', $itemId);
        $select->where('ie.element_id = ?', $elementId);
        
        $records = $this->fetchObjects($select);
        
        if(!$records) {
            $record = new ItemsElements;
            $record->item_id = $itemId;
            $record->element_id = $elementId;
            $records = array($record);
        }
        
        return $records;
    }
}
