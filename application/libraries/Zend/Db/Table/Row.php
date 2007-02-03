<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Table
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */ 


/**
 * Zend_Db_Table_Row_Exception
 */
require_once 'Zend/Db/Table/Row/Exception.php';


/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Table
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Db_Table_Row
{
    /**
     * The data for each column in the row (underscore_words => value).
     * 
     * @var array
     */
    protected $_data = array();
    
    /**
     * Zend_Db_Adapter object from the table interface.
     * 
     * @var Zend_Db_Adapter
     */
    protected $_db;
    
    /**
     * Zend_Db_Table interface (the row "parent").
     * 
     * @var Zend_Db_Table
     */
    protected $_table;
    
    /**
     * Zend_Db_Table info (name, cols, primary, etc).
     * 
     * @var array
     */
    protected $_info = array();
    
    /**
     * Constructor.
     */
    public function __construct($config = array())
    {
        $this->_db    = $config['db'];
        $this->_table = $config['table'];
        $this->_info  = $this->_table->info();
        
        if ($config['data'] === false) {
            // empty row, use blanks
            $cols = array_keys($this->_info['cols']);
            $data = array_fill(0, count($cols), null);
            $this->_data = array_combine ($cols, $data);
        } else {
            $this->_data  = (array) $config['data'];
        }
    }
    
    /**
     * Getter for camelCaps properties mapped to underscore_word columns.
     * 
     * @param string $camel The camelCaps property name; e.g., 'columnName'
     * maps to 'column_name'.
     * @return string The mapped column value.
     */
    public function __get($camel)
    {
        $under = array_search($camel, $this->_info['cols']);
        if ($under) {
            return $this->_data[$under];
        } else {
            throw new Zend_Db_Table_Row_Exception("column '$camel' not in row");
        }
    }
    
    /**
     * Setter for camelCaps properties mapped to underscore_word columns.
     * 
     * @param string $camel The camelCaps property name; e.g., 'columnName'
     * maps to 'column_name'.
     * @param mixed $value The value for the property.
     * @return void
     */
    public function __set($camel, $value)
    {
        $under = array_search($camel, $this->_info['cols']);
        if ($under == $this->_info['primary']) {
            throw new Zend_Db_Table_Row_Exception("not allowed to change primary key value");
        } elseif ($under === false) {
            throw new Zend_Db_Table_Row_Exception("column '$camel' not in row");
        } else {
            $this->_data[$under] = $value;
        }
    }
    
    /**
     * Saves the properties to the database.
     * 
     * This performs an intelligent insert/update, and reloads the 
     * properties with fresh data from the table on success.
     * 
     * @return int 0 on failure, 1 on success.
     */
    public function save()
    {
        // convenience var for the primary key name
        $primary = $this->_info['primary'];
        
        // check the primary key value for insert/update
        if (empty($this->_data[$primary])) {
        
            // no primary key value, must be an insert.
            // make sure it's null.
            $this->_data[$primary] = null;
            
            // attempt the insert.
            $result = $this->_table->insert($this->_data);
            if (is_numeric($result)) {
                // insert worked, refresh with data from the table
                $this->_data[$primary] = $result;
                $this->_refresh();
            }
            
            
        } else {
            
            // has a primary key value, update only that key.
            $where = $this->_db->quoteInto(
                "$primary = ?",
                $this->_data[$primary]
            );
            
            // return the result of the update attempt,
            // no need to update the row object.
            $result = $this->_table->update($this->_data, $where);
            if (is_int($result)) {
                // update worked, refresh with data from the table
                $this->_refresh();
            }
        }
        
        // regardless of success return the result
        return $result;
    }
    
    /**
     * Returns the column/value data as an array.
     * 
     * @return array
     */
    public function toArray()
    {
        return $this->_data;
    }
    
    /**
     * Sets all data in the row from an array.
     * 
     * @param array $data
     */
    public function setFromArray($data)
    {
        foreach ($data as $key => $val) {
            if (array_key_exists($key, $this->_data)) {
                $this->_data[$key] = $val;
            }
        }
    }
    
    /**
     * Refreshes properties from the database.
     */
    protected function _refresh()
    {
        $fresh = $this->_table->find($this->_data[$this->_info['primary']]);
        // we can do this because they're both Zend_Db_Table_Row objects
        $this->_data = $fresh->_data;
    }
}

