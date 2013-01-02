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
        // Truncate the `search_texts` table before indexing to clean out 
        // obsolete records.
        $sql = "TRUNCATE TABLE {$this->_db->SearchText}";
        $this->_db->query($sql);
        
        foreach (get_custom_search_record_types() as $key => $value) {
            
            $recordType = is_string($key) ? $key : $value;
            
            if (!class_exists($recordType)) {
                // The class does not exist or cannot be found.
                continue;
            }
            $record = new $recordType;
            if (!($record instanceof Omeka_Record_AbstractRecord)) {
                // The class is not a valid record.
                continue;
            }
            if (!is_callable(array($record, 'addSearchText'))) {
                // The record does not implement the search mixin.
                continue;
            }
            
            $pageNumber = 1;
            $recordTable = $record->getTable();
            // Query a limited number of rows at a time to prevent memory issues.
            while ($recordObjects = $recordTable->fetchObjects($recordTable->getSelect()->limitPage($pageNumber, 100))) {
                foreach ($recordObjects as $recordObject) {
                    // Save the record object, which indexes its search text.
                    $recordObject->save();
                    release_object($recordObject);
                }
                $pageNumber++;
            }
        }
    }
}
