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
class Table_ElementText extends Omeka_Db_Table
{
    /**
     * @param integer
     * @param string
     * @return Omeka_Db_Select
     */
    public function getSelectForRecord($recordId, $recordType)
    {
        $select = $this->getSelect();
        $db = $this->getDb();

        $select->where('element_texts.record_type = ?', (string) $recordType);
        $select->where('element_texts.record_id = ?', (int) $recordId);
        
        // Retrieve element texts ordered by ID, which is incremental.
        // This means that element texts will be retrieved / displayed in the
        // same order as they were saved to the database.
        $select->order('element_texts.id ASC');
        
        return $select;
    }
    
    /**
     * Find all ElementText records for a given database record (Item, File, etc).
     * 
     * @param Omeka_Record_AbstractRecord
     * @return array
     */
    public function findByRecord(Omeka_Record_AbstractRecord $record)
    {
        $select = $this->getSelectForRecord($record->id, get_class($record));
        return $this->fetchObjects($select);
    }
    
    public function findByElement($elementId)
    {
        $select = $this->getSelect()->where('element_texts.element_id = ?', (int)$elementId);
        return $this->fetchObjects($select);
    }
}
