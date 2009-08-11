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
class CollectionTable extends Omeka_Db_Table
{    
    public function applySearchFilters($select, $params)
    {
        /****************************
         * FIND RECENT COLLECTIONS
         *
         * ORDER BY id DESC works because MyISAM tables always increment IDs for new rows,
         * would not work with InnoDB because it assigns IDs of deleted records
         ****************************/
         
         if ($params['recent'] === true) {             
             $select->order('c.id DESC');
         }        
    }
    
    protected function _getColumnPairs()
    {
        return array('c.id', 'c.name');
    }

    /**
     * Apply permissions checks to all SQL statements retrieving collections from the table
     * 
     * @param string
     * @return void
     **/
    public function getSelect()
    {
        $select = parent::getSelect();
        
        if($acl = Omeka_Context::getInstance()->getAcl()) {
            new CollectionPermissions($select, $acl);
        }
        
        return $select;
    }
    
    public function findRandomFeatured()
    {
        $select = $this->getSelect()->where("c.featured = 1")->order("RAND()")->limit(1);        
        return $this->fetchObject($select);
    }
    
    
    /**
     * Adds an lucene subquery to the search query for the advanced search
     *
     * @param Zend_Search_Lucene_Search_Query_Boolean $advancedSearchQuery
     * @param string|array $requestParams An associative array of request parameters
     */
    public function addAdvancedSearchQueryForLucene($advancedSearchQuery, $requestParams) 
    {
        if ($search = Omeka_Search::getInstance()) {

            // Build an advanced search query for the item
            $advancedSearchQueryForCollection = new Zend_Search_Lucene_Search_Query_Boolean();
            foreach($requestParams as $requestParamName => $requestParamValue) {
                switch($requestParamName) {
                    case 'public':
                        if (is_true($requestParamValue)) {
                            $subquery = $search->getLuceneTermQueryForFieldName(Omeka_Search::FIELD_NAME_IS_PUBLIC, Omeka_Search::FIELD_VALUE_TRUE, true);
                            $advancedSearchQueryForCollection->addSubquery($subquery, true);
                        }
                    break;

                    case 'featured':
                        if (is_true($requestParamValue)) {
                            $subquery = $search->getLuceneTermQueryForFieldName(Omeka_Search::FIELD_NAME_IS_FEATURED, Omeka_Search::FIELD_VALUE_TRUE, true);
                            $advancedSearchQueryForCollection->addSubquery($subquery, true);
                        }
                    break;

                    case 'collectors':
                        
                        if (is_array($requestParamValue)) {
                            $collectorIds = $requestParamValue;
                            $addedCollector = false;
                            $collectorsSubquery = new Zend_Search_Lucene_Search_Query_Boolean();
                            foreach($collectorIds as $collectorId) {
                                if (is_numeric($collectorId) && ((int)$collectorId > 0)) {
                                    $addedCollector = true;
                                    $subquery = $search->getLuceneTermQueryForFieldName(array('Collection', 'collector_id'), $collectorId, true);
                                    $collectorsSubquery->addSubquery($subquery);
                                }
                            }
                            if ($addedCollector) {
                                $advancedSearchQueryForCollection->addSubquery($collectorsSubquery, true);                                
                            }
                        }

                    break;
                }
            }

            // add the collection advanced search query to the searchQuery as a disjunctive subquery 
            // (i.e. there will be OR statements between each of models' the advanced search queries)
            $advancedSearchQuery->addSubquery($advancedSearchQueryForCollection);
        }        
    }
    
}