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
class ItemTable extends Omeka_Db_Table
{    
    /**
     * Can specify a range of valid Item IDs or an individual ID
     * 
     * @param Omeka_Db_Select $select
     * @param string $range Example: 1-4, 75, 89
     * @return void
     */
    public function filterByRange($select, $range)
    {
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
                
                $wheres[] = "(i.id BETWEEN $start AND $finish)";
            
                //It is a single item ID
            } else {
                $id = (int) trim($expr);
                $wheres[] = "(i.id = $id)";
            }
        }
        
        $where = join(' OR ', $wheres);
        
        $select->where('('.$where.')');
    }
    
    /**
     * Run the search filter on the SELECT statement
     * 
     * @param Zend_Db_Select
     * @param array
     * @return void
     */
    public function filterBySearch($select, $params)
    {
        //Apply the simple or advanced search
        if (isset($params['search']) || isset($params['advanced_search'])) {
            $search = new ItemSearch($select);
            if ($simpleTerms = @$params['search']) {
                $search->simple($simpleTerms);
            }
            if ($advancedTerms = @$params['advanced_search']) {
                $search->advanced($advancedTerms);
            }
        }        
    }
    
    /**
     * Apply a filter to the items based on whether or not they should be public
     * 
     * @param Zend_Db_Select
     * @param boolean Whether or not to retrieve only public items
     * @return void
     */
    public function filterByPublic($select, $isPublic)
    {         
        $isPublic = (bool) $isPublic; // this makes sure that empty strings and unset parameters are false

        //Force a preview of the public items
        if ($isPublic) {
            $select->where('i.public = 1');
        } else {
            $select->where('i.public = 0');
        }
    }
    
    public function filterByFeatured($select, $isFeatured)
    {
        $isFeatured = (bool) $isFeatured; // this make sure that empty strings and unset parameters are false
        
        //filter items based on featured (only value of 'true' will return featured items)
        if ($isFeatured) {
            $select->where('i.featured = 1');
        } else {
            $select->where('i.featured = 0');
        }     
    }
    
    /**
     * Filter the SELECT statement based on an item's collection
     * 
     * @param Zend_Db_Select
     * @param Collection|integer|string Either a Collection object, the collection ID, or the name of the collection
     * @return void
     */
    public function filterByCollection($select, $collection)
    {
        $select->joinInner(array('c' => $this->getDb()->Collection), 
                           'i.collection_id = c.id', 
                           array());
        
        if ($collection instanceof Collection) {
            $select->where('c.id = ?', $collection->id);
        } else if (is_numeric($collection)) {
            $select->where('c.id = ?', $collection);
        } else {
            $select->where('c.name = ?', $collection);
        }
    }
    
    /**
     * Filter the SELECT statement based on the item Type
     * 
     * @param Zend_Db_Select
     * @param Type|integer|string Type object, Type ID or Type name
     * @return void
     */
    public function filterByItemType($select, $type)
    {        
        $select->joinInner(array('ty' => $this->getDb()->ItemType), 
                           'i.item_type_id = ty.id', 
                           array());
        if ($type instanceof Type) {
            $select->where('ty.id = ?', $type->id);
        } else if (is_numeric($type)) {
            $select->where('ty.id = ?', $type);
        } else {
            $select->where('ty.name = ?', $type);
        }        
    }
    
    /**
     * Query must look like the following in order to correctly retrieve items     
     * that have all the tags provided (in this example, all items that are
     * tagged both 'foo' and 'bar'):
     *
     *    SELECT i.id 
     *    FROM omeka_items i
     *    WHERE 
     *    (
     *    i.id IN 
     *        (SELECT tg.relation_id as id
     *        FROM omeka_taggings tg
     *        INNER JOIN omeka_tags t ON t.id = tg.tag_id
     *        WHERE t.name = 'foo' AND tg.type = 'Item')
     *    AND i.id IN
     *       (SELECT tg.relation_id as id
     *       FROM omeka_taggings tg
     *       INNER JOIN omeka_tags t ON t.id = tg.tag_id
     *       WHERE t.name = 'bar' AND tg.type = 'Item')
     *    )
     *      ...
     *
     *
     * @param Omeka_Db_Select
     * @param string|array A comma-delimited string or an array of tag names.
     * @return void
     */
    public function filterByTags($select, $tags)
    {   
        // Split the tags into an array if they aren't already     
        if (!is_array($tags)) {
            $tags = explode(get_option('tag_delimiter'), $tags);
        }
        
        $db = $this->getDb();
        
        // For each of the tags, create a SELECT subquery using Omeka_Db_Select.
        // This subquery should only return item IDs, so that the subquery can be
        // appended to the main query by WHERE i.id IN (SUBQUERY).
        foreach ($tags as $tagName) {
            
            $subSelect = new Omeka_Db_Select;
            $subSelect->from(array('tg'=>$db->Taggings), array('id'=>'tg.relation_id'))
                ->joinInner(array('t'=>$db->Tag), 't.id = tg.tag_id', array())
                ->where('t.name = ? AND tg.`type` = "Item"', trim($tagName));
            
            $select->where('i.id IN (' . (string) $subSelect . ')');
        }    
    }
    
    /**
     * Filter the SELECT based on users or entities associated with the item
     * 
     * @param Zend_Db_Select
     * @param integer $entityId  ID of the User or Entity to filter by
     * @param boolean $isUser Whether or not the ID From the previous argument is for a user or entity
     * @return void
     */
    public function filterByUserOrEntity($select, $entityId, $isUser=true)
    {
        $db = $this->getDb();
        
        $select->joinLeft(array('ie' => "$db->EntitiesRelations"), 
                          'ie.relation_id = i.id', 
                          array());
        $select->joinLeft(array('e' => "$db->Entity"), 
                          'e.id = ie.entity_id', 
                          array());
        
        // Only retrieve items that were added by a specific user/entity.
        $select->joinLeft(array('ier' => $db->EntityRelationships), 
                          'ier.id = ie.relationship_id',
                          array());
        $select->where('ier.name = "added"');
        
        
        if ($isUser) {
            $select->joinLeft(array('u' => "$db->User"), 
                              'u.entity_id = e.id', 
                              array());
            $select->where('(u.id = ? AND ie.type = "Item")', $entityId);            
        } else {
            $select->where('(e.id = ? AND ie.type = "Item")', $entityId);
        }                                
    }
    
    /**
     * Filter SELECT statement based on items that are not tagged with a specific
     * set of tags
     * 
     * @param Zend_Db_Select 
     * @param array|string Set of tag names (either array or comma-delimited string)
     * @return void
     */
    public function filterByExcludedTags($select, $tags)
    {
        $db = $this->getDb();
        
        if (!is_array($tags)){
            $tags = explode(get_option('tag_delimiter'), $tags);
        }
        $subSelect = new Omeka_Db_Select;
        $subSelect->from(array('i'=>$db->Item), 'i.id')
                         ->joinInner(array('tg' => $db->Taggings), 
                                     'tg.relation_id = i.id AND tg.type = "Item"', 
                                     array())
                         ->joinInner(array('t' => $db->Tag), 
                                     'tg.tag_id = t.id', 
                                     array());
                        
        foreach ($tags as $key => $tag) {
            $subSelect->where("t.name LIKE ?", $tag);
        }    

        $select->where('i.id NOT IN ('.$subSelect->__toString().')');        
    }

    /**
     * Filter SELECT statement based on whether items have a derivative image
     * file.
     *
     * @param Zend_Db_Select
     * @param boolean $hasDerivativeImage Whether items should have a derivative
     * image file.
     * @return void
     */
    public function filterByHasDerivativeImage($select, $hasDerivativeImage = true)
    {
        $hasDerivativeImage = $hasDerivativeImage ? '1' : '0';

        $db = $this->getDb();

        $select->joinLeft(array('f'=>"$db->File"), 'f.item_id = i.id', array());
        $select->where('f.has_derivative_image = ?', $hasDerivativeImage);
    }
    
    public function orderSelectByRecent($select)
    {
        $select->order('i.id DESC');
    }
    
    /**
     * Order SELECT results randomly.
     *
     * @param Zend_Db_Select
     * @return void
     */
    public function orderSelectByRandom($select)
    {
        $select->order('RAND()');
    }

    /**
     * Possible options: 'public','user','featured','collection','type','tag',
     * 'excludeTags', 'search', 'recent', 'range', 'advanced', 'hasImage',
     * 'random'
     * 
     * @param Omeka_Db_Select
     * @param array
     * @return void
     */
    public function applySearchFilters($select, $params)
    {   
        // Show items associated somehow with a specific user or entity
        if (isset($params['user']) || isset($params['entity'])) {
            $filterByUser = isset($params['user']);
            $paramToFilter = (int) ($filterByUser ? $params['user'] : $params['entity']);
            $this->filterByUserOrEntity($select, $paramToFilter, $filterByUser);
        }
        
        if(isset($params['public'])) {
            $this->filterByPublic($select, $params['public']);
        }
        if(isset($params['featured'])) {
            $this->filterByFeatured($select, $params['featured']);
        }
        
        if (isset($params['collection'])) {
            $this->filterByCollection($select, $params['collection']);
        }
                
        // filter based on type
        if (isset($params['type'])) {
            $this->filterByItemType($select, $params['type']);
        }
        
        // filter based on tags
        if (isset($params['tags'])) {
            $this->filterByTags($select, $params['tags']);
        }
        
        // exclude Items with given tags
        if (isset($params['excludeTags'])) {
            $this->filterByExcludedTags($select, $params['excludeTags']);
        }

        // include only Items with derivative images.
        if (isset($params['hasImage'])) {
            $this->filterByHasDerivativeImage($select, $params['hasImage']);
        }
        
        $this->filterBySearch($select, $params);
                
        if (isset($params['range'])) {
            $this->filterByRange($select, $params['range']);
        }
        
        // Order items by recent. @since 11/7/07  ORDER BY must not be in the 
        // COUNT() query b/c it slows down
        if (isset($params['recent'])) {
            $this->orderSelectByRecent($select);
        }

        if (isset($params['random'])) {
            $this->orderSelectByRandom($select);
        }
        
        //If we returning the data itself, we need to group by the item ID
        $select->group("i.id");
                
    }
    
    /**
     * Enables sorting based on ElementSet,Element field strings.
     *
     * @param Omeka_Db_Select $select
     * @param string $sortField Field to sort on
     * @param string $sortDir Sorting direction (ASC or DESC)
     */
    public function applySorting($select, $sortField, $sortDir)
    {
        parent::applySorting($select, $sortField, $sortDir);

        $db = $this->getDb();
        $fieldData = explode(',', $sortField);
        if (count($fieldData) == 2) {
            $element = $db->getTable('Element')->findByElementSetNameAndElementName($fieldData[0], $fieldData[1]);
            if ($element) {
                $recordTypeId = $db->getTable('RecordType')->findIdFromName('Item');
                $select->joinLeft(array('et_sort' => $db->ElementText),
                                  "et_sort.record_id = i.id AND et_sort.record_type_id = {$recordTypeId} AND et_sort.element_id = {$element->id}",
                                  array())
                       ->group('i.id')
                       ->order(array("IF(ISNULL(et_sort.text), 1, 0) $sortDir",
                                     "et_sort.text $sortDir"));
            }
        }
    }
    
    /**
     * This is a kind of simple factory that spits out proper beginnings 
     * of SQL statements when retrieving items
     *
     * @return Omeka_Db_Select
     */
    public function getSelect()
    {
        // @duplication self::findBy()
        $db = $this->getDb();
        $select = new Omeka_Db_Select($db->getAdapter());
        
        $select->from(array('i'=>$db->Item), array('i.*'));
        $acl = Omeka_Context::getInstance()->acl;
        if ($acl) {
            new ItemPermissions($select, $acl);
        }
        
        return $select;
    }
    
    /**
     * Return the first item accessible to the current user.
     * 
     * @return Item|null
     */
    public function findFirst()
    {
        $select = $this->getSelect();
        $select->order('i.id ASC');
        $select->limit(1);
        return $this->fetchObject($select);
    }
    
    /**
     * Return the last item accessible to the current user.
     * 
     * @return Item|null
     */
    public function findLast()
    {
        $select = $this->getSelect();
        $select->order('i.id DESC');
        $select->limit(1);
        return $this->fetchObject($select);
    }
    
    public function findPrevious($item)
    {
        return $this->findNearby($item, 'previous');
    }
    
    public function findNext($item)
    {
        return $this->findNearby($item, 'next');
    }
    
    protected function findNearby($item, $position = 'next')
    {
        //This will only pull the title and id for the item
        $select = $this->getSelect();
        
        $select->limit(1);
        
        switch ($position) {
            case 'next':
                $select->where('i.id > ?', (int) $item->id);
                $select->order('i.id ASC');
                break;
                
            case 'previous':
                $select->where('i.id < ?', (int) $item->id);
                $select->order('i.id DESC');
                break;
                
            default:
                throw new Omeka_Record_Exception( 'Invalid position provided to ItemTable::findNearby()!' );
                break;
        }
        
        return $this->fetchObject($select);
    }
    
    /**
     * Finds a random featured item.
     *
     * @deprecated Since 1.4.
     * @see findBy There are parameters for returning items randomly and with
     * derivative images as of version 1.4.
     *
     * @param bool $withImage Whether to find an item with a derivative image.
     * @return Item
     */
    public function findRandomFeatured($withImage=true)
    {        
        $select = $this->getSelect();
        
        $select->limit(1);
        
        $params = array('featured' => 1, 'random' => 1);

        if ($withImage) {
            $params['hasImage'] = 1;
        }

        $this->applySearchFilters($select, $params);

        $item = $this->fetchObject($select);
    
        return $item;
    }
}
