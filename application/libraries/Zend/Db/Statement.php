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
 * @subpackage Statement
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */ 

/** Zend_Db_Statement_Exception */
require_once 'Zend/Db/Statement/Exception.php';

/** Zend_Db_Statement_Interface */
require_once 'Zend/Db/Statement/Interface.php';


/**
 * Abstract class to emulate a PDOStatement for native database adapters.
 *
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Statement
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Db_Statement implements Zend_Db_Statement_Interface {

    /**
     * The current fetch mode.
     */
    protected $_fetchMode = Zend_Db::FETCH_ASSOC;

    /**
     * Attributes.
     */
    protected $_attribute = array();

    /**
     * Column result bindings.
     */
    protected $_bindColumn = array();

    /**
     * Query parameter bindings; covers bindParam() and bindValue().
     */
    protected $_bindParam = array();

    /**
     * SQL string split into an array at placeholders.
     */
    protected $_sqlSplit = array();

    /**
     * Parameter placeholders in the SQL string by position in the split array.
     */
    protected $_sqlParam = array();


    /**
     * Constructor.
     */
    public function __construct($connection, $sql)
    {
        $this->_connection = $connection;
        $this->_prepSql($sql);
    }


    /**
     * Splits SQL into text and params, sets up $this->_bindParam for replacements.
     */
    protected function _prepSql($sql)
    {
        // split into text and params
        $this->_sqlSplit = preg_split(
            "/(\?|\:[a-z]+)/",
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


    /**
     * Joins SQL text and bound params into a string.
     */
    protected function _joinSql()
    {
        $sql = $this->_sqlSplit;
        foreach ($this->_bindParam as $key => $val) {
            $pos = $key *2 +1; // always an odd position, right?
            $sql[$pos] = $this->_connection->quote($val);
        }
        return implode('', $sql);
    }


    /**
     * binds a PHP variable to an output column in a result set
     */
    public function bindColumn($column, &$param, $type = null)
    {
        $this->_bindColumn[$column] =& $param;
    }


    /**
     * binds a PHP variable to a parameter in the prepared statement
     */
    public function bindParam($parameter, &$variable, $type = null,
        $length = null, $options = null)
    {
        if (is_integer($parameter)) {
            if ($parameter > 0 && $parameter <= count($this->_sqlParam)) {
                // bind by position, 1-based
                $this->_bindParam[$parameter-1] =& $variable;
            } else {
                throw new Zend_Db_Statement_Exception("position '$parameter' not valid");
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
                throw new Zend_Db_Statement_Exception("parameter name '$parameter' not valid");
            }
        }
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
                $data[] = $val;
            }
        }
        return $data;
    }


    /**
     * returns the data from a single column in a result set
     */
    public function fetchColumn($col = 0)
    {
        $data = array();
        $col = (int) $col;
        while ($row = $this->fetch(Zend_Db::FETCH_NUM)) {
            $data[] = $row[$col];
        }
        return $data;
    }


    /**
     * fetches the next row and returns it as an object
     */
    public function fetchObject($class = 'stdClass', $config = null)
    {
        $obj = new $class();
        $row = $this->fetch(Zend_Db::FETCH_ASSOC);
        foreach ($row as $key => $val) {
            $obj->$key = $val;
        }
        return $obj;
    }


    /**
     * retrieves a Zend_Db_Statement attribute
     */
    public function getAttribute($key)
    {
        if (array_key_exists($key, $this->_attribute)) {
            return $this->_attribute[$key];
        }
    }


    /**
     * sets a Zend_Db_Statement attribute
     */
    public function setAttribute($key, $val)
    {
        $this->_attribute[$key] = $val;
    }


    /**
     * sets the fetch mode for a Zend_Db_Statement
     */
    public function setFetchMode($mode)
    {
        switch ($mode) {
            case Zend_Db::FETCH_NUM:
            case Zend_Db::FETCH_ASSOC:
            case Zend_Db::FETCH_BOTH:
            case Zend_Db::FETCH_OBJ:
                $this->_fetchMode = $mode;
                break;
            default:
                throw new Zend_Db_Statement_Exception('Invalid fetch mode specified');
                break;
        }
    }


    /**
     * retrieves the next rowset (result set)
     * @todo needs implementation or better exception message
     * @todo fix docblock for params & return types
     */
    public function nextRowset()
    {
        throw new Zend_Db_Statement_Exception(__FUNCTION__ . ' not implemented');
    }


    /**
     * returns the number of rows that were affected by the execution of an SQL statement
     * @todo needs implementation or better exception message
     * @todo fix docblock for params & return types
     */
    public function rowCount()
    {
        throw new Zend_Db_Statement_Exception(__FUNCTION__ . ' not implemented');
    }


    /**
     * binds a value to a parameter in the prepared statement
     * @todo needs implementation or better exception message
     * @todo fix docblock for params & return types
     */
    public function bindValue($parameter, $value, $type = null)
    {
        return $this->bindParam($parameter, $value);
    }


    /**
     * closes the cursor, allowing the statement to be executed again
     * @todo needs implementation or better exception message
     * @todo fix docblock for params & return types
     */
    public function closeCursor()
    {
        throw new Zend_Db_Statement_Exception(__FUNCTION__ . ' not implemented');
    }


    /**
     * returns the number of columns in the result set
     * @todo needs implementation or better exception message
     * @todo fix docblock for params & return types
     */
    public function columnCount()
    {
        throw new Zend_Db_Statement_Exception(__FUNCTION__ . ' not implemented');
    }


    /**
     * retrieves an error code, if any, from the statement
     * @todo needs implementation or better exception message
     * @todo fix docblock for params & return types
     */
    public function errorCode()
    {
        throw new Zend_Db_Statement_Exception(__FUNCTION__ . ' not implemented');
    }


    /**
     * retrieves an array of error information, if any, from the statement
     * @todo needs implementation or better exception message
     * @todo fix docblock for params & return types
     */
    public function errorInfo()
    {
        throw new Zend_Db_Statement_Exception(__FUNCTION__ . ' not implemented');
    }


    /**
     * executes a prepared statement
     * @todo needs implementation or better exception message
     * @todo fix docblock for params & return types
     */
    public function execute($params = null)
    {
        throw new Zend_Db_Statement_Exception(__FUNCTION__ . ' not implemented');
    }


    /**
     * fetches a row from a result set
     * @todo needs implementation or better exception message
     * @todo fix docblock for params & return types
     */
    public function fetch($style = null, $cursor = null, $offset = null)
    {
        throw new Zend_Db_Statement_Exception(__FUNCTION__ . ' not implemented');
    }

}
