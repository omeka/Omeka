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
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 *
 *
 */

/** Zend_Db_Adapter_Abstract */
require_once 'Zend/Db/Adapter/Abstract.php';

/** Zend_Db_Adapter_Db2_Exception */
require_once 'Zend/Db/Adapter/Db2/Exception.php';

/** Zend_Db_Statement_Db2 */
require_once 'Zend/Db/Statement/Db2.php';


/**
 * @package    Zend_Db
 * @copyright  Copyright (c) 2005-2007 Zend Technologies Inc. (http://www.zend.com)
 * @license    Zend Framework License version 1.0
 * @author 	   Joscha Feth <jffeth@de.ibm.com>
 * @author     Salvador Ledezma <ledezma@us.ibm.com>
 */

class Zend_Db_Adapter_Db2 extends Zend_Db_Adapter_Abstract
{
	/**
     * User-provided configuration.
     *
     * Basic keys are:
     *
     * username   => (string)  Connect to the database as this username.
     * password   => (string)  Password associated with the username.
     * host       => (string)  What host to connect to (default 127.0.0.1)
     * dbname     => (string)  The name of the database to user
     * protocol   => (string)  Protocol to use, defaults to "TCPIP"
     * port       => (integer) Port number to use for TCP/IP if protocol is "TCPIP"
     * persistent => (boolean) Set TRUE to use a persistent connection (db2_pconnect)
     *     
     * @var array
     */
    protected $_config = array(
        'dbname' 		=> null,
        'username' 		=> null,
        'password' 		=> null,
        'host' 			=> 'localhost',
        'port' 			=> '50000',
        'protocol' 		=> 'TCPIP',
        'persistent'	=> false
    );

    /**
     * Execution mode
     *
     * @var int execution flag (DB2_AUTOCOMMIT_ON or DB2_AUTOCOMMIT_OFF)
     * @access protected
     */
    protected $_execute_mode = DB2_AUTOCOMMIT_ON;

    /**
     * Table name of the last accessed table for an insert operation
     * This is a DB2-Adapter-specific member variable with the utmost
     * probability you might not find it in other adapters...
     * 
     * @var string
     * @access protected
     */
    protected $_lastInsertTable = null;
    
     /**
     * Constructor.
     *
     * $config is an array of key/value pairs containing configuration
     * options.  These options are common to most adapters:
     *
     * dbname   	=> (string) The name of the database to user
     * username 	=> (string) Connect to the database as this username.
     * password 	=> (string) Password associated with the username.
     * host     	=> (string) What host to connect to, defaults to localhost
     * port     	=> (string) The port of the database, defaults to 50000
     * persistent 	=> (boolean) Whether to use a persistent connection or not, 
     * 				   defaults to false 
     * protocol 	=> (string) The network protocol, defaults to TCPIP
     * options  	=> (array)  Other database options such as, 
     * 				   autocommit, case, and cursor options
     *
     * @param array $config An array of configuration keys.
     */
	public function __construct($config)
    {
        // make sure the config array exists
    	if (! is_array($config)) {
            throw new Zend_Db_Adapter_Exception('must pass a config array');
        }

        // we need at least a dbname, a user and a password
        if (! array_key_exists('password', $config) || 
        	! array_key_exists('username', $config) ||
            ! array_key_exists('dbname', $config)) {
            throw new Zend_Db_Adapter_Exception('config array must have at least a username, a password, and a database name');
        }

        // keep the config
        $this->_config = array_merge($this->_config, (array) $config);

        // create a profiler object
        $enabled = false;
        if (array_key_exists('profiler', $this->_config)) {
            $enabled = (bool) $this->_config['profiler'];
            unset($this->_config['profiler']);
        }

        $this->_profiler = new Zend_Db_Profiler($enabled);
    }

    /**
    * Creates a connection resource.
 	*
    * @return void
    */
    protected function _connect()
    {
   		if (is_resource($this->_connection)) {
			// connection already exists
            return;
        }

        if($this->_config['persistent']) {
			// use persistent connection
        	$conn_func_name = 'db2_pconnect';
        } else {
			// use "normal" connection
        	$conn_func_name = 'db2_connect';
        }
        
        if (!isset($this->_config['options'])) {
			// config options were not set, so set it to an empty array
        	$this->_config['options'] = array();
        }
        
        if (!isset($this->_config['options']['autocommit'])) {
			// set execution mode
        	$this->_config['options']['autocommit'] = &$this->_execute_mode;
        }

		if ($this->_config['host'] !== 'localhost') {
			// if the host isn't localhost, use extended connection params
			$dbname = 'DRIVER={IBM DB2 ODBC DRIVER}' .
					  ';DATABASE='	. $this->_config['dbname'] .
					  ';HOSTNAME=' 	. $this->_config['host'] .
					  ';PORT=' 		. $this->_config['port'] . 
					  ';PROTOCOL= ' . $this->_config['protocol'] . 
					  ';UID=' 		. $this->_config['username'] .
					  ';PWD=' 		. $this->_config['password'] .';';
			$this->_connection = $conn_func_name($dbname,
											 	 null,
											 	 null,
											 	 $this->_config['options']);
		} else {
			// host is localhost, so use standard connection params
			$this->_connection = $conn_func_name($this->_config['dbname'],
											 	 $this->_config['username'],
											 	 $this->_config['password'],
											 	 $this->_config['options']);
		}

        // check the connection
		if (!$this->_connection) {
            throw new Zend_Db_Adapter_Db2_Exception(db2_conn_errormsg(), db2_conn_error());
        }
    }

