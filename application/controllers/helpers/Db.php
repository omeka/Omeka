<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * An action helper replacement for the database-oriented methods that were 
 * baked into Omeka_Controller_AbstractActionController in v1.x.
 * 
 * @package Omeka\Controller\ActionHelper
 */
class Omeka_Controller_Action_Helper_Db extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * @var Omeka_Db
     */
    private $_db;
    
    /**
     * @var Omeka_Db_Table
     */
    private $_defaultTable;
    
    /**
     * @var string
     */
    private $_defaultModel;
    
    /**
     * @var integer
     */
    private $_findByLimit;
    
    public function __construct(Omeka_Db $db)
    {
        $this->_db = $db;
    }

    public function init()
    {
        $this->_defaultTable = null;
        $this->_defaultModel = null;
    }
    
    /**
     * Delegate to the default table object for all other method calls.
     */
    public function __call($method, $args)
    {
        if (!$this->_defaultTable) {
            throw new BadMethodCallException("No default table has been set.");
        }
        if (!method_exists($this->_defaultTable, $method)) {
            throw new BadMethodCallException("Method named '$method' does not exist in the default table, which is an instance of '" . get_class($this->_defaultTable) . "'.");
        }
        return call_user_func_array(array($this->_defaultTable, $method), $args);
    }
    
    /**
     * Set the class name corresponding to the default model.
     */
    public function setDefaultModelName($modelName)
    {
        $this->_defaultModel = $modelName;
        $this->setDefaultTable($this->_db->getTable($modelName));
    }
    
    public function getDefaultModelName()
    {
        return $this->_defaultModel;
    }
    
    public function setDefaultTable(Omeka_Db_Table $table)
    {
        $this->_defaultTable = $table;
    }
    
    public function getDb()
    {
        return $this->_db;
    }
    
    /**
     * @param string|null $tableName
     * @return Omeka_Db_Table
     */
    public function getTable($tableName = null)
    {
        if(!$tableName) {
            if (!$this->_defaultTable) {
                throw new InvalidArgumentException("Default table must be assigned.");
            }
            return $this->_defaultTable;
        } else {
            return $this->_db->getTable($tableName);
        }
    }
            
    /**
     * Find a particular record given its unique ID # and (optionally) its class name.  
     * 
     * @uses Omeka_Db_Table::find()
     * @uses Omeka_Db_Table::checkExists()
     * @param int The ID of the record to find (optional)
     * @param string The model class corresponding to the table that should be checked (optional)
     * @throws Omeka_Controller_Exception_404
     * @throws Omeka_Controller_Exception_403
     * @return Omeka_Record_AbstractRecord
     */
    public function findById($id = null, $table = null)
    {
        $id = (!$id) ? $this->getRequest()->getParam('id') : $id;
        
        if (!$id) {
            throw new Omeka_Controller_Exception_404(get_class($this) . ': No ID passed to this request' );
        }
        
        $table = $this->getTable($table);

        $record = $table->find($id);
        
        if (!$record) {
            
            //Check to see whether to record exists at all
            if (!$table->checkExists($id)) {
                throw new Omeka_Controller_Exception_404(get_class($this) . ": No record with ID # $id exists" );
            } else {
                throw new Omeka_Controller_Exception_403('You do not have permission to access this page.');
            }
            
        }
        
        return $record;
    }
}
