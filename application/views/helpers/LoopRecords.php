<?php
class Omeka_View_Helper_LoopRecords extends Zend_View_Helper_Abstract
{
    /**
     * Return an iterator used for looping and array of records.
     * 
     * @param string $recordType
     * @param array|null $records
     * @return Omeka_Record_Iterator
     */
    public function loopRecords($recordType, $records = null)
    {
        if (!is_array($records)) {
            $records = $this->view->{Inflector::tableize($recordType)};
        }
        return new Omeka_Record_Iterator($recordType, $records, $this->view);
    }
}
