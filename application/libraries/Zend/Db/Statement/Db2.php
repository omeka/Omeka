<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to version 1.0 of the Zend Framework
 * license, that is bundled with this package in the file LICENSE, and
 * is available through the world-wide-web at the following URL:
 * http://www.zend.com/license/framework/1_0.txt. If you did not receive
 * a copy of the Zend Framework license and are unable to obtain it
 * through the world-wide-web, please send a note to license@zend.com
 * so we can mail you a copy immediately.
 *
 * @package    Zend_Db
 * @subpackage Statement
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */


/** Zend_Db_Statement */
require_once 'Zend/Db/Statement.php';

/** Zend_Db_Statement_Db2_Exception */
require_once 'Zend/Db/Statement/Db2/Exception.php';


/**
 * Extends for DB2.
 *
 * @package    Zend_Db
 * @subpackage Statement
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 * @author 	   Joscha Feth <jffeth@de.ibm.com>
 * @author     Salvador Ledezma <ledezma@us.ibm.com>
 */
class Zend_Db_Statement_Db2 extends Zend_Db_Statement
{
	/**
	 * statement resource handle 
	 */
	protected $_stmt;
	
	/**
  	* column names
    */
    protected $_keys;

    /**
     * fetched result values
     */
    protected $_values;

	/**
     * retrieves the next rowset (result set)
     * @todo not familiar with how to do nextrowset
     */
    public function nextRowset()
    {
        throw new Zend_Db_Statement_Exception(__FUNCTION__ . ' not implemented');
    }


    /**
     * 
     * @return integer number of rows updated
     */
    public function rowCount()
    {
    	if(!$this->_stmt) {
    		return false;
    	}
    	
    	$num = db2_num_rows($this->_stmt);
    	
    	if ($num === false) {
    		throw new Zend_Db_Statement_Db2_Exception(db2_stmt_errormsg($this->_stmt), 
    												  db2_stmt_error($this->_stmt));	
    	}
   
    	return db2_num_rows($this->_stmt);
    }

    /**
     * closes the cursor, allowing the statement to be executed again
     */
    public function closeCursor()
    {
    	if(!$this->_stmt) {
    		return false;
    	}
    	
    	db2_free_stmt($this->_stmt);
        $this->_stmt = false;
    }


   	/**
   	* returns the number of columns in the result set
	*
	* @return integer
    */
    public function columnCount()
    {
    	if (!$this->_stmt) {
    		return false;
    	}
        return db2_num_fields($this->_stmt);
    }


    /**
     * retrieves a sql state, if any, from the statement
     * 
     * @return string the error code
     */
    public function errorCode()
    {
        if (!$this->_stmt) {
        	return false;
        }
        
        return db2_stmt_error($this->_stmt);
    }


    /**
     * retrieves a error msg, if any, from the statement
     *
     * @return string the statement error message
     */
    public function errorInfo()
    {
        if (!$this->_stmt) {
        	return false;
        }
        
        return db2_stmt_errormsg($this->_stmt);
    }


    /**
     * executes a prepared statement
     * 
     * @return void
     */
    public function execute($params = null)
    {
        if (!$this->_stmt) {
        	$connection = $this->_connection->getConnection();
        	$sql = $this->_joinSql();
        	$this->_stmt = db2_prepare($connection, $sql);
        }
        
        if(!$this->_stmt) {
        	throw new Zend_Db_Statement_Db2_Exception(db2_conn_errormsg($connection), db2_conn_error($connection));
        }
        
        if (!is_array($params)) {
        	$params = array($params);
        }
         
        $success = db2_execute($this->_stmt, $params); 
  	      
        if (!$success) {
        	throw new Zend_Db_Statement_Db2_Exception(db2_stmt_errormsg($this->_stmt), 
        	                                          db2_stmt_error($this->_stmt));
        }
        
        $this->_keys = array();
        if ($field_num = $this->columnCount()) {
        	for ($i = 0; $i < $field_num; $i++) {
        		$name = db2_field_name($this->_stmt, $i);
        		$this->_keys[] = $name;
        	}
        }
        
        $this->_values = array();
        if ($this->_keys) {
        	$this->_values = array_fill(0, count($this->_keys), null);
        }
        
    }

	public function bindParam($parameter, &$variable, $type = null, $length = null, $options = null)
	{
		Zend_Db_Statement::bindParam($parameter, $variable, $length, $options);
		if (!is_int($parameter)) {
			throw new Zend_Db_Statement_Db2_Exception('bind by name is not supported in DB2 Adapter');
		}
		
		if($type === null) {
			$type = DB2_PARAM_IN;
		}
		
		if (isset($options['data-type'])) {
			$datatype = $options['data-type'];
		} else {
			$datatype = DB2_CHAR;
		}
		
		if ($parameter > 0 && $parameter <= count($this->_sqlParam)){
			if (!db2_bind_param($this->_stmt, $parameter, "variable", $type, $datatype)) {
				throw new Zend_Db_Statement_Db2_Exception(db2_stmt_errormsg($this->_stmt), 
				                                          db2_stmt_error($this->_stmt));
			}
		} else {
			throw new Zend_Db_Statement_Db2_Exception("position '$parameter' not valid");
		}
	}
    
   
    /**
     * fetches a row from a result set
     */
    public function fetch($style = null, $cursor = null, $offset = null)
    {
        if (!$this->_stmt){
        	return false;
        }
        
        if ($style === null) {
        	$style = $this->_fetchMode;
        }
        
        switch ($style) {
        	case Zend_Db::FETCH_NUM :
        		$fetch_function = "db2_fetch_array";
        		break;
        	case Zend_Db::FETCH_ASSOC :
        		$fetch_function = "db2_fetch_assoc";
        		break;
        	case Zend_Db::FETCH_BOTH :
        		$fetch_function = "db2_fetch_both";
        		break;
        	case Zend_Db::FETCH_OBJ :
        		$fetch_function = "db2_fetch_object";
        		break;
        	default:
        		throw new Zend_Db_Statement_Db2_Exception('invalid fetch mode specified');
        		break;
        }
     
        $row = $fetch_function($this->_stmt);
        return $row;
    }
    
    /**
     * Prepare statement handle
     */
    public function _prepSql($sql)
    {
    	Zend_Db_Statement::_prepSql($sql);
    	$connection = $this->_connection->getConnection();
    	
    	$this->_stmt = db2_prepare($connection, $sql);
    	
    	if (!$this->_stmt) {
    		throw new Zend_Db_Statement_Db2_Exception(db2_stmt_errormsg($this->_stmt), db2_stmt_error($this->_stmt));	
    	}
    }
    
    public function fetchObject($class = 'stdClass', $config = null)
    {
    	$obj = fetch(Zend_Db::FETCH_OBJ);
    	
    	return $obj;
    }
    
     /**
     * fetches an array containing all of the rows from a result set
     */
    public function fetchAll($style = null, $col = null)
    {
        $data = array();
        if ($col === null) {
            while ($row = $this->fetch($style)) {
                $data[] = $row;
            }
        } else {
            while ($val = $this->fetchColumn($col)) {
                $data = $val;
            }
        }
        return $data;
    }
}
