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
        return $pairs;
    }
    
    /**
     * Apply permissions checks to all SQL statements retrieving collections from the table
     * 
     * @param string
     * @return void
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
}
