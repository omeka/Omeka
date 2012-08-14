<?php
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
    
    /**
     * Perform a fulltext search.
     * 
     * @param string $query
     * @param string $recordType Limit results to this record type.
     * @return array
     */
    public function search($query, $recordType = null)
    {
        $showNotPublic = Zend_Registry::get('bootstrap')->getResource('Acl')
            ->isAllowed(current_user(), 'Search', 'showNotPublic');
        $searchRecordTypes = Mixin_Search::getSearchRecordTypes();
        
        $sql = "
        SELECT record_type, record_id, title, MATCH (text) AGAINST (?) AS relevance
        FROM {$this->getTableName()} 
        WHERE MATCH (text) AGAINST (?)";
        if ($searchRecordTypes) {
            $sql .= $this->getDb()->quoteInto(" AND record_type IN (?)", $searchRecordTypes);
        }
        if ($recordType) {
            $sql .= $this->getDb()->quoteInto(" AND record_type = ?", $recordType);
        }
        if (!$showNotPublic) {
            $sql .= " AND public = 1";
        }
        $results = $this->getDb()->fetchAll($sql, array($query, $query));
        foreach ($results as $key => $result) {
            $results[$key]['record'] = $this->getTable($result['record_type'])
                                            ->find($result['record_id']);
        }
        return $results;
    }
}
