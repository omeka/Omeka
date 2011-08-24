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
class ElementTextTable extends Omeka_Db_Table
{
    
    protected $_alias = 'ie';
    
    /**
     * @param integer
     * @param string
     * @return Omeka_Db_Select
     */
    public function getSelectForRecord($recordId, $recordType)
    {
        $select = $this->getSelect();
        $db = $this->getDb();
        
        // Join against the record_types table to only retrieve text for items.
        $select->joinInner(array('rty'=>$db->RecordType), 
            'rty.id = ' . $this->_alias . '.record_type_id AND rty.name = "' . (string)$recordType . '"', array());
        
        $select->where( $this->_alias . '.record_id = ?', (int) $recordId);
        
        // Retrieve element texts ordered by ID, which is incremental.
        // This means that element texts will be retrieved / displayed in the
        // same order as they were saved to the database.
        $select->order($this->_alias . '.id ASC');
        
        return $select;
    }
    
    /**
     * Find all ElementText records for a given database record (Item, File, etc).
     * 
     * @param Omeka_Record
     * @return array
     */
    public function findByRecord(Omeka_Record $record)
    {
        $select = $this->getSelectForRecord($record->id, get_class($record));
        return $this->fetchObjects($select);
    }
    
    public function findByElement($elementId)
    {
        $select = $this->getSelect()->where($this->_alias . '.element_id = ?', (int)$elementId);
        return $this->fetchObjects($select);
    }
    
    /**
     * 
     * @param string
     * @return void
     */
    protected function getRecordTypeId($recordTypeName)
    {
        // Cache the record type ID so we don't have to retrieve it every time.
        if(empty($this->_recordTypeId[$recordTypeName])) {
            $this->_recordTypeId[$recordTypeName] = $this->getDb()->getTable('RecordType')->findIdFromName($recordTypeName);
        }
        
        return $this->_recordTypeId[$recordTypeName];
    }
}
