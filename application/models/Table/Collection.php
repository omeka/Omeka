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
        if(array_key_exists('public', $params)) {
            $this->filterByPublic($select, $params['public']);
        }
        
        if(array_key_exists('featured', $params)) {
            $this->filterByFeatured($select, $params['featured']);
        }
    }
    
    protected function _getColumnPairs()
    {
        // will replace the second value with the Dublin Core Title in findPairsForSelectForm()
        return array('collections.id', 'collections.id');
    }
    
    public function findPairsForSelectForm(array $options = array())
    {
        // replace the second value with the Dublin Core Title in findPairsForSelectForm()
        $pairs = parent::findPairsForSelectForm($options);
        $nPairs = array();
        foreach($pairs as $collectionId => $v) {
            $collection = $this->find($collectionId);
            if ($collection) {
                $collectionTitle = strip_formatting(metadata($collection, array('Dublin Core', 'Title')));
                if ($collectionTitle == '') {
                    $collectionTitle = __('[Untitled] #%s', strval($collectionId));
                }
                $nPairs[$collectionId] = $collectionTitle;
            }
        }
        return $nPairs;
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
     * Apply a filter to the collections based on whether or not they are public
     * 
     * @param Zend_Db_Select
     * @param boolean Whether or not to retrieve only public collections
     * @return void
     */
    public function filterByPublic($select, $isPublic)
    {         
        $isPublic = (bool) $isPublic; // this makes sure that empty strings and unset parameters are false

        //Force a preview of the public collections
        if ($isPublic) {
            $select->where('collections.public = 1');
        } else {
            $select->where('collections.public = 0');
        }
    }
    
    /**
     * Apply a filter to the collections based on whether or not they are featured
     * 
     * @param Zend_Db_Select
     * @param boolean Whether or not to retrieve only public collections
     * @return void
     */
    public function filterByFeatured($select, $isFeatured)
    {
        $isFeatured = (bool) $isFeatured; // this make sure that empty strings and unset parameters are false
        
        //filter items based on featured (only value of 'true' will return featured collections)
        if ($isFeatured) {
            $select->where('collections.featured = 1');
        } else {
            $select->where('collections.featured = 0');
        }     
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
        } else {
            if ($sortField == 'random') {
                $select->order('RAND()');
            }
        }
    }
}