    /**
     * Returns an SQL statement for preparation.
     *
     * @param string $sql The SQL statement with placeholders.
     * @return Zend_Db_Statement_Db2
     */
    public function prepare($sql)
    {
        $this->_connect();
        $stmt = new Zend_Db_Statement_Db2($this, $sql);
        $stmt->setFetchMode($this->_fetchMode);
        return $stmt;
    }

	/**
	* Gets the execution mode
	*
	* @return int the execution mode (DB2_AUTOCOMMIT_ON or DB2_AUTOCOMMIT_OFF)
	*/
    public function _getExecuteMode()
    {
    	return $this->_execute_mode;
    }

    public function _setExecuteMode($mode)
    {
    	switch ($mode) {
			case DB2_AUTOCOMMIT_OFF:
			case DB2_AUTOCOMMIT_ON:
    			$this->_execute_mode = $mode;
    			db2_autocommit($this->_connection, $mode);	
    			break;
			default:
				throw new Zend_Db_Adapter_Db2_Exception("execution mode not supported");
				break;
    	}
    }

    /**
     * Quote a raw string
     *
     * @param string $value		Raw string
     * @return string			Quoted string
     */
    protected function _quote($value)
    {
    	$value = str_replace('"', "'", $value);
    	return $value;
    }

    /**
     * Quotes an identifier.
     *
     * @param string $ident The identifier.
     * 
     * @return string The quoted identifier.
     */
    public function quoteIdentifier($string)
    {
        $info = db2_server_info($this->_connection);
        $identQuote = $info->IDENTIFIER_QUOTE_CHAR;
    	return $identQuote . $string . $identQuote;
    }
       
   	/**
     * Returns a list of the tables in the database.
     *
     * @return array
     */
    public function listTables()
    {
    	if (!$this->_connection) {
    		$this->_connect();
    	}
    	// take the most general case and assume no z/OS
    	// since listTables() takes no parameters
    	$stmt = db2_tables($this->_connection);

    	$tables = array();

    	while( $tables[] = db2_fetch_assoc($stmt));

    	return $tables;
    }
  
    /**
     *
     * Returns the column descriptions for a table.
     * @param string schema.tablename or just tablename
     * @return array
     */
    public function describeTable($table)
    {
    	$sql = "select colname,tabschema,typename, length," . "
    	        scale, nulls from syscat.columns where tabname = '";

    	$schema = strtok($table, '.');
    	$name = strtok('.');
    	if ($name !== false) {
    		$sql .= strtoupper($name) . "' and tabschema ='"
    			  . strtoupper($schema) . "'";
    	} else {
    		$sql .= strtoupper($table) . "'";
    	}

    	$ret = array();
    	$result = $this->fetchAssoc($sql);
    	foreach($result as $row) {
    		$ret[$row['COLNAME']] = $row;
    	}
    	
    	return $ret;
    }

	/**
     * Gets the last inserted ID.
     *
     * @param  string $tableName   name of table associated with sequence
     * @param  string $primaryKey  primary key in $tableName (not used in this adapter)
     * @todo   can we skip the select COLNAME query,
     *         if primaryKey is available?
     * @return integer
     */
    public function lastInsertId($tableName = null, $primaryKey = null)
    {
    	// we must know the name of the table
    	if (!$tableName) {
    		$tableName = $this->_lastInsertTable;
    	}
  
    	if (!$tableName) {
    		return -1;
    	}

    	if (!$this->_connection) {
    		$this->_connect();
    	}
    	
    	$sql = "select COLNAME from syscat.colidentattributes "
    	 	 . "where TABNAME='" . strtoupper($tableName) . "'";
    	$result = $this->fetchAssoc($sql);
    	if ($result) {
    		$identCol = $result[0]['COLNAME'];
    	} else {
    		$identCol = 'ID';
    	}
    	
    	$sql = "select max($identCol) as MAX from $tableName";
    	$result = $this->fetchAssoc($sql);
    	if ($result) {
    		return $result[0]['MAX'];
    	} else {
    		return -1;
    	}    		
    }

