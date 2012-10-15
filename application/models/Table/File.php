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
class Table_File extends Omeka_Db_Table
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
        $select->joinInner(array('items' => $db->Item), 'items.id = files.item_id', array());

        $permissions = new Omeka_Db_Select_PublicPermissions('Items');
        $permissions->apply($select, 'items');

        $select->group('files.id');
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
                       ->where('files.item_id = ? AND files.has_derivative_image = 1')
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
        $select->where('files.item_id = ?');
        if ($fileIds) {
            $select->where('files.id IN (?)', $fileIds);
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
                       ->where('files.item_id = ? AND files.has_derivative_image = 1');

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
                $select->order('ISNULL(files.order)')->order('files.order');
            break;

            case 'filename':
                $select->order('files.original_filename ASC');
            break;

            case 'id':
            default:
                $select->order('files.id ASC');
            break;
        }
    }
}
