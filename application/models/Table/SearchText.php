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
class Table_SearchText extends Omeka_Db_Table
{
    /**
     * Find search text by record.
     * 
     * @param string $recordType
     * @param int $recordId
     * @return SearchText|null
     */
    public function findByRecord($recordType, $recordId)
    {
         $select = $this->getSelect();
         $select->where('record_type = ?', $recordType);
         $select->where('record_id = ?', $recordId);
         return $this->fetchObject($select);
    }
    
    public function applySearchFilters($select, $params)
    {
        // Set the query string if not passed.
        if (!isset($params['query'])) {
            $params['query'] = '';
        }
        
        // Set the query type if not passed.
        if (!isset($params['query_type'])) {
            $params['query_type'] = 'full_text';
        }
        
        // Set the base select statement.
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->columns(array('record_type', 'record_id', 'title'));
        
        // Set the where clause according to the query type.
        if ('exact_match' == $params['query_type']) {
            $where = '`text` LIKE ?';
            $params['query'] = "%{$params['query']}%";
        } else if ('boolean' == $params['query_type']) {
            $where = 'MATCH (`text`) AGAINST (? IN BOOLEAN MODE)';
        } else {
            $where = 'MATCH (`text`) AGAINST (?)';
        }
        $select->where($where, $params['query']);
        
        // Search only those record types that are configured to be searched.
        $searchRecordTypes = get_custom_search_record_types();
        if ($searchRecordTypes) {
            $select->where('`record_type` IN (?)', array_keys($searchRecordTypes));
        }
        
        // Search on an specific record type.
        if (isset($params['record_types'])) {
            $select->where('`record_type` IN (?)', $params['record_types']);
        }
        
        // Restrict access to private records.
        $showNotPublic = Zend_Registry::get('bootstrap')->getResource('Acl')
            ->isAllowed(current_user(), 'Search', 'showNotPublic');
        if (!$showNotPublic) {
            $select->where('`public` = 1');
        }
    }
}
