<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * @package Omeka
 * @subpackage Models
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class FileTable extends Omeka_Db_Table
{
    protected $_target = 'File';
    
    /**
     * All files should only be retrieved if they join properly on the items
     * table.  
     * 
     * @return Omeka_Db_Select
     **/
    public function getSelect()
    {
        $select = parent::getSelect();
        $db = $this->getDb();
        $select->joinInner(array('i' => $db->Item), "i.id = f.item_id", array());
        new ItemPermissions($select);
        $select->group('f.id');
        return $select;
    }
    
    public function getRandomFileWithImage($item_id)
    {        
        $select = $this->getSelect()
                       ->where('f.item_id = ? AND f.has_derivative_image = 1')
                       ->order('RAND()')
                       ->limit(1);
        
        return $this->fetchObject($sql, array($item_id));
    }
    
    /**
     * Retrieve files associated with an item. 
     * 
     * @param integer $itemId
     * @param array $fileIds Optional If given, this will only retrieve files
     * with these specific IDs.
     * @return array
     **/
    public function findByItem($item_id, $fileIds = array())
    {
        $select = $this->getSelect();
        $select->where('f.item_id = ?');
        if ($fileIds) {
            $select->where('f.id IN (?)', $fileIds);
        }
        return $this->fetchObjects($select, array($item_id));
    }
}