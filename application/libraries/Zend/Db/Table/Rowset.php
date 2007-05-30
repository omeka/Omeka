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
 * Zend_Db_Table_Row
 */
require_once 'Zend/Db/Table/Row.php';


/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Table
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @todo also implement Countable if ZF minimum PHP version moves up to 5.1.
 */
class Zend_Db_Table_Rowset implements Iterator
{
    /**
     * The original data for each row.
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
     * Zend_Db_Table object.
     * 
     * @var Zend_Db_Table
     */
    protected $_table;
    
    /**
     * Iterator pointer.
     */
    protected $_pointer = 0;
    
    /**
     * How many data rows there are.
     */
    protected $_count;
    
    
    /**
     * Collection of instantiated Zend_Db_Table_Row objects.
     */
    protected $_rows = array();
    
    /**
     * Constructor.
     */
    public function __construct($config = array())
    {
        $this->_db    = $config['db'];
        $this->_table = $config['table'];
        $this->_data  = $config['data'];
        
        // set the count of rows
        $this->_count = count($this->_data);
    }
    
    /**
     * Rewind the Iterator to the first element.
     * Similar to the reset() function for arrays in PHP.
     * 
     * @return void
     */
    public function rewind()
    {
        $this->_pointer = 0;
    }

    /**
     * Return the current element.
     * Similar to the current() function for arrays in PHP
     * 
     * @return mixed current element from the collection
     */
    public function current()
    {
        // is the pointer at a valid position?
        if (! $this->valid()) {
            return false;
        }
        
        // do we already have a row object for this position?
        if (empty($this->_rows[$this->_pointer])) {
            // create a row object
            $this->_rows[$this->_pointer] = new Zend_Db_Table_Row(array(
                'db'    => $this->_db,
                'table' => $this->_table,
                'data'  => $this->_data[$this->_pointer]
            ));
        }
        
        // return the row object
        return $this->_rows[$this->_pointer];
    }

    /**
     * Return the identifying key of the current element.
     * Similar to the key() function for arrays in PHP.
     * 
     * @return int
     */
    public function key()
    {
        return $this->_pointer;
    }

    /**
     * Move forward to next element.
     * Similar to the next() function for arrays in PHP.
     * 
     * @return int The next pointer value.
     */
    public function next()
    {
        return ++$this->_pointer;
    }

    /**
     * Check if there is a current element after calls to rewind() or next().
     * Used to check if we've iterated to the end of the collection.
     * 
     * @return bool False if there's nothing more to iterate over
     */
    public function valid()
    {
        return $this->_pointer < $this->count();
    }

    /**
     * Returns the number of elements in the collection.
     * 
     * @return int
     */
    public function count()
    {
        return $this->_count;
    }

    /**
     * Returns true if $this->count > 0, false otherwise.
     * 
     * @return bool
     */
    public function exists()
    {
        return $this->_count > 0;
    }
    
    /**
     * Returns all data as an array.
     * 
     * Updates the $_data property with current row object values.
     * 
     * @return array
     */
    public function toArray()
    {
        foreach ($this->_rows as $i => $row) {
            $this->_data[$i] = $row->toArray();
        }
        return $this->_data;
    }
}
