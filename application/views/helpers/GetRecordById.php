<?php
class Omeka_View_Helper_GetRecordById extends Zend_View_Helper_Abstract
{
    protected $_db;
    
    public function __construct()
    {
        $this->_db = Zend_Registry::get('bootstrap')->getResource('Db');
    }
    
    /**
     * Get a record by its ID.
     * 
     * @param string $recordVar
     * @param int $recordId
     * @return Omeka_Record_AbstractRecord|null
     */
    public function getRecordById($recordVar, $recordId)
    {
        return $this->_db->getTable(Inflector::camelize($recordVar))->find($recordId);
    }
}
