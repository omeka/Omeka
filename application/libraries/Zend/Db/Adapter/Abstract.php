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
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */ 


/** Zend_Db_Adapter_Exception */
require_once 'Zend/Db/Adapter/Exception.php';

/** Zend_Db_Profiler */
require_once 'Zend/Db/Profiler.php';

/** Zend_Db_Select */
require_once 'Zend/Db/Select.php';


/**
 * Class for connecting to SQL databases and performing common operations.
 *
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Db_Adapter_Abstract
{

    /**
     * User-provided configuration
     *
     * @var array
     */
    protected $_config = array();

    /**
     * Fetch mode
     *
     * @var integer
     */
    protected $_fetchMode = Zend_Db::FETCH_ASSOC;

    /**
     * Query profiler
     *
     * @var Zend_Db_Profiler
     */
    protected $_profiler;

    /**
     * Database connection
     *
     * @var object|resource|null
     */
    protected $_connection = null;


    /**
     * Constructor.
     *
     * $config is an array of key/value pairs containing configuration
     * options.  These options are common to most adapters:
     *
     * dbname   => (string) The name of the database to user (required)
     * username => (string) Connect to the database as this username (optional).
     * password => (string) Password associated with the username (optional).
     * host     => (string) What host to connect to (default 127.0.0.1).
     *
     * @param array $config An array of configuration keys.
     */
    public function __construct($config)
    {
        // make sure the config array exists
        if (! is_array($config)) {
            throw new Zend_Db_Adapter_Exception('must pass a config array');
        }

        // we need at least a dbname
        if (! array_key_exists('dbname', $config)) {
            throw new Zend_Db_Adapter_Exception('config array must have a key for dbname');
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
     * Returns the underlying database connection object or resource.  If not
     * presently connected, this may return null.
     *
     * @return object|resource|null
     */
    public function getConnection()
    {
        return $this->_connection;
    }


    /**
     * Returns the profiler for this adapter.
     *
     * @return Zend_Db_Profiler
     */
    public function getProfiler()
    {
        return $this->_profiler;
    }


    /**
     * Prepares and executes an SQL statement with bound data.
     *
     * @param string $sql The SQL statement with placeholders.
     * @param array $bind An array of data to bind to the placeholders.
     * @return Zend_Db_Statement (may also be PDOStatement in the case of PDO)
     */
    public function query($sql, $bind = array())
    {
        // connect to the database if needed
        $this->_connect();

        // is the $sql a Zend_Db_Select object?
        if ($sql instanceof Zend_Db_Select) {
            $sql = $sql->__toString();
        }

        // prepare and execute the statement with profiling
        $stmt = $this->prepare($sql);
        $q = $this->_profiler->queryStart($sql);
        $stmt->execute((array) $bind);
        $this->_profiler->queryEnd($q);

        // return the results embedded in the prepared statement object
        $stmt->setFetchMode($this->_fetchMode);
        return $stmt;
    }


    /**
     * Leave autocommit mode and begin a transaction.
     *
     * @return void
     */
    public function beginTransaction()
    {
        $this->_connect();
        $q = $this->_profiler->queryStart('begin', Zend_Db_Profiler::TRANSACTION);
        $this->_beginTransaction();
        $this->_profiler->queryEnd($q);
        return true;
    }


    /**
     * Commit a transaction and return to autocommit mode.
     *
     */
    public function commit()
    {
        $this->_connect();
        $q = $this->_profiler->queryStart('commit', Zend_Db_Profiler::TRANSACTION);
        $this->_commit();
        $this->_profiler->queryEnd($q);
        return true;
    }


    /**
     * Roll back a transaction and return to autocommit mode.
     *
     * @return void
     */
    public function rollBack()
    {
        $this->_connect();
        $q = $this->_profiler->queryStart('rollback', Zend_Db_Profiler::TRANSACTION);
        $this->_rollBack();
        $this->_profiler->queryEnd($q);
        return true;
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

        // build the statement
        $sql = "INSERT INTO $table "
             . '(' . implode(', ', $cols) . ') '
             . 'VALUES (:' . implode(', :', $cols) . ')';

        // execute the statement and return the number of affected rows
        $result = $this->query($sql, $bind);
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
        foreach ($bind as $col => $val) {
            $set[] = "$col = :$col";
        }

        // build the statement
        $sql = "UPDATE $table "
             . 'SET ' . implode(', ', $set)
             . (($where) ? " WHERE $where" : '');

        // execute the statement and return the number of affected rows
        $result = $this->query($sql, $bind);
        return $result->rowCount();
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
        $result = $this->query($sql);
        return $result->rowCount();
    }


    /**
     * Creates and returns a new Zend_Db_Select object for this adapter.
     *
     * @return Zend_Db_Select
     */
    public function select()
    {
        return new Zend_Db_Select($this);
    }


    /**
     * Get the fetch mode.
     *
     * @return int
     */
    public function getFetchMode()
    {
        return $this->_fetchMode;
    }


    /**
     * Fetches all SQL result rows as a sequential array.
     *
     * @param string $sql An SQL SELECT statement.
     * @param array $bind Data to bind into SELECT placeholders.
     * @return array
     */
    public function fetchAll($sql, $bind = null)
    {
        $result = $this->query($sql, $bind);
        return $result->fetchAll($this->_fetchMode);
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
            $tmp = array_values(array_slice($row, 0, 1));
            $data[$tmp[0]] = $row;
        }
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
        return $result->fetchAll(Zend_Db::FETCH_COLUMN, 0);
    }


    /**
     * Fetches all SQL result rows as an array of key-value pairs.
     *
     * The first column is the key, the second column is the
     * value.
     *
     * @param string $sql An SQL SELECT statement.
     * @param array $bind Data to bind into SELECT placeholders.
     * @return string
     */
    public function fetchPairs($sql, $bind = null)
    {
        $result = $this->query($sql, $bind);
        $data = array();
        while ($row = $result->fetch(Zend_Db::FETCH_NUM)) {
            $data[$row[0]] = $row[1];
        }
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
        return $result->fetchColumn(0);
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
        return $result->fetch($this->_fetchMode);
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
    public function quote($value)
    {
        $this->_connect();
        if (is_array($value)) {
            foreach ($value as &$val) {
                $val = $this->quote($val);
            }
            return implode(', ', $value);
        } else {
            return $this->_quote($value);
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
     * Abstract Methods
     */


    /**
     * Quotes an identifier.
     *
     * @param string $ident The identifier.
     * @return string The quoted identifier.
     */
    abstract public function quoteIdentifier($string);


    /**
     * Returns a list of the tables in the database.
     *
     * @return array
     */
    abstract public function listTables();


    /**
     * Returns the column descriptions for a table.
     *
     * @return array
     */
    abstract public function describeTable($table);


    /**
     * Quote a raw string.
     *
     * @param string $value     Raw string
     * @return string           Quoted string
     */
    abstract protected function _quote($value);


    /**
     * Creates a connection to the database.
     *
     * @return void
     */
    abstract protected function _connect();


    /**
     * Prepare a statement and return a PDOStatement-like object.
     *
     * @param  string  $sql  SQL query
     * @return Zend_Db_Statment|PDOStatement
     */
    abstract public function prepare($sql);


    /**
     * Gets the last inserted ID.
     *
     * @param  string $tableName   name of table (or sequence) associated with sequence
     * @param  string $primaryKey  primary key in $tableName
     * @return integer
     */
    abstract public function lastInsertId($tableName = null, $primaryKey = null);


    /**
     * Begin a transaction.
     */
    abstract protected function _beginTransaction();


    /**
     * Commit a transaction.
     */
    abstract protected function _commit();


    /**
     * Roll-back a transaction.
     */
    abstract protected function _rollBack();


    /**
     * Set the fetch mode.
     *
     * @param integer $mode
     */
    abstract public function setFetchMode($mode);



    /**
     * Adds an adapter-specific LIMIT clause to the SELECT statement.
     *
     * @return string
     */
    abstract public function limit($sql, $count, $offset);
}
