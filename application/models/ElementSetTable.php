<?php 
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * @package Omeka
 * @subpackage Models
 * @author CHNM
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
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
     */
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
    
    /**
     * Find all element sets for Item record type. If the second param is set, 
     * this will include all element sets that belong to the 'All' record type.
     *
     * @param boolean
     */
    public function findForItems($includeAll = true)
    {
        return $this->findByRecordType('Item', $includeAll);
    }
    
    public function findByName($name)
    {
        $select = $this->getSelect()->where('name = ?', $name);
        return $this->fetchObject($select);
    }
}
