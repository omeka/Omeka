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
 * @subpackage Select
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */ 


/** Zend_Db_Adapter_Abstract */
require_once 'Zend/Db/Adapter/Abstract.php';


/**
 * Class for SQL SELECT generation and results.
 *
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Select
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Db_Select {

    /**
     * Zend_Db_Adapter_Abstract object.
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_adapter;

    /**
     * The component parts of a SELECT statement.
     *
     * @var array
     */
    protected $_parts = array(
        'distinct'    => false,
        'forUpdate'   => false,
        'cols'        => array(),
        'from'        => array(),
        'join'        => array(),
        'where'       => array(),
        'group'       => array(),
        'having'      => array(),
        'order'       => array(),
        'limitCount'  => null,
        'limitOffset' => null,
    );

    /**
     * Tracks which columns are being select from each table and join.
     *
     * @var array
     */
    protected $_tableCols = array();


    /**
     * Class constructor
     *
     * @param Zend_Db_Adapter_Abstract $adapter
     */
    public function __construct(Zend_Db_Adapter_Abstract $adapter)
    {
        $this->_adapter = $adapter;
    }


    /**
     * Converts this object to an SQL SELECT string.
     *
     * @todo use $this->_adapter->quoteColumns() for non-PDO adapters
     * @todo use $this->_adapter->quoteTableNames() for non-PDO adapters
     * @todo use prepared queries for PDO adapters instead of constructing all the SQL ourselves
     *           like in Adapter/Abstract.php.html:query()
     * @return string This object as a SELECT string.
     */
    public function __toString()
    {
        // initial SELECT [DISTINCT] [FOR UPDATE]
        $sql = "SELECT";
        if ($this->_parts['distinct']) {
            $sql .= " DISTINCT";
        }
        if ($this->_parts['forUpdate']) {
            $sql .= " FOR UPDATE";
        }
        $sql .= "\n\t";

        // add columns
        if ($this->_parts['cols']) {
            $sql .= implode(",\n\t", $this->_parts['cols']) . "\n";
        }

        // from these tables
        if ($this->_parts['from']) {
            $sql .= "FROM ";
            $sql .= implode(", ", array_keys($this->_parts['from'])) . "\n";
        }

        // joined to these tables
        if ($this->_parts['join']) {
            $list = array();
            foreach ($this->_parts['join'] as $join) {
                $tmp = '';
                // add the type (LEFT, INNER, etc)
                if (! empty($join['type'])) {
                    $tmp .= strtoupper($join['type']) . ' ';
                }
                // add the table name and condition
                $tmp .= 'JOIN ' . $join['name'];
                $tmp .= ' ON ' . $join['cond'];
                // add to the list
                $list[] = $tmp;
            }
            // add the list of all joins
            $sql .= implode("\n", $list) . "\n";
        }

        // with these where conditions
        if ($this->_parts['where']) {
            $sql .= "WHERE\n\t";
            $sql .= implode("\n\t", $this->_parts['where']) . "\n";
        }

        // grouped by these columns
        if ($this->_parts['group']) {
            $sql .= "GROUP BY\n\t";
            $sql .= implode(",\n\t", $this->_parts['group']) . "\n";
        }

        // having these conditions
        if ($this->_parts['having']) {
            $sql .= "HAVING\n\t";
            $sql .= implode("\n\t", $this->_parts['having']) . "\n";
        }

        // ordered by these columns
        if ($this->_parts['order']) {
            $sql .= "ORDER BY\n\t";
            $sql .= implode(",\n\t", $this->_parts['order']) . "\n";
        }

        // determine count
        $count = ! empty($this->_parts['limitCount'])
            ? (int) $this->_parts['limitCount']
            : 0;

        // determine offset
        $offset = ! empty($this->_parts['limitOffset'])
            ? (int) $this->_parts['limitOffset']
            : 0;

        // add limits, and done
        return trim($this->_adapter->limit($sql, $count, $offset));
    }


    /**
     * Makes the query SELECT DISTINCT.
     *
     * @param bool $flag Whether or not the SELECT is DISTINCT (default true).
     * @return Zend_Db_Select This Zend_Db_Select object.
     */
    public function distinct($flag = true)
    {
        $this->_parts['distinct'] = (bool) $flag;
        return $this;
    }


    /**
     * Makes the query SELECT FOR UPDATE.
     *
     * @param bool $flag Whether or not the SELECT is DISTINCT (default true).
     * @return Zend_Db_Select This Zend_Db_Select object.
     */
    public function forUpdate($flag = true)
    {
        $this->_parts['forUpdate'] = (bool) $flag;
        return $this;
    }


    /**
     * Adds a FROM table and optional columns to the query.
     *
     * @param string $name The table name.
     * @param array|string $cols The columns to select from this table.
     * @return Zend_Db_Select This Zend_Db_Select object.
     */
    public function from($name, $cols = '*')
    {
        // add the table to the 'from' list
        $this->_parts['from'][$name] = null;

        // add to the columns from this table
        $this->_tableCols($name, $cols);
        return $this;
    }

    /**
     * Populate the {@link $_parts} 'join' key
     *
     * Does the dirty work of populating the join key.
     *
     * @access protected
     * @param null|string $type Type of join; inner, left, and null are
     * currently supported
     * @param string $name Table name
     * @param string $cond Join on this condition
     * @param array|string $cols The columns to select from the joined table
     * @return Zend_Db_Select This Zend_Db_Select object
     */
    protected function _join($type, $name, $cond, $cols) 
    {
        if (!in_array($type, array('left', 'inner'))) {
            $type = null;
        }

        $this->_parts['join'][] = array(
            'type' => $type,
            'name' => $name,
            'cond' => $cond
        );

        // add to the columns from this joined table
        $this->_tableCols($name, $cols);
        return $this;
    }

    /**
     * Adds a JOIN table and columns to the query.
     *
     * @param string $name The table name.
     * @param string $cond Join on this condition.
     * @param array|string $cols The columns to select from the joined table.
     * @return Zend_Db_Select This Zend_Db_Select object.
     */
    public function join($name, $cond, $cols = null)
    {
        return $this->_join(null, $name, $cond, $cols);
    }


    /**
     * Add a LEFT JOIN table and colums to the query
     *
     * @param string $name The table name.
     * @param string $cond Join on this condition.
     * @param array|string $cols The columns to select from the joined table.
     * @return Zend_Db_Select This Zend_Db_Select object.
     */
    public function joinLeft($name, $cond, $cols = null) 
    {
        return $this->_join('left', $name, $cond, $cols);
    }

    /**
     * Add an INNER JOIN table and colums to the query
     *
     * @param string $name The table name.
     * @param string $cond Join on this condition.
     * @param array|string $cols The columns to select from the joined table.
     * @return Zend_Db_Select This Zend_Db_Select object.
     */
    public function joinInner($name, $cond, $cols = null) 
    {
        return $this->_join('inner', $name, $cond, $cols);
    }


    /**
     * Adds a WHERE condition to the query by AND.
     *
     * If a value is passed as the second param, it will be quoted
     * and replaced into the condition wherever a question-mark
     * appears. Array values are quoted and comma-separated.
     *
     * <code>
     * // simplest but non-secure
     * $select->where("id = $id");
     *
     * // secure (ID is quoted but matched anyway)
     * $select->where('id = ?', $id);
     *
     * // alternatively, with named binding
     * $select->where('id = :id');
     * </code>
     * 
     * Note that it is more correct to use named bindings in your
     * queries for values other than strings. When you use named
     * bindings, don't forget to pass the values when actually
     * making a query:
     * 
     * <code>
     * $db->fetchAll($select, array('id' => 5));
     * </code>
     *
     * @param string $cond The WHERE condition.
     * @param string $val A single value to quote into the condition.
     * @return void
     */
    public function where($cond)
    {
        if (func_num_args() > 1) {
            $val = func_get_arg(1);
            $cond = $this->_adapter->quoteInto($cond, $val);
        }

        if ($this->_parts['where']) {
            $this->_parts['where'][] = "AND $cond";
        } else {
            $this->_parts['where'][] = $cond;
        }

        return $this;
    }


    /**
     * Adds a WHERE condition to the query by OR.
     *
     * Otherwise identical to where().
     *
     * @param string $cond The WHERE condition.
     * @param string $val A value to quote into the condition.
     * @return void
     *
     * @see where()
     */
    public function orWhere($cond)
    {
        if (func_num_args() > 1) {
            $val = func_get_arg(1);
            $cond = $this->_adapter->quoteInto($cond, $val);
        }

        if ($this->_parts['where']) {
            $this->_parts['where'][] = "OR $cond";
        } else {
            $this->_parts['where'][] = $cond;
        }

        return $this;
    }


    /**
     * Adds grouping to the query.
     *
     * @param string|array $spec The column(s) to group by.
     * @return void
     */
    public function group($spec)
    {
        if (is_string($spec)) {
            $spec = explode(',', $spec);
        } else {
            settype($spec, 'array');
        }

        foreach ($spec as $val) {
            $this->_parts['group'][] = trim($val);
        }

        return $this;
    }


    /**
     * Adds a HAVING condition to the query by AND.
     *
     * If a value is passed as the second param, it will be quoted
     * and replaced into the condition wherever a question-mark
     * appears. See {@link where()} for an example
     *
     * @param string $cond The HAVING condition.
     * @param string $val A single value to quote into the condition.
     * @return void
     */
    public function having($cond)
    {
        if (func_num_args() > 1) {
            $val = func_get_arg(1);
            $cond = $this->_adapter->quoteInto($cond, $val);
        }

        if ($this->_parts['having']) {
            $this->_parts['having'][] = "AND $cond";
        } else {
            $this->_parts['having'][] = $cond;
        }

        return $this;
    }


    /**
     * Adds a HAVING condition to the query by OR.
     *
     * Otherwise identical to orHaving().
     *
     * @param string $cond The HAVING condition.
     * @param string $val A single value to quote into the condition.
     * @return void
     *
     * @see having()
     */
    public function orHaving($cond)
    {
        if (func_num_args() > 1) {
            $val = func_get_arg(1);
            $cond = $this->_adapter->quoteInto($cond, $val);
        }

        if ($this->_parts['having']) {
            $this->_parts['having'][] = "OR $cond";
        } else {
            $this->_parts['having'][] = $cond;
        }

        return $this;
    }


    /**
     * Adds a row order to the query.
     *
     * @param string|array $spec The column(s) and direction to order by.
     * @return void
     */
    public function order($spec)
    {
        if (is_string($spec)) {
            $spec = explode(',', $spec);
        } else {
            settype($spec, 'array');
        }

        // force 'ASC' or 'DESC' on each order spec, default is ASC.
        foreach ($spec as $key => $val) {
            $asc  = (strtoupper(substr($val, -4)) == ' ASC');
            $desc = (strtoupper(substr($val, -5)) == ' DESC');
            if (! $asc && ! $desc) {
                $val .= ' ASC';
            }
            $this->_parts['order'][] = trim($val);
        }

        return $this;
    }


    /**
     * Sets a limit count and offset to the query.
     *
     * @param int $count The number of rows to return.
     * @param int $offset Start returning after this many rows.
     * @return void
     */
    public function limit($count = null, $offset = null)
    {
        $this->_parts['limitCount']  = (int) $count;
        $this->_parts['limitOffset'] = (int) $offset;
        return $this;
    }


    /**
     * Sets the limit and count by page number.
     *
     * @param int $page Limit results to this page number.
     * @param int $rowCount Use this many rows per page.
     * @return void
     */
    public function limitPage($page, $rowCount)
    {
        $page     = ($page > 0)     ? $page     : 1;
        $rowCount = ($rowCount > 0) ? $rowCount : 1;
        $this->_parts['limitCount']  = (int) $rowCount;
        $this->_parts['limitOffset'] = (int) $rowCount * ($page - 1);
        return $this;
    }


    /**
     * Adds to the internal table-to-column mapping array.
     *
     * @param string $tbl The table/join the columns come from.
     * @param string|array $cols The list of columns; preferably as
     * an array, but possibly as a comma-separated string.
     * @return void
     */
    protected function _tableCols($tbl, $cols)
    {
        if (is_string($cols)) {
            $cols = explode(',', $cols);
        } else {
            settype($cols, 'array');
        }

        foreach ($cols as $col) {
            $this->_parts['cols'][] = trim($col);
        }
    }

}
