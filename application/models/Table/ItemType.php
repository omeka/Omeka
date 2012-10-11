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
class Table_ItemType extends Omeka_Db_Table
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
