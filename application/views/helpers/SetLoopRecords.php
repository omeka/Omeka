<?php
class Omeka_View_Helper_SetLoopRecords extends Zend_View_Helper_Abstract
{
    /**
     * Set records to the view for iteration.
     * 
     * @param string $recordsVar
     * @param array $records
     */
    public function setLoopRecords($recordsVar, array $records)
    {
        $recordsVar = $this->view->pluralize($recordsVar);
        $this->view->$recordsVar = $records;
    }
}
