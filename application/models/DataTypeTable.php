<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * 
 * @subpackage Models
 * @package Omeka
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class DataTypeTable extends Omeka_Db_Table
{
    /**
     * @internal This is duplicated in RecordTypeTable.
     * 
     * @param string
     * @return integer|false
     **/
    public function findIdFromName($dataTypeName)
    {
        $table = $this->getTableName();
        $select = "SELECT d.id FROM $table d WHERE d.name = ?";
        $dataTypeId = $this->getDb()->fetchOne($select, array($dataTypeName));
        if (empty($dataTypeId)) {
            throw new Omeka_Record_Exception('Cannot find id from DataType name: ' . $dataTypeName);
        }
        return $dataTypeId;
    }
    
    protected function _getColumnPairs()
    {
        return array('d.id', 'd.name');
    }
}
