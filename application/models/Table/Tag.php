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
class Table_Tag extends Omeka_Db_Table
{
    public function findOrNew($name)
    {
        $db = $this->getDb();
        $sql = "
        SELECT tags.* 
        FROM {$db->Tag} tags
        WHERE tags.name = ?
        LIMIT 1";
        $tag = $this->fetchObject($sql, array($name));

        if (!$tag) {
            $tag = new Tag;
            $tag->name = $name;
            $tag->save();
        }

        return $tag;
    }

    /**
     * Filter a SELECT statement based on an Omeka_Record_AbstractRecord instance
     * 
     * @param Omeka_Db_Select
     * @param Omeka_Record_AbstractRecord
     */
    public function filterByRecord($select, $record)
    {
        if ($record->exists()) {
            $record_id = $record->id;
            $select->where('records_tags.record_id = ?', $record_id)
                   ->where('records_tags.record_type = ?', get_class($record));
        //A non-persistent record has no tags, so return emptiness
        } else {
            $select->where('tags.id = 0');
        }
    }
    
    /**
     * Filter a SELECT statement based on Omeka record IDs of a given record type.
     *
     * Can specify a range of valid record IDs or an individual ID
     *
     * @version 2.2.2
     * @param Omeka_Db_Select $select
     * @param string $range Example: 1-4, 75, 89
     * @param string $type Example: Item, Collection, etc.
     * @return void
     */
    public function filterByRecordRange($select, $range, $type)
    {
        // Narrow the records to the given type
        $select->where('records_tags.record_type = ?',$type);

        // Comma-separated expressions should be treated individually
        $exprs = explode(',', $range);

        // Construct a SQL clause where every entry in this array is linked by 'OR'
        $wheres = array();

        foreach ($exprs as $expr) {
            // If it has a '-' in it, it is a range of item IDs.  Otherwise it is
            // a single item ID
            if (strpos($expr, '-') !== false) {
                list($start, $finish) = explode('-', $expr);

                // Naughty naughty koolaid, no SQL injection for you
                $start  = (int) trim($start);
                $finish = (int) trim($finish);

                $wheres[] = "(records_tags.record_id BETWEEN $start AND $finish)";

                //It is a single item ID
            } else {
                $id = (int) trim($expr);
                $wheres[] = "(records_tags.record_id = $id)";
            }
        }

        $where = join(' OR ', $wheres);

        $select->where('('.$where.')');
    }

    /**
     * Filter the SELECT statement based on an item's collection
     *
     * @param Zend_Db_Select
     * @param Collection|integer Either a Collection object, or the collection ID
     * @return void
     */
    public function filterByCollection($select, $collection)
    {
        if ($collection instanceof Collection) {
            $collectionId = $collection->id;
        } elseif (is_numeric($collection)) {
            $collectionId = (int) $collection;
        } else {
            return;
        }

        if ($collectionId === 0) {
            $select->where('items.collection_id IS NULL');
        } else {
            $select->joinInner(
                array('collections' => $this->getDb()->Collection),
                'items.collection_id = collections.id',
                array());
            $select->where('collections.id = ?', $collectionId);
        }
    }

    /**
     * Apply custom sorting for tags.
     *
     * This also applies the normal, built-in sorting.
     *
     * @param Omeka_Db_Select $select
     * @param string $sortField Sorting field.
     * @param string $sortDir Sorting direction, suitable for direct
     *  inclusion in SQL (ASC or DESC).
     */
    public function applySorting($select, $sortField, $sortDir)
    {
        parent::applySorting($select, $sortField, $sortDir);

        switch ($sortField) {
            case 'time':
                $select->order(array("records_tags.time $sortDir", 'tags.name ASC'));
                break;
            case 'count':
                $select->order("tagCount $sortDir");
                break;
            default:
                break;
        }
    }