    /**
     * Begin a transaction.
     */
    protected function _beginTransaction()
    {
    	$this->_setExecuteMode(DB2_AUTOCOMMIT_OFF);
    }

	/**
     * Commit a transaction.
     */
    protected function _commit()
    {
    	if (!db2_commit($this->_connection)) {
    		throw new Zend_Db_Adapter_Db2_Exception(db2_conn_errormsg($this->_connection),
    		                                        db2_conn_error($this->_connection));
    	}

    	$this->_setExecuteMode(DB2_AUTOCOMMIT_ON);
    }

 	/**
     * Roll-back a transaction.
     */
    protected function _rollBack()
    {
    	if (!db2_rollback($this->_connection)) {
    		throw new Zend_Db_Adapter_Db2_Exception(db2_conn_errormsg($this->_connection),
    		                                        db2_conn_error($this->_connection));
    	}
    	$this->_setExecuteMode(DB2_AUTOCOMMIT_ON);
    }

     /**
     * Set the fetch mode.
     *
     * @param integer $mode
     */
    public function setFetchMode($mode)
    {
   		switch ($mode) {
        	case Zend_Db::FETCH_NUM:   // seq array
            case Zend_Db::FETCH_ASSOC: // assoc array
            case Zend_Db::FETCH_BOTH:  // seq+assoc array
            case Zend_Db::FETCH_OBJ:   // object
                $this->_fetchMode = $mode;
                break;
            default:
                throw new Zend_Db_Adapter_Db2_Exception('Invalid fetch mode specified');
                break;
        }
    }

	/**
     * Adds an adapter-specific LIMIT clause to the SELECT statement.
     *
     * @return string
     */
  	public function limit($sql, $count, $offset)
  	{
  		if(!$count) {
  			return $sql;
  		}
  		
  		if ($offset == 0) {
  			return $sql . " FETCH FIRST $count ROWS ONLY";
  		} else {
  			
  			$sqlPieces = split("from", $sql);
  			$select = $sqlPieces[0];
  			$table = $sqlPieces[1];
  			
  			$col = split("select", $select);
  			
  			$sql = "WITH OFFSET AS($select, ROW_NUMBER() " . 
  				   "OVER(ORDER BY " . $col[1] . ") AS RN FROM $table)" . 
  				   $select ."FROM OFFSET WHERE rn between $offset " .
  			       "and " . ($offset + $count - 1);
  			return $sql;
  		}
	}

	 /**
     * Inserts a table row with specified data.
     *
     * @param string $table The table to insert data into.
     * @param array $bind Column-value pairs.
     * @return int The number of affected rows.
     */
    public function insert($table, $bind)
    {
        // col names come from the array keys
        $cols = array_keys($bind);
        
        $sql = '';
        $values = array();
        foreach($bind as $key => $value){
        	if($value !== null) {
        		if($sql){
        			$sql .= ', ';
        		}
        		$sql .= $key;
        		$values[] = $value;
        	}
        }
        
        $sql = "INSERT INTO $table (" . $sql . ") VALUES (";
        
        $markers = '';
        $numParams = count($bind);
        
        for ($i = 0; $i < $numParams; $i++) {
        	$markers .= '?';
        	if ($i != $numParams - 1 ) {
        		$markers .= ',';
        	}
        }
        $sql .= $markers . ')';
           
        // execute the statement and return the number of affected rows
        $result = $this->query($sql, $values);
        
        $this->_lastInsertTable = $table;
        
        return $result->rowCount();
    }
    
     /**
     * Updates table rows with specified data based on a WHERE clause.
     *
     * @param string $table The table to udpate.
     * @param array $bind Column-value pairs.
     * @param string $where UPDATE WHERE clause.
     * @return int The number of affected rows.
     */
    public function update($table, $bind, $where)
    {
        // build "col = :col" pairs for the statement
        $set = array();
        $values = array_values($bind);
        $newValues = array();
        foreach ($bind as $col => $val) {
        	if($val !== null) {
        		$set[] = "$col = ?";
        		$newValues[] = $val;	
        	}
            
        }

        // build the statement
        $sql = "UPDATE $table "
             . 'SET ' . implode(', ', $set)
             . (($where) ? " WHERE $where" : '');
              
        // execute the statement and return the number of affected rows
        $result = $this->query($sql, $newValues);
        return $result->rowCount();
    }

    /**
     * Fetches all SQL result rows as an associative array.
     *
     * The first column is the key, the entire row array is the
     * value.
     *
     * @param string $sql An SQL SELECT statement.
     * @param array $bind Data to bind into SELECT placeholders.
     * @return string
     */
    public function fetchAssoc($sql, $bind = null)
    {
        $result = $this->query($sql, $bind);
        $data = array();
        while ($row = $result->fetch($this->_fetchMode)) {
            $data[] = $row;
        }
        return $data;
    }
}
