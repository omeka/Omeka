<?php 

/**
 * @package Omeka
 * @subpackage Models
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class ElementSetTable extends Omeka_Db_Table
{
    protected $_alias = 'es';
    
    /**
     * Find all the element sets that correspond to a particular record type.  
     * If the second param is set, this will include all element sets that belong 
     * to the 'All' record type.
     * 
     * @param string
     * @param boolean
     * @return array
     **/
    public function findByRecordType($recordTypeName, $includeAll = true)
    {
        $select = $this->getSelect();
        $select->joinInner(array('rty'=>$this->getDb()->RecordType), 'rty.id = es.record_type_id', array());
        $select->where('rty.name = ?', $recordTypeName);
        if ($includeAll) {
            $select->orWhere('rty.name = "All"');
        }
        
        return $this->fetchObjects($select);
    }
    
    public function findForItems()
    {
        $select = $this->getSelect();
        $select->joinInner(array('rty'=>$this->getDb()->RecordType), 'rty.id = es.record_type_id', array());
        $select->where('rty.name = "Item" OR rty.name = "All"');
        // Exclude item-type by default?  
        // $select->where('es.name != "Item Type"');
        return $this->fetchObjects($select);
    }
    
    public function findByName($name)
    {
        $select = $this->getSelect()->where('name = ?', $name);
        return $this->fetchObject($select);
    }
}
