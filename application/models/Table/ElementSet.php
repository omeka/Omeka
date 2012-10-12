<?php 
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * @package Omeka\Db\Table
 */
class Table_ElementSet extends Omeka_Db_Table
{
    public function getSelect() {
        $select = parent::getSelect();
        $select->order('id ASC');
        return $select;
    }
    
    /**
     * Find all the element sets that correspond to a particular record type.  
     * If the second param is set, this will include all element sets that belong 
     * to the 'All' record type.
     * 
     * @param string
     * @param boolean
     * @return array
     */
    public function findByRecordType($recordTypeName, $includeAll = true)
    {
        $select = $this->getSelect();
        $select->where('record_type = ?', $recordTypeName);
        if ($includeAll) {
            $select->orWhere('record_type IS NULL');
        }
        
        return $this->fetchObjects($select);
    }
    
    public function findByName($name)
    {
        $select = $this->getSelect()->where('name = ?', $name);
        return $this->fetchObject($select);
    }
}
