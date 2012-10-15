<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * @package Omeka\Record
 */
class Omeka_Record_Iterator implements Iterator
{
    protected $_records;
    protected $_view;
    protected $_currentRecordVar;
    
    /**
     * Construct the record iterator.
     * 
     * @uses Omeka_View_Helper_Singularize::singularize()
     * @param array $records
     * @param null|Zend_View_Abstract $view
     * @param null|string $currentRecordVar
     */
    public function __construct(array $records, $view = null, $currentRecordVar = null)
    {
        // Normalize the current record variable for the view.
        if ($view instanceof Zend_View_Abstract) {
            $currentRecordVar = $view->singularize($currentRecordVar);
        }
        
        $this->_records = $records;
        $this->_currentRecordVar = $currentRecordVar;
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
        if (!(current($this->_records) instanceof Omeka_Record_AbstractRecord)) {
            throw new Omeka_Record_Exception(__('An invalid value was detected during record iteration.'));
        }
        
        if ($this->_view instanceof Zend_View_Abstract) {
            $this->_view->{$this->_currentRecordVar} = current($this->_records);
        }
        return current($this->_records);
    }
    
    public function key()
    {
        return key($this->_records);
    }
    
    /**
     * Release the previous record and advance the pointer to the next one.
     * 
     * @uses release_object()
     */
    public function next()
    {
        release_object($this->_records[$this->key()]);
        next($this->_records);
    }
    
    public function valid()
    {
        return false !== current($this->_records);
    }
}
