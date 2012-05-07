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
class ItemTypeTable extends Omeka_Db_Table
{
    protected function _getColumnPairs()
    {
        return array($this->_name . '.id', $this->_name . '.name');
    }
    
    public function findByName($itemTypeName) 
    {
        $select = $this->getSelect();
        $select->where($this->_name . '.name = ?', $itemTypeName);
        return $this->fetchObject($select);
    }
}
