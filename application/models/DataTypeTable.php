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
     * @todo This is duplicated in RecordTypeTable.  There should be an analog 
     * for this type of name-id reverse lookup in Omeka_Db_Table.  Not sure how 
     * best to do that though.
     * 
     * @param string
     * @return integer|false
     **/
    public function getIdFromName($dataTypeName)
    {
        $table = $this->getTableName();
        $select = "SELECT d.id FROM $table d WHERE d.name = ?";
        var_dump($this->getDb()->fetchOne($select, array($dataTypeName)));exit;
    }
    
    protected function _getColumnPairs()
    {
        return array('d.id', 'd.name');
    }
}
