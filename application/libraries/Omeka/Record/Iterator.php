<?php
class Omeka_Record_Iterator implements Iterator
{
    private $_recordVar;
    private $_records;
    private $_view;
    
    /**
     * Construct the record iterator.
     * 
     * @param string $recordsVar
     * @param array $records
     * @param Zend_View_Abstract|null $view
     */
    public function __construct($recordsVar, $records, $view = null)
    {
        $this->_recordVar = Inflector::singularize(Inflector::tableize($recordsVar));
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
            $this->_view->{$this->_recordVar} = current($this->_records);
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
