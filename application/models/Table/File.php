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

    public function applySearchFilters($select, $params)
    {
        $boolean = new Omeka_Filter_Boolean;
        
        foreach ($params as $paramName => $paramValue) {
            if ($paramValue === null || (is_string($paramValue) && trim($paramValue) == '')) {
                continue;
            }

            switch($paramName) {
                case 'item':
                case 'item_id':
                    $select->where('files.item_id = ?', $paramValue);
                    break;
                    
                case 'order':
                    if($paramValue == 'null') {
                        $select->where('files.order IS NULL');
                    } else {
                        $select->where('files.order = ?', $paramValue);
                    }
                    break;
                    
                case 'original_filename':
                    $select->where('files.original_filename = ?', $paramValue);
                    break;

                case 'size_greater_then':
                    $select->where('files.size > ?', $paramValue);
                    break;
                    
                case 'has_derivative_image':
                    $this->filterByHasDerivativeImage($select, $boolean->filter($paramValue));
                    break;
                    
                case 'mime_type':
                    $select->where('files.mime_type = ?', $paramValue);
                    break;
                    
                case 'added_since':
                    $this->filterBySince($select, $paramValue, 'added');
                    break;
                    
                case 'modified_since':
                    $this->filterBySince($select, $paramValue, 'modified');
                    break;
            }
        }
    }
    
    /**
     * Filter select object by date since.
     *
     * @param Zend_Db_Select $select
     * @param string $dateSince ISO 8601 formatted date
     * @param string $dateField "added" or "modified"
     */
    public function filterBySince($select, $dateSince, $dateField)
    {
        // Reject invalid date fields.
        if (!in_array($dateField, array('added', 'modified'))) {
            return;
        }
    
        // Accept an ISO 8601 date, set the tiemzone to the server's default
        // timezone, and format the date to be MySQL timestamp compatible.
        $date = new Zend_Date($dateSince, Zend_Date::ISO_8601);
        $date->setTimezone(date_default_timezone_get());
        $date = $date->get('yyyy-MM-dd HH:mm:ss');
    
        // Select all dates that are greater than the passed date.
        $select->where("files.$dateField > ?", $date);
    }    
    
    public function filterByHasDerivativeImage($select, $hasDerivative)
    {
        if ($hasDerivative) {
            $select->where('files.has_derivative_image = 1');
        } else {
            $select->where('files.has_derivative_image = 0');
        }        
    }
    
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
     * @param string $sort The manner by which to order the files. For example:
     *  'id': file id, 'filename' = alphabetical by filename. The default is
     *  'order', following the user's specified order.
     * @return array
     */
    public function findByItem($itemId, $fileIds = array(), $sort = 'order')
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
     * Get a single file associated with an item, by index.
     *
     * @param integer $itemId
     * @param integer $index
     * @param string $sort The manner by which to order the files. For example:
     *  'id': file id, 'filename' = alphabetical by filename. The default is
     *  'order', following the user's specified order.
     * @return File|null
     */
    public function findOneByItem($itemId, $index = 0, $sort = 'order')
    {
        $select = $this->getSelect();
        $select->where('files.item_id = ?');
        $this->_orderFilesBy($select, $sort);
        $select->limit(1, $index);

        return $this->fetchObject($select, array($itemId));
    }

    /**
     * Retrieve files for an item that has derivative images.
     *
     * @param integer $itemId The ID of the item to get images for.
     * @param integer|null $index Optional If given, this specifies the file to
     * retrieve for an item, based upon the ordering of its files.
     * @param string $sort The manner by which to order the files. For example:
     *  'id': file id, 'filename': alphabetical by filename. The default is
     *  'order', following the user's specified order.
     *
     * @return File|array
     */
    public function findWithImages($itemId, $index = null, $sort = 'order')
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
