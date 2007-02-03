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
 * @package    Zend_Log
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/** Zend_Log_Adapter_Interface */
require_once 'Zend/Log/Adapter/Interface.php';

/** Zend_Log_Adapter_Exception */
require_once 'Zend/Log/Adapter/Exception.php';

/** Zend_Db_Adapter_Abstract */
require_once 'Zend/Db/Adapter/Abstract.php';


/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Log_Adapter_Db implements Zend_Log_Adapter_Interface
{

    /**
     * Database adapter instance.
     *
     * @var Zend_Db_Adapter
     */
    protected $_dbAdapter = null;

    /**
     * Name of the log table in the database.
     *
     * @var string
     */
    protected $_tableName = null;

    /**
     * Options to be set by setOption().  Sets the field names in the database table.
     *
     * @var array
     */
    protected $_options = array('fieldMessage'  => 'message',
                                'fieldLevel'    => 'level');


    /**
     * Class constructor.  Pass it a database adapter and the table name of the log table.
     *
     * @param Zend_Db_Adapter $dbAdapter
     * @param string $tableName
     */
    public function __construct(Zend_Db_Adapter_Abstract $dbAdapter, $tableName=null)
    {
        // Get the object handle to the database adapter
        if (is_null($tableName)) {
            throw new Zend_Log_Adapter_Exception('Database table name must be specified.');
        }
        $this->_dbAdapter = $dbAdapter;
        $this->_tableName = $tableName;
    }


    /**
     * Sets either 'fieldMessage' to change the field name where messages are logged,
     * or 'fieldLevel' to change the field name where the log levels are logged.
     *
     * @param string $optionKey
     * @param string $optionValue
     */
    public function setOption($optionKey, $optionValue)
    {
        if (!array_key_exists($optionKey, $this->_options)) {
            throw new Zend_Log_Adapter_Exception("Unknown option \"$optionKey\".");
        }
        $this->_options[$optionKey] = $optionValue;
    }


	/**
	 * Does nothing.
	 *
	 * @return bool
	 */
	public function open()
	{
        return true;
	}


	/**
	 * Does nothing.
	 *
	 * @return bool
	 */
	public function close()
	{
	    return true;
	}


	/**
	 * Writes an array of key/value pairs to the database, where the keys are the
	 * database field names and values are what to put in those fields.
	 *
	 * @param array $fields
	 * @return bool
	 */
	public function write($fields)
	{
	    /**
	     * If the field defaults for 'message' and 'level' have been changed
	     * in the options, replace the keys in the $field array.
	     */
	    if ($this->_options['fieldMessage'] != 'message') {
	        $fields[$this->_options['fieldMessage']] = $fields['message'];
	        unset($fields['message']);
	    }

	    if ($this->_options['fieldLevel'] != 'level') {
	        $fields[$this->_options['fieldLevel']] = $fields['level'];
	        unset($fields['level']);
	    }

	    /**
	     * Build an array of field names and values for the SQL statement.
	     */
        $fieldNames = array();
	    foreach ($fields as $key=>&$value) {
	        /**
	         * @todo needs to be updated for new database adapters
	         */
	        $fieldNames[] = "`" .$this->_dbAdapter->quote($key). "`";
	        $value = "'" .$this->_dbAdapter->quote($value). "'";
	        if ($value=="''") {
	            $value = "NULL";
	        }
	    }

	    /**
	     * INSERT the log line into the database.  XXX Replace with Prepared Statement
	     */
        /**
         * @todo needs to be updated for new database adapters
         */
	    $sql = "INSERT INTO `" .$this->_dbAdapter->quote($this->_tableName). "` ("
	         . implode(', ', $fieldNames) . ') VALUES ('
	         . implode(', ', $fields) .')';

	    // The database adapter will raise an exception if any problems occur.
        /**
         * @todo needs to be updated for new database adapters
         */
	    $this->_dbAdapter->insert($sql);
        return true;
	}


}

