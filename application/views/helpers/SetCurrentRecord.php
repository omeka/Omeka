<?php
class Omeka_View_Helper_SetCurrentRecord extends Zend_View_Helper_Abstract
{
    /**
     * Set a record to the view as the current record.
     * 
     * @param string $recordVar
     * @param Omeka_Record_AbstractRecord $record
     * @param bool $setPreviousRecord
     */
    public function setCurrentRecord($recordVar, Omeka_Record_AbstractRecord $record, $setPreviousRecord = false)
    {
        $recordVar = $this->view->singularize($recordVar);
        if ($setPreviousRecord) {
            $previousRecordVar = "previous_$recordVar";
            $this->view->$previousRecordVar = $this->view->$recordVar;
        }
        $this->view->$recordVar = $record;
    }
}
