<?php
class Job_SearchTextIndex extends Omeka_JobAbstract
{
    /**
     * List of all class names in the application/models directory that extend 
     * Omeka_Record_AbstractRecord and implement the search mixin.
     * @var array
     */
    protected $_searchRecords = array('Item', 'File', 'Collection');
    
    public function perform()
    {
        // Get the registry of records that implement the search mixin.
        $pluginBroker = Zend_Registry::get('pluginbroker');
        $searchRecords = $pluginBroker->applyFilters('search_records', $this->_searchRecords);
        
        foreach ($searchRecords as $searchRecord) {
            
            if (!class_exists($searchRecord)) {
                // The class does not exist or cannot be found.
                continue;
            }
            $record = new $searchRecord;
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
                }
                $pageNumber++;
            }
        }
    }
}