    /**
     * Filter SELECT statement based on the type of tags to view (Item, Exhibit, etc.)
     * 
     * @param Omeka_Db_Select
     * @param string
     */
    public function filterByTagType($select, $type)
    {
        $db = $this->getDb();

        $recordType = $db->quote($type);
        //Redo the "from" so we can change the join condition
        $select->reset(Zend_Db_Select::FROM)
               ->from(array('tags' => $db->Tag), array())
               ->joinLeft(array('records_tags' => $db->RecordsTags),
                    "records_tags.tag_id = tags.id AND records_tags.record_type = $recordType",
                    array());

        //Showing tags related to items
        if ($type == 'Item') {
            //Join on the items table, add permissions checks for public
            $select->joinLeft(array('items' => $db->Item), "items.id = records_tags.record_id", array());
            $permissions = new Omeka_Db_Select_PublicPermissions('Items');
            $permissions->apply($select, 'items');
        }
    }

    /**
     * Filter SELECT statement based on whether the tag contains the partial tag name
     * 
     * @param Omeka_Db_Select
     * @param string
     */
    public function filterByTagNameLike($select, $partialTagName)
    {
        $select->where("`tags`.`name` LIKE CONCAT('%', ?, '%')", $partialTagName);
    }

    /**
     * Retrieve a certain number of tags
     *
     * @param Omeka_Db_Select 
     * @param array $params
     *        'limit' => integer
     *        'record' => instanceof Omeka_Record_AbstractRecord
     *        'like' => partial_tag_name
     *        'type' => tag_type
     */
    public function applySearchFilters($select, $params = array())
    {
        $db = $this->getDb();

        if (array_key_exists('type', $params)) {
            if (isset($params['range'])) {
              $this->filterByRecordRange($select,$params['range'],$params['type']);
            } else {
              $this->filterByTagType($select, $params['type']);
            }

            //If we only want tags for public items, use one of the ItemTable's filters
            if ($params['type'] == 'Item' && isset($params['public'])) {
                $db->getTable('Item')->filterByPublic($select, (bool) $params['public']);
            }
        }
        
        if (array_key_exists('collection',$params) && isset($params['type']) && $params['type']==='Item') {
          $this->filterByCollection($select,$params['collection']);
        }

        if (array_key_exists('record', $params) && $params['record'] instanceof Omeka_Record_AbstractRecord) {
            $this->filterByRecord($select, $params['record']);
        }

        if (array_key_exists('like', $params)) {
            $this->filterByTagNameLike($select, $params['like']);
        }

        if (!(array_key_exists('include_zero', $params) && $params['include_zero'])) {
            $select->where('records_tags.id IS NOT NULL');
        }
        $select->group("tags.id");
    }

    /**
     * @internal SELECT statements should always pull a count of how many times 
     * the tag occurs as a tagCount field in the Tag object.
     * 
     * @return Omeka_Db_Select
     */
    public function getSelect()
    {
        $select = new Omeka_Db_Select;

        $db = $this->getDb();

        $select->from(array('tags' => $db->Tag), array('tags.*', 'tagCount' => 'COUNT(records_tags.id)'))
                ->joinLeft(array('records_tags' => $db->RecordsTags), 'records_tags.tag_id = tags.id', array())
                ->group('tags.id');

        return $select;
    }

    /**
     * @internal Avoid the unnecessary expense of joining if we're just counting
     * all the tags.
     */
    public function getSelectForCount($params = array())
    {
        if (!$params) {
            $select = new Omeka_Db_Select;
            $db = $this->getDb();
            $select->from(array('tags' => $db->Tag), array('COUNT(*)'));
        } else {
            $select = parent::getSelectForCount($params);
        }
        return $select;
    }

    public function findTagNamesLike($partialName, $limit = 10)
    {
        $db = $this->getDb();
        $sql = "SELECT tags.name FROM $db->Tag tags WHERE tags.name LIKE ? LIMIT $limit";
        $tags = $db->fetchCol($sql, array($partialName . '%'));
        return $tags;
    }
}
