<?php
class Omeka_View_Helper_LoopRecords extends Zend_View_Helper_Abstract implements Iterator
{
    private $_records = array();
    private $_recordParam;
    
    /**
     * Return an iterator meant to be used for looping records.
     * 
     * This sets the current record to the view and returns it. It also releases 
     * the previous record to prevent memory leaks.
     * 
     * @param string $recordType
     * @param array|null $records
     * @return Omeka_View_Helper_LoopRecords
     */
    public function loopRecords($recordType, $records = null)
    {
        if (!is_array($records)) {
            $records = $this->view->{Inflector::tableize($recordType)};
        }
        $this->_records = $records;
        $this->_recordParam = Inflector::underscore($recordType);
        return $this;
    }
    
    public function rewind()
    {
        reset($this->_records);
    }
    
    public function current()
    {
        $this->view->{$this->_recordParam} = current($this->_records);
        return current($this->_records);
    }
    
    public function key()
    {
        return key($this->_records);
    }
    
    public function next()
    {
        release_object($this->_records[$this->key()]);
        next($this->_records);
    }
    
    public function valid()
    {
        return false !== $this->current();
    }
}
