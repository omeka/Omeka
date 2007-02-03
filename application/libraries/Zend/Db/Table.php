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


/** Zend_Db_Inflector */
require_once 'Zend/Db/Inflector.php';

/** Zend_Db_Table_Exception */
require_once 'Zend/Db/Table/Exception.php';

/** Zend_Db_Table_Row */
require_once 'Zend/Db/Table/Row.php';

/** Zend_Db_Table_Rowset */
require_once 'Zend/Db/Table/Rowset.php';


/**
 * Class for SQL table interface.
 *
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Table
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Db_Table {

    /**
     * Default Zend_Db_Adapter object.
     *
     * @var Zend_Db_Adapter
     */
    static protected $_defaultDb;

    /**
     * For name inflections.
     *
     * @var Zend_Db_Inflector
     */
    static protected $_inflector;

    /**
     * Zend_Db_Adapter object.
     *
     * @var Zend_Db_Adapter
     */
    protected $_db;

    /**
     * The table name derived from the class name (underscore format).
     *
     * @var array
     */
    protected $_name;

    /**
     * The table column names derived from Zend_Db_Adapter_*::describeTable().
     *
     * The key is the underscore format, and the value is the camelized
     * format.
     *
     * @var array
     */
    protected $_cols;

    /**
     * The primary key column (underscore format).
     *
     * @var string
     */
    protected $_primary = 'id';

    /**
     * Constructor.
     *
     * @param array $config Array of user-specified config options.
     */
    public function __construct($config = null)
    {
        // set a custom Zend_Db_Adapter connection
        if (! empty($config['db'])) {

            // convenience variable
            $db = $config['db'];

            // use an object from the registry?
            if (is_string($db)) {
                $db = Zend::registry($db);
            }

            // make sure it's a Zend_Db_Adapter
            if (! $db instanceof Zend_Db_Adapter_Abstract) {
                throw new Zend_Db_Table_Exception('db object does not implement Zend_Db_Adapter_Abstract');
            }

            // save the connection
            $this->_db = $db;
        }

        // set the inflector
        self::$_inflector = new Zend_Db_Inflector();

        // continue with automated setup
        $this->_setup();
    }

    /**
     * Sets the default Zend_Db_Adapter for all Zend_Db_Table objects.
     *
     * @param Zend_Db_Adapter $db A Zend_Db_Adapter object.
     */
    static public final function setDefaultAdapter($db)
    {
        // make sure it's a Zend_Db_Adapter
        if (! $db instanceof Zend_Db_Adapter_Abstract) {
            throw new Zend_Db_Table_Exception('db object does not extend Zend_Db_Adapter_Abstract');
        }
        Zend_Db_Table::$_defaultDb = $db;
    }

    /**
     * Gets the default Zend_Db_Adapter for all Zend_Db_Table objects.
     *
     */
    protected final function _getDefaultAdapter()
    {
        return Zend_Db_Table::$_defaultDb;
    }

    /**
     * Gets the Zend_Db_Adapter for this particular Zend_Db_Table object.
     *
     */
    public final function getAdapter()
    {
        return $this->_db;
    }
    
    /**
     * Populate static properties for this table module.
     *
     * @return void
     */
    protected function _setup()
    {
        // get the database adapter
        if (! $this->_db) {
            $this->_db = $this->_getDefaultAdapter();
        }
        
        if (! $this->_db instanceof Zend_Db_Adapter_Abstract) {
            throw new Zend_Db_Table_Exception('db object does not implement Zend_Db_Adapter_Abstract');
        }
        
        // get the table name
        if (! $this->_name) {
            $this->_name = self::$_inflector->underscore(get_class($this));
        }

        // get the table columns
        if (! $this->_cols) {
            $tmp = array_keys($this->_db->describeTable($this->_name));
            foreach ($tmp as $native) {
                $this->_cols[$native] = self::$_inflector->camelize($native);
            }
        }

        // primary key
        if (! $this->_primary) {
            // none specified
            $table = $this->_name;
            throw new Zend_Db_Table_Exception("primary key not specified for table '$table'");
        } elseif (! array_key_exists($this->_primary, $this->_cols)) {
            // wrong name
            $key = $this->_primary;
            $table = $this->_name;
            throw new Zend_Db_Table_Exception("primary key '$key' not in columns for table '$table'");
        }
    }

    /**
     * Returns table information.
     *
     * @return array
     */
    public function info()
    {
        return array(
            'name' => $this->_name,
            'cols' => $this->_cols,
            'primary' => $this->_primary,
        );
    }


    // -----------------------------------------------------------------
    //
    // Manipulation
    //
    // -----------------------------------------------------------------
    
    /**
     * Inserts a new row.
     *
     * Columns must be in underscore format.
     * 
     * @param array $data Column-value pairs.
     * @param string $where An SQL WHERE clause.
     * @return int The last insert ID.
     */
    public function insert(&$data)
    {
        $this->_db->insert(
            $this->_name,
            $data
        );
        return $this->_db->lastInsertId($this->_name, $this->_primary);
    }

    /**
     * Updates existing rows.
     *
     * Columns must be in underscore format.
     *
     * @param array $data Column-value pairs.
     * @param string $where An SQL WHERE clause.
     * @return int The number of rows updated.
     */
    public function update(&$data, $where)
    {
        return $this->_db->update(
            $this->_name,
            $data,
            $where
        );
    }

    /**
     * Deletes existing rows.
     *
     * The WHERE clause must be in native (underscore) format.
     *
     * @param string $where An SQL WHERE clause.
     * @return int The number of rows deleted.
     */
    public function delete($where)
    {
        return $this->_db->delete($this->_name, $where);
    }


    // -----------------------------------------------------------------
    //
    // Retrieval
    //
    // -----------------------------------------------------------------

    /**
     * Fetches rows by primary key.
     *
     * @param scalar|array $val The value of the primary key.
     * @return array Row(s) which matched the primary key value.
     */
    public function find($val)
    {
        $val = (array) $val;
        $key = $this->_primary;
        if (count($val) > 1) {
            $where = array(
                "$key IN(?)" => $val,
            );
            $order = "$key ASC";
            return $this->fetchAll($where, $order);
        } else {
            $where = array(
                "$key = ?" => (isset($val[0]) ? $val[0] : ''),
            );
            return $this->fetchRow($where);
        }
    }

    /**
     * Fetches all rows.
     *
     * Honors the Zend_Db_Adapter fetch mode.
     *
     * @param string|array $where An SQL WHERE clause.
     * @param string|array $order An SQL ORDER clause.
     * @param int $count An SQL LIMIT count.
     * @param int $offset An SQL LIMIT offset.
     * @return mixed The row results per the Zend_Db_Adapter fetch mode.
     */
    public function fetchAll($where = null, $order = null, $count = null,
        $offset = null)
    {
        return new Zend_Db_Table_Rowset(array(
            'db'    => $this->_db,
            'table' => $this,
            'data'  => $this->_fetch('All', $where, $order, $count, $offset),
        ));
    }
    
    /**
     * Fetches one row.
     *
     * Honors the Zend_Db_Adapter fetch mode.
     *
     * @param string|array $where An SQL WHERE clause.
     * @param string|array $order An SQL ORDER clause.
     * @return mixed The row results per the Zend_Db_Adapter fetch mode.
     */
    public function fetchRow($where = null, $order = null)
    {
        return new Zend_Db_Table_Row(array(
            'db'    => $this->_db,
            'table' => $this,
            'data'  => $this->_fetch('Row', $where, $order, 1),
        ));
    }
    
    /**
     * Fetches a new blank row (not from the database).
     * 
     * @return Zend_Db_Table_Row
     */
    public function fetchNew()
    {
        $keys = array_keys($this->_cols);
        $vals = array_fill(0, count($keys), null);
        return new Zend_Db_Table_Row(array(
            'db'    => $this->_db,
            'table' => $this,
            'data'  => array_combine($keys, $vals),

        ));
    }
    
    /**
     * Support method for fetching rows.
     *
     * @param string $type Whether to fetch 'all' or 'row'.
     * @param string|array $where An SQL WHERE clause.
     * @param string|array $order An SQL ORDER clause.
     * @param int $count An SQL LIMIT count.
     * @param int $offset An SQL LIMIT offset.
     * @return mixed The row results per the Zend_Db_Adapter fetch mode.
     */
    protected function _fetch($type, $where = null, $order = null, $count = null,
        $offset = null)
    {
        // selection tool
        $select = $this->_db->select();

        // the FROM clause
        $select->from($this->_name, array_keys($this->_cols));

        // the WHERE clause
        $where = (array) $where;
        foreach ($where as $key => $val) {
            // is $key an int?
            if (is_int($key)) {
                // $val is the full condition
                $select->where($val);
            } else {
                // $key is the condition with placeholder,
                // and $val is quoted into the condition
                $select->where($key, $val);
            }
        }

        // the ORDER clause
        $order = (array) $order;
        foreach ($order as $val) {
            $select->order($val);
        }

        // the LIMIT clause
        $select->limit($count, $offset);

        // return the results
        $method = "fetch$type";
        return $this->_db->$method($select);
    }
}
