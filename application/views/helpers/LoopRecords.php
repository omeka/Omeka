<?php
class Omeka_View_Helper_LoopRecords extends Zend_View_Helper_Abstract implements Iterator
{
    private $_recordParamPlural;
    private $_recordParamSingular;
    private $_records;
    
    /**
     * Return an iterator meant to be used for looping records.
     * 
     * The view must already have corresponding array of records assigned to it. 
     * This iterator sets the current record to the view and returns it. It also 
     * releases the previous record to prevent memory leaks.
     * 
     * @param string $recordType
     * @return Omeka_View_Helper_LoopRecords
     */
    public function loopRecords($recordType)
    {
        $this->_recordParamPlural = Inflector::tableize($recordType);
        $this->_recordParamSingular = Inflector::underscore($recordType);
        $this->_records = $this->view->{$this->_recordParamPlural};
        return $this;
    }
    
    public function rewind()
    {
        reset($this->_records);
    }
    
    public function current()
    {
        $this->view->{$this->_recordParamSingular} = current($this->_records);
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
