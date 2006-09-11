<?php

class Kea_DB_Adapter
{
	protected static $_db;
	protected static $_instance;
	
	protected $_sqlSplit;
	protected $_sqlParam;
	protected $_bindParam;

	private function __construct() {}
	private function __clone() {}
	
	public static function instance()
	{
		if( !self::$_instance instanceof self ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

    protected function _connect()
    {
		if ( !self::$_db instanceof mysqli ) {
			self::$_db = Kea_DB_Connection::instance()->connect();
		}
		return self::$_db;
    }

	public function error()
	{
		return self::$_db->error;
	}

    public function query( $sql, $bind = array() )
    {
        // connect to the database if needed
        $this->_connect();

        // is the $sql a Zend_Db_Select object?
        if ($sql instanceof Kea_Db_Select) {
            $sql = $sql->__toString();
        }

		$this->prepSQL( $sql );

		foreach( (array) $bind as $key => $val ) {
			$this->bindParam( $key, $val );
		}

		$sql = $this->_joinSQL();
		
		if( KEA_LOG_SQL === true ) {
			Kea_Logger::logSQL( $sql );
		}
		return self::$_db->query( $sql );
    }

	public function prepSQL( $sql )
	{
		// split into text and params
        $this->_sqlSplit = preg_split(
			//was "/(\?|\:[a-z]+)/",
            "/(\?|\:[a-zA-Z_]+)/",
            $sql,
            -1,
            PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY
        );

        // map params
        $this->_sqlParam = array();
        foreach ($this->_sqlSplit as $key => $val) {
            if ($val[0] == ':' || $val[0] == '?') {
                $this->_sqlParam[] = $val; // key *2 +1 is the parsed position
            }
        }

        // set up for binding
        $this->_bindParam = array();
	}

	//array( 0=>'foo', 1=>'bar' )
	public function bindParam( $parameter, $variable )
    {
        if ( is_integer( $parameter ) ) {
            if ($parameter > 0 && $parameter <= count($this->_sqlParam)) {
                $this->_bindParam[$parameter] = $variable;
            } elseif( $parameter == 0 ) {
				$this->_bindParam[0] = $variable;
			} else {
                throw new Kea_DB_Adapter_Exception("position '$parameter' not valid");
            }
        } else {
            // bind by name. make sure it has a colon on it.
            if ($parameter[0] != ':') {
                $parameter = ":$parameter";
            }
            // look up its position in the params.
            $key = array_search($parameter, $this->_sqlParam);
			
            if (is_integer($key)) {
                $this->_bindParam[$key] =& $variable;
            } else {
                throw new Kea_DB_Adapter_Exception("parameter name '$parameter' not valid");
            }
        }
    }

    protected function _joinSql()
    {
        $sql = $this->_sqlSplit;
        foreach ($this->_bindParam as $key => $val) {
            $pos = $key *2 +1; // always an odd position, right?
			if( $val == 'NULL' ) {
				$sql[$pos] = 'NULL';
			}else{
            	$sql[$pos] = $this->quote($val);
			}
        }
        return implode('', $sql);
    }



    public function insert($table, array $bind)
    {
        // col names come from the array keys
        $cols = array_keys($bind);

        // build the statement
        $sql = "INSERT INTO $table "
             . '(' . implode(', ', $cols) . ') '
             . 'VALUES (:' . implode(', :', $cols) . ')';
        return $this->query($sql, $bind);
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
        foreach ($bind as $col => $val) {
            $set[] = "$col = :$col";
        }

        // build the statement
        $sql = "UPDATE $table "
             . 'SET ' . implode(', ', $set)
             . (($where) ? " WHERE $where" : '');

        // execute the statement and return the number of affected rows
        $this->query($sql, $bind);
        return self::$_db->affected_rows;
    }

    /**
     * Deletes table rows based on a WHERE clause.
     *
     * @param string $table The table to udpate.
     * @param string $where DELETE WHERE clause.
     * @return int The number of affected rows.
     */
    public function delete($table, $where)
    {
        // build the statement
        $sql = "DELETE FROM $table"
             . (($where) ? " WHERE $where" : '');

        // execute the statement and return the number of affected rows
        $this->query($sql);
        return self::$_db->affected_rows;
    }

    /**
     * Adds an adapter-specific LIMIT clause to the SELECT statement.
     *
     * @return string
     */
     public function limit($sql, $count, $offset)
     {
        if ($count > 0) {
            $offset = ($offset > 0) ? $offset : 0;
            $sql .= "LIMIT $offset, $count";
        }
        return $sql;
    }


    /**
     * Creates and returns a new Zend_Db_Select object for this adapter.
     *
     * @return Zend_Db_Select
     */
    public function select( $mapper = null, $use_plugins = true )
    {
		return new Kea_DB_Select( $this, $mapper, $use_plugins );
    }

    public function insertId()
    {
        $this->_connect();
        return self::$_db->insert_id;
    }

    public function listTables()
    {
        return $this->fetchCol('SHOW TABLES');
    }

    public function describeTable( $table )
    {
        $sql = "DESCRIBE $table";
        $result = $this->fetchAssoc($sql);
        foreach ($result as $key => $val) {
            $descr[$val['Field']] = array(
                'name'    => $val['Field'],
                'type'    => $val['Type'],
                'notnull' => (bool) ($val['Null'] === ''), // not null is empty, null is yes
                'default' => $val['Default'],
                'primary' => (strtolower($val['Key']) == 'pri'),
				'extra'   => (strtolower($val['Extra']))
            );
        }
        return $descr;
    }

    public function beginTransaction()
    {
        $this->_connect();
		self::$_db->autocommit(false);
        return true;
    }

    public function commit()
    {
        $this->_connect();
        self::$_db->autocommit(true);
		if( self::$_db->commit() ) {
        	return true;
		} else {
			throw new Kea_DB_Adapter_Exception( 'Could not commit the current operation.' );
		}
    }

    public function rollback()
    {
        $this->_connect();
        if( self::$_db->rollback() ) {
	    	return true;
		} else {
			throw new Kea_DB_Adapter_Exception( 'Could not preform the current rollback operation.' );
		}
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
        while ($row = $result->fetch_assoc() ) {
            $data[] = $row;
        }
        $result->free_result();
        return $data;
    }


    /**
     * Fetches the first column of all SQL result rows as an array.
     *
     * The first column in each row is used as the array key.
     *
     * @param string $sql An SQL SELECT statement.
     * @param array $bind Data to bind into SELECT placeholders.
     * @return array
     */
    public function fetchCol($sql, $bind = null)
    {
        $result = $this->query($sql, $bind);
        $data = array();
        while ($row = $result->fetch_array(MYSQLI_NUM)) {
            $data[] = $row[0];
        }
        $result->free_result();
        return $data;
    }


    /**
     * Fetches the first column of the first row of the SQL result.
     *
     * @param string $sql An SQL SELECT statement.
     * @param array $bind Data to bind into SELECT placeholders.
     * @return string
     */
    public function fetchOne($sql, $bind = null)
    {
        $result = $this->query($sql, $bind);
	    $row = $result->fetch_array(MYSQLI_NUM);
        $result->free_result();
        return $row[0];	
    }


    /**
     * Fetches the first row of the SQL result.
     *
     * @param string $sql An SQL SELECT statement.
     * @param array $bind Data to bind into SELECT placeholders.
     * @return array
     */
    public function fetchRow($sql, $bind = null)
    {
        $result = $this->query($sql, $bind);
        $row = $result->fetch_row();
        $result->free_result();
        return $row;
    }


    /**
     * Safely quotes a value for an SQL statement.
     *
     * If an array is passed as the value, the array values are quoted
     * and then returned as a comma-separated string.
     *
     * @param mixed $value The value to quote.
     * @return mixed An SQL-safe quoted value (or string of separated values).
     */
    public function quote(&$value)
    {
        $this->_connect();
        if (is_array($value)) {
            foreach ($value as &$val) {
                $val = $this->quote($val);
            }
            return implode(', ', $value);
        } else {
            return '"' . self::$_db->real_escape_string($value) . '"';
        }
    }


    /**
     * Quotes a value and places into a piece of text at a placeholder.
     *
     * The placeholder is a question-mark; all placeholders will be replaced
     * with the quoted value.   For example:
     *
     * <code>
     * $text = "WHERE date < ?";
     * $date = "2005-01-02";
     * $safe = $sql->quoteInto($text, $date);
     * // $safe = "WHERE date < '2005-01-02'"
     * </code>
     *
     * @param string $txt The text with a placeholder.
     * @param mixed $val The value to quote.
     * @return mixed An SQL-safe quoted value placed into the orignal text.
     */
    public function quoteInto($text, $value)
    {
        return str_replace('?', $this->quote($value), $text);
    }


    /**
     * Quotes an identifier.
     *
     * @param string $ident The identifier.
     * @return string The quoted identifier.
     */
    public function quoteIdentifier($ident)
    {
        $ident = str_replace('`', '\`', $ident);
        return "`$ident`";
    }
	
	/*
	public function fetchPreparedArray( mysqli_stmt $stmt ) {
		$data = $stmt->result_metadata();
		//start the count from 1. First value has to be a reference to the stmt. because bind_param requires the link to $stmt as the first param.
		$count = 1;
		$fieldnames[0] = &$stmt;
		$fields =  $data->fetch_fields();
		foreach( $fields as $field ) {
		    $fieldnames[$count] = &$array[$field->name]; //load the fieldnames into an array.
		    $count++;
		}
		call_user_func_array('mysqli_stmt_bind_result', $fieldnames);
		$stmt->fetch();
		return $array;
	}
	*/

}


?>