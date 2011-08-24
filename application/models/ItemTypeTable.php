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
    protected $_alias = 'it';
    
    protected function _getColumnPairs()
    {
        return array($this->_alias . '.id', $this->_alias . '.name');
    }
    
    public function findByName($itemTypeName) 
    {
        $select = $this->getSelect();
        $select->where($this->_alias . '.name = ?', $itemTypeName);        
        return $this->fetchObject($select);   	
    }
}
