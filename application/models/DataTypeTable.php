<?php 
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @subpackage Models
 * @package Omeka
 * @author CHNM
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class DataTypeTable extends Omeka_Db_Table
{
    /**
     * @internal This is duplicated in RecordTypeTable.
     * 
     * @param string
     * @return integer|false
     */
    public function findIdFromName($dataTypeName)
    {
        $table = $this->getTableName();
        $select = "SELECT d.id FROM $table d WHERE d.name = ?";
        $dataTypeId = $this->getDb()->fetchOne($select, array($dataTypeName));
        if (empty($dataTypeId)) {
            throw new Omeka_Record_Exception(__('Cannot find id from DataType name: %s', $dataTypeName));
        }
        return $dataTypeId;
    }
    
    protected function _getColumnPairs()
    {
        return array('d.id', 'd.name');
    }
}
