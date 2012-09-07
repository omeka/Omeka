<?php
class Omeka_View_Helper_LoopRecords extends Zend_View_Helper_Abstract
{
    /**
     * Return an iterator used for looping an array of records.
     * 
     * @param string $recordsVar
     * @param array|null $records
     * @return Omeka_Record_Iterator
     */
    public function loopRecords($recordsVar, $records = null)
    {
        $recordsVar = $this->view->pluralize($recordsVar);
        if (!is_array($records)) {
            $records = $this->view->$recordsVar;
        }
        return new Omeka_Record_Iterator($recordsVar, $records, $this->view);
    }
}
