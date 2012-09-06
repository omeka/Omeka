<?php
class Omeka_Record_Iterator implements Iterator
{
    private $_recordParam;
    private $_records;
    private $_view;
    
    /**
     * Construct the record iterator.
     * 
     * @param string $recordType
     * @param array $records
     * @param Zend_View_Abstract|null $view
     */
    public function __construct($recordType, $records, $view = null)
    {
        $this->_recordParam = Inflector::underscore($recordType);
        $this->_records = $records;
        $this->_view = $view;
    }
    
    public function rewind()
    {
        reset($this->_records);
    }
    
    /**
     * Return the current record, setting it to the view if applicable.
     */
    public function current()
    {
        if ($this->_view instanceof Zend_View_Abstract) {
            $this->_view->{$this->_recordParam} = current($this->_records);
        }
        return current($this->_records);
    }
    
    public function key()
    {
        return key($this->_records);
    }
    
    /**
     * Release the previous record and advance the pointer to the next one.
     */
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
