<?php
class Table_SearchText extends Omeka_Db_Table
{
    public function findByRecord($recordType, $recordId)
    {
        $select = $this->getSelect();
        $select->where('record_type = ?', $recordType);
        $select->where('record_id = ?', $recordId);
        return $this->fetchObject($select);
    }
    
    public function search($query)
    {
        $acl = Zend_Registry::get('bootstrap')->getResource('Acl');
        $showNotPublic = $acl->isAllowed(current_user(), 'Search', 'showNotPublic');
        
        $sql = "
        SELECT record_type, record_id, title, MATCH (text) AGAINST (?) AS relevance
        FROM {$this->getTableName()} 
        WHERE MATCH (text) AGAINST (?)";
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
