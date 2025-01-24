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
class Table_Collection extends Omeka_Db_Table
{
    public function applySearchFilters($select, $params)
    {
        $boolean = new Omeka_Filter_Boolean;
        foreach ($params as $key => $value) {
            switch ($key) {
                case 'user':
                case 'owner':
                case 'user_id':
                case 'owner_id':
                    $this->filterByUser($select, $value, 'owner_id');
                    break;
                case 'public':
                    $this->filterByPublic($select, $boolean->filter($value));
                    break;
                case 'featured':
                    $this->filterByFeatured($select, $boolean->filter($value));
                    break;
                case 'added_since':
                    $this->filterBySince($select, $value, 'added');
                    break;
                case 'modified_since':
                    $this->filterBySince($select, $value, 'modified');
                    break;
                case 'range':
                    $this->filterByRange($select, $value);
                    break;
                case 'tag':
                case 'tags':
                    $this->filterByTags($select, $value);
                    break;
                case 'excludeTags':
                    $this->filterByExcludedTags($select, $value);
                    break;
            }
        }
    }

    public function findPairsForSelectForm(array $options = array())
    {
        $db = $this->getDb();

        $subquery = new Omeka_Db_Select;
        $subquery->from(array('element_texts' => $db->ElementText), 'id');
        $subquery->joinInner(
            array('elements' => $db->Element),
            'elements.id = element_texts.element_id',
            array()
        );
        $subquery->joinInner(
            array('element_sets' => $db->ElementSet),
            'element_sets.id = elements.element_set_id',
            array()
        );
        $subquery->where("element_sets.name = 'Dublin Core'");
        $subquery->where("elements.name = 'Title'");
        $subquery->where("element_texts.record_type = 'Collection'");
        $subquery->where('element_texts.record_id = collections.id');
        $subquery->limit(1);

        $select = $this->getSelectForFindBy($options);
        $select->joinLeft(
            array('element_texts' => $db->ElementText),
            "element_texts.id = ($subquery)",
            array()
        );

        $select->reset(Zend_Db_Select::COLUMNS);
        $select->from(array(), array('collections.id', 'element_texts.text'));
        $select->order('element_texts.text');

        $pairs = $db->fetchPairs($select);
        foreach ($pairs as $collectionId => &$name) {
            if ($name === null || $name == '') {
                $name = __('[Untitled] #%s', $collectionId);
            } else {
                $name = strip_formatting($name);
            }
        }

        if (isset($options['include_no_collection']) && $options['include_no_collection']) {
            $pairs = array(__('No Collection')) + $pairs;
        }
        return $pairs;
    }

    /**
     * Apply permissions checks to all SQL statements retrieving collections from the table
     * 
     * @param string
     */
    public function getSelect()
    {
        $select = parent::getSelect();
        $permissions = new Omeka_Db_Select_PublicPermissions('Collections');
        $permissions->apply($select, 'collections');

        return $select;
    }

    public function findRandomFeatured()
    {
        $select = $this->getSelect()->where('collections.featured = 1')->order('RAND()')->limit(1);
        return $this->fetchObject($select);
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
                $select->joinLeft(array('et_sort' => $db->ElementText),
                                  "et_sort.record_id = collections.id AND et_sort.record_type = 'Collection' AND et_sort.element_id = {$element->id}",
                                  array())
                       ->group('collections.id')
                       ->order(array("IF(ISNULL(et_sort.text), 1, 0) $sortDir",
                                     "et_sort.text $sortDir"));
            }
        }
    }

    /**
     * Query must look like the following in order to correctly retrieve collections
     * that have all the tags provided (in this example, all collections that are
     * tagged both 'foo' and 'bar'):
     *
     *    SELECT i.id
     *    FROM omeka_collections i
     *    WHERE
     *    (
     *    i.id IN
     *        (SELECT tg.record_id as id
     *        FROM omeka_records_tags tg
     *        INNER JOIN omeka_tags t ON t.id = tg.tag_id
     *        WHERE t.name = 'foo' AND tg.record_type = 'Collection')
     *    AND i.id IN
     *       (SELECT tg.record_id as id
     *       FROM omeka_records_tags tg
     *       INNER JOIN omeka_tags t ON t.id = tg.tag_id
     *       WHERE t.name = 'bar' AND tg.record_type = 'Collection')
     *    )
     *      ...
     *
     *
     * @param Omeka_Db_Select
     * @param string|array A comma-delimited string or an array of tag names.
     */
    public function filterByTags($select, $tags)
    {
        // Split the tags into an array if they aren't already
        if (!is_array($tags)) {
            $tags = explode(get_option('tag_delimiter'), $tags);
        }

        $db = $this->getDb();

        // For each of the tags, create a SELECT subquery using Omeka_Db_Select.
        // This subquery should only return collection IDs, so that the subquery can be
        // appended to the main query by WHERE i.id IN (SUBQUERY).
        foreach ($tags as $tagName) {
            $subSelect = new Omeka_Db_Select;
            $subSelect->from(array('records_tags' => $db->RecordsTags), array('collections.id' => 'records_tags.record_id'))
                ->joinInner(array('tags' => $db->Tag), 'tags.id = records_tags.tag_id', array())
                ->where('tags.name = ? AND records_tags.`record_type` = "Collection"', trim($tagName));

            $select->where('collections.id IN (' . (string) $subSelect . ')');
        }
    }

    /**
     * Filter SELECT statement based on collections that are not tagged with a specific
     * set of tags
     *
     * @param Zend_Db_Select
     * @param array|string Set of tag names (either array or comma-delimited string)
     */
    public function filterByExcludedTags($select, $tags)
    {
        $db = $this->getDb();

        if (!is_array($tags)) {
            $tags = explode(get_option('tag_delimiter'), $tags);
        }
        $subSelect = new Omeka_Db_Select;
        $subSelect->from(array('collections' => $db->Collection), 'collections.id')
                         ->joinInner(array('records_tags' => $db->RecordsTags),
                                     'records_tags.record_id = collections.id AND records_tags.record_type = "Collection"',
                                     array())
                         ->joinInner(array('tags' => $db->Tag),
                                     'records_tags.tag_id = tags.id',
                                     array());

        foreach ($tags as $key => $tag) {
            $subSelect->where('tags.name LIKE ?', $tag);
        }

        $select->where('collections.id NOT IN ('.$subSelect->__toString().')');
    }
}
