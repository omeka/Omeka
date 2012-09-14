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
            if (!is_array($records)) {
                throw new Zend_View_Exception(__('An array of records is not available for the loop.'));
            }
        }
        return new Omeka_Record_Iterator($records, $this->view, $recordsVar);
    }
}
