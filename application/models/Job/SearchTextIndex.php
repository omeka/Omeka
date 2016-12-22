<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * @package Omeka\Job
 */
class Job_SearchTextIndex extends Omeka_Job_AbstractJob
{
    /**
     * Bulk index all valid records.
     */
    public function perform()
    {
        // when passing specific records, do only update
        // (works safely only with hundreds of records, because `args` column is TEXT type)
        if (!empty($this->_options['records'])) {
            return $this->_performUpdate($this->_options['records']);
        }
        // when passing custom SQL, do update on the found records
        // (solves TEXT data type limitation when passing thousands of records to update)
        if (!empty($this->_options['sql'])) {
            $recordMap = $this->_getRecordMapFromSql($this->_options['sql']);
            return $this->_performUpdate($recordMap);
        }
        
        // Truncate the `search_texts` table before indexing to clean out 
        // obsolete records.
        $sql = "TRUNCATE TABLE {$this->_db->SearchText}";
        $this->_db->query($sql);
        
        foreach (get_custom_search_record_types() as $key => $value) {
            
            $recordType = is_string($key) ? $key : $value;
            
            $record = $this->_getIndexedRecordByType($recordType);
            if (!$record) {
                continue;
            }
            
            $pageNumber = 1;
            $recordTable = $record->getTable();
            // Query a limited number of rows at a time to prevent memory issues.
            while ($recordObjects = $recordTable->fetchObjects($recordTable->getSelect()->limitPage($pageNumber, 100))) {
                foreach ($recordObjects as $recordObject) {
                    // Save the record object, which indexes its search text.
                    try {
                        $recordObject->save();
                    } catch (Omeka_Validate_Exception $e) {
                        _log($e, Zend_Log::ERR);
                        _log(sprintf('Failed to index %s #%s',
                                get_class($recordObject), $recordObject->id),
                            Zend_Log::ERR);
                    }
                    release_object($recordObject);
                    // TODO/Question - what about short usleep(5000); here as well?? spends less than half of cpu
                }
                $pageNumber++;
            }
        }
    }
    /**
     * Updates index for given record_type + record_id(s).
     *
     * @param mixed $recordMap Map of record types and their ids, in format:
     * <code>
     *  [
     *      (string) recordType => [(int) recordId, (int) recordId, ...],
     *      (string) recordType2 => [(int) recordId, (int) recordId, ...],
     *      ...
     *  ]
     * </code>
     * @return void
     */
    protected function _performUpdate($recordMap)
    {
        foreach (get_custom_search_record_types() as $key => $value) {
            $recordType = is_string($key) ? $key : $value;
    
            if (empty($recordMap[$recordType])) {
                continue;
            }
            $record = $this->_getIndexedRecordByType($recordType);
            if (!$record) {
                continue;
            }
    
            $recordTable = $record->getTable();
            $recordTableAlias = $recordTable->getTableAlias();
            $pageNumber = 0;
            $perPpage = 100;
            // Find all records by given list of ids (paginated by 100).
            while ($ids = array_slice($recordMap[$recordType], $pageNumber * $perPpage, $perPpage)) {
                $recordObjects = $recordTable->fetchObjects($recordTable->getSelect()->where("$recordTableAlias.id IN (?)", $ids));
                foreach ($recordObjects as $recordObject) {
                    // Save the record object, which indexes its search text.
                    try {
                        $recordObject->save();
                    } catch (Omeka_Validate_Exception $e) {
                        _log($e, Zend_Log::ERR);
                        _log(sprintf('Failed to index %s #%s',
                                get_class($recordObject), $recordObject->id),
                                Zend_Log::ERR);
                    }
                    release_object($recordObject);
                    usleep(5000);
                }
                $pageNumber++;
            }
        }
    }
    /**
     * Retrieves map of records to update from given SQL query.
     *
     * @param string $sql Select query that must specify `record_type` and `record_id` columns.
     *  Example1: SELECT `record_type`, `record_id` FROM `records_tags`
     *  Example2: SELECT 'Item' AS `record_type`, `items`.`id` AS `record_id` FROM `items`
     * @return ArrayObject $recordMap Map of records, where key is record type and values is array of record ids.
     *  Returns empty array if nothing found.
     */
    protected function _getRecordMapFromSql($sql)
    {
        $recordMap = new ArrayObject();
        $records = $this->_db->fetchAll($sql);
        foreach ($records as $record) {
            if (isset($record['record_type'], $record['record_id'])) {
                $recordMap[$record['record_type']][] = $record['record_id'];
            }
        }
        return $recordMap;
    }
    /**
     * Retrieves record that should be indexed by given record type.
     *
     * @param string $recordType
     * @return null|Omeka_Record_AbstractRecord Returns null if record doesn't exist or doesn't implement search mixin.
     */
    protected function _getIndexedRecordByType($recordType)
    {
        if (!class_exists($recordType)) {
            // The class does not exist or cannot be found.
            return null;
        }
        $record = new $recordType;
        if (!($record instanceof Omeka_Record_AbstractRecord)) {
            // The class is not a valid record.
            return null;
        }
        if (!is_callable(array($record, 'addSearchText'))) {
            // The record does not implement the search mixin.
            return null;
        }
        return $record;
    }
}
