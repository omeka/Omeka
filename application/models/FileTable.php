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
class FileTable extends Omeka_Db_Table
{
    protected $_target = 'File';

    /**
     * All files should only be retrieved if they join properly on the items
     * table.
     *
     * @return Omeka_Db_Select
     */
    public function getSelect()
    {
        $select = parent::getSelect();
        $db = $this->getDb();
        $select->joinInner(array('i' => $db->Item), "i.id = f.item_id", array());
        if($acl = Omeka_Context::getInstance()->getAcl()) {
            new ItemPermissions($select, $acl);
        }
        $select->group('f.id');
        return $select;
    }

    /**
     * Retrieve a random file with an image associated with an item.
     *
     * @param integer $itemId
     * @return File
     */
    public function getRandomFileWithImage($itemId)
    {
        $select = $this->getSelect()
                       ->where('f.item_id = ? AND f.has_derivative_image = 1')
                       ->order('RAND()')
                       ->limit(1);

        return $this->fetchObject($select, array($itemId));
    }

    /**
     * Retrieve files associated with an item.
     *
     * @param integer $itemId
     * @param array $fileIds Optional If given, this will only retrieve files
     * with these specific IDs.
     * @param string $sort The manner in which to order the files by. For example: 'id' = file id, 'filename' = alphabetical by filename
     * @return array
     */
    public function findByItem($itemId, $fileIds = array(), $sort='id')
    {
        $select = $this->getSelect();
        $select->where('f.item_id = ?');
        if ($fileIds) {
            $select->where('f.id IN (?)', $fileIds);
        }

        $this->_orderFilesBy($select, $sort);

        return $this->fetchObjects($select, array($itemId));
    }

    /**
     * Retrieve files for an item that has derivative images.
     *
     * @param integer $itemId
     * @param integer|null $index Optional If given, this specifies the file to
     * retrieve for an item, based upon the ordering of its derivative files.
     * @param string $sort The manner in which to order the files by. For example: 'id' = file id, 'filename' = alphabetical by filename
     *
     * @return File|array
     */
    public function findWithImages($itemId, $index=null, $sort='id')
    {
        $select = $this->getSelect()
                       ->where('f.item_id = ? AND f.has_derivative_image = 1');

        $this->_orderFilesBy($select, $sort);

        if ($index === null) {
            return $this->fetchObjects($select, array($itemId));
        } else {
            $select->limit(1, $index);
            return $this->fetchObject($select, array($itemId));
        }

    }

    /**
     * Orders select results for files.
     *
     * @param Omeka_Db_Select The select object for finding files
     * @param string $sort The manner in which to order the files by.
     * For example:
     * 'id' = file id
     * 'filename' = alphabetical by filename
     *
     * @return void
     */
    private function _orderFilesBy($select, $sort)
    {
        // order the files
        switch($sort) {
            case 'order':
                $select->order('ISNULL(f.order)')->order('f.order');
            break;

            case 'filename':
                $select->order('f.original_filename ASC');
            break;

            case 'id':
            default:
                $select->order('f.id ASC');
            break;
        }
    }
}
