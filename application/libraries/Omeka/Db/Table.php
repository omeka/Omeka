<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Database table classes.
 * 
 * Subclasses attached to models must follow the naming convention: 
 * Table_TableName, e.g. Table_ElementSet in models/Table/ElementSet.php.
 * 
 * @package Omeka\Db\Table
 */
class Omeka_Db_Table
{
    const SORT_PARAM = 'sort_field';
    const SORT_DIR_PARAM = 'sort_dir';
    
    /**
     * The name of the model for which this table will retrieve objects.
     *
     * @var string
     */
    protected $_target;
    
    /**
     * The name of the table (sans prefix).
     * 
     * If this is not given, it will be inflected.
     *
     * @var string
     */
    protected $_name;
    
    /**
     * The table prefix.
     * 
     * Generally used to differentiate Omeka installations sharing a database. 
     *
     * @var string
     */
    protected $_tablePrefix;
    
    /**
     * The Omeka database object.
     * 
     * @var Omeka_Db
     */
    protected $_db;
    
    /**
     * Construct the database table object.
     * 
     * Do not instantiate this by itself. Access instances only via
     * Omeka_Db::getTable().
     * 
     * @see Omeka_Db::getTable()
     * @param string $targetModel Class name of the table's model.
     * @param Omeka_Db $db Database object to use for queries.
     */
    public function __construct($targetModel, $db)
    {
        $this->_target = $targetModel;
        $this->_db = $db;
    }
    
    /**
     * Delegate to the database adapter.
     * 
     * Used primarily as a convenience method. For example, you can call 
     * fetchOne() and fetchAll() directly from this object.
     * 
     * @param string $m Method name.
     * @param array $a Method arguments.
     * @return mixed
     */
    public function __call($m, $a)
    {
        if (!method_exists($this->_db, $m) && !method_exists($this->_db->getAdapter(), $m)) {
            throw new BadMethodCallException("Method named '$m' does not exist or is not callable.");
        }
        return call_user_func_array(array($this->_db, $m), $a);
    }
    
    /**
     * Retrieve the alias for this table (the name without the prefix).  
     * 
     * @return string
     */
    public function getTableAlias() {
        if (empty($this->_name)) {
           $this->setTableName();
        }
        return $this->_name;
    }
    
    /**
     * Retrieve the Omeka_Db instance.
     * 
     * @return Omeka_Db
     */
    public function getDb()
    {
        return $this->_db;
    }
    
    /**
     * Determine whether a model has a given column.
     * 
     * @param string $field Field name.
     * @return bool
     */
    public function hasColumn($field)
    {
        return in_array($field, $this->getColumns());
    }

    /**
     * Retrieve a list of all the columns for a given model.
     * 
     * This should be here and not in the model class because get_class_vars() 
     * returns private/protected properties when called from within the class. 
     * Will only return public properties when called in this fashion.
     *
     * @return array
     */
    public function getColumns()
    {
        return array_keys(get_class_vars($this->_target));
    }
    
    /**
     * Retrieve the name of the table for the current table (used in SQL 
     * statements).
     * 
     * If the table name has not been set, it will inflect the table name.
     *
     * @uses Omeka_Db_Table::setTableName().
     * @return string
     */
    public function getTableName()
    {
        if (empty($this->_name)) {
           $this->setTableName();
        }
        
        // Return the table name with the prefix added.
        return $this->getTablePrefix()  . $this->_name;
    }
    
    /**
     * Set the name of the database table accessed by this class.
     * 
     * If no name is provided, it will inflect the table name from the name of
     * the model defined in the constructor. For example, Item -> items.
     * 
     * @uses Inflector::tableize()
     * @param string $name (optional) Table name.
     * @return void
     */
    public function setTableName($name = null)
    {
        if ($name) {
            $this->_name = (string) $name;
        } else {
            $this->_name = Inflector::tableize($this->_target);
        }
    }

    /**
     * Retrieve the table prefix for this table instance.
     * 
     * @return string
     */
    public function getTablePrefix()
    {
        if ($this->_tablePrefix === null) {
            $this->setTablePrefix();
        }
        return $this->_tablePrefix;
    }

    /**
     * Set the table prefix.
     * 
     * Defaults to the table prefix defined by the Omeka_Db instance. This 
     * should remain the default in most cases. However, edge cases may require 
     * customization, e.g. creating wrappers for tables generated by other
     * applications.
     * 
     * @param string|null $tablePrefix
     */
    public function setTablePrefix($tablePrefix = null)
    {
        if ($tablePrefix === null) {
            $this->_tablePrefix = $this->getDb()->prefix;
        } else {
            $this->_tablePrefix = $tablePrefix;
        }
    }

    /**
     * Retrieve a single record given an ID.
     *
     * @param integer $id
     * @return Omeka_Record_AbstractRecord|false
     */
    public function find($id)
    {        
        $select = $this->getSelectForFind($id);
        return $this->fetchObject($select, array());
    }
    
    /**
     * Get a set of objects corresponding to all the rows in the table
     * 
     * WARNING: This will be memory intensive and is thus not recommended for 
     * large data sets.
     *
     * @return array Array of {@link Omeka_Record_AbstractRecord}s.
     */
    public function findAll()
    {
        $select = $this->getSelect();
        return $this->fetchObjects($select);
    }
    
    /**
     * Retrieve an array of key=>value pairs that can be used as options in a 
     * <select> form input.
     * 
     * @uses Omeka_Db_Table::_getColumnPairs()
     * @see Omeka_Db_Table::applySearchFilters()
     * @param array $options (optional) Set of parameters for searching/
     * filtering results.
     * @return array
     */
    public function findPairsForSelectForm(array $options = array())
    {
        $select = $this->getSelectForFindBy($options);
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->from(array(), $this->_getColumnPairs());
        $pairs = $this->getDb()->fetchPairs($select);
        return $pairs;
    }
    
    /**
     * Retrieve the array of columns that are used by findPairsForSelectForm().
     * 
     * This is a template method because these columns are different for every
     * table, but the underlying logic that retrieves the pairs from the 
     * database is the same in every instance.
     * 
     * @see Omeka_Db_Table::findPairsForSelectForm()
     * @return array
     */
    protected function _getColumnPairs()
    {
        throw new BadMethodCallException('Column pairs must be defined by _getColumnPairs() in order to use Omeka_Db_Table::findPairsForSelectForm()!');
    }
    
    /**
     * Retrieve a set of model objects based on a given number of parameters
     * 
     * @uses Omeka_Db_Table::getSelectForFindBy()
     * @param array $params A set of parameters by which to filter the objects
     * that get returned from the database.
     * @param integer $limit Number of objects to return per "page".
     * @param integer $page Page to retrieve.
     * @return array|null The set of objects that is returned
     */
    public function findBy($params = array(), $limit = null, $page = null)
    {
        $select = $this->getSelectForFindBy($params);
        if ($limit) {
            $this->applyPagination($select, $limit, $page);
        }
        return $this->fetchObjects($select);
    }
    
    /**
     * Retrieve a select object for this table.
     * 
     * @return Omeka_Db_Select
     */
    public function getSelect()
    {
        $select = new Omeka_Db_Select($this->getDb()->getAdapter());
        $alias = $this->getTableAlias();
        $select->from(array($alias=>$this->getTableName()), "$alias.*");
        return $select;
    }
    
    /**
     * Retrieve a select object that has had search filters applied to it.
     * 
     * @uses Omeka_Db_Table::getSelectForFindBy()
     * @param array $params optional Set of named search parameters.
     * @return Omeka_Db_Select
     */
    public function getSelectForFindBy($params = array())
    {
        $params = apply_filters($this->_getHookName('browse_params'), $params);
        
        $select = $this->getSelect();
        $sortParams = $this->_getSortParams($params);
        
        if ($sortParams) {
            list($sortField, $sortDir) = $sortParams;
            $this->applySorting($select, $sortField, $sortDir);

            if ($select->getPart(Zend_Db_Select::ORDER)
                && $sortField != 'id'
            ) {
                $alias = $this->getTableAlias();
                $select->order("$alias.id $sortDir");
            }
        }
        
        $this->applySearchFilters($select, $params);
        
        fire_plugin_hook($this->_getHookName('browse_sql'), 
                         array('select' => $select, 'params' => $params));
        
        return $select;
    }
    
    /**
     * Retrieve a select object that is used for retrieving a single record from 
     * the database.
     * 
     * @param integer $recordId
     * @return Omeka_Db_Select
     */
    public function getSelectForFind($recordId)
    {
        // Cast to integer to prevent SQL injection.
        $recordId = (int) $recordId;
        
        $select = $this->getSelect();
        $select->where( $this->getTableAlias().'.id = ?', $recordId);
        $select->limit(1);
        $select->reset(Zend_Db_Select::ORDER);
        
        return $select;
    }
    
    /**
     * Apply a set of filters to a Select object based on the parameters given.
     * 
     * By default, this simply checks the params for keys corresponding to database
     * column names. For more complex filtering (e.g., when other tables are involved),
     * or to use keys other than column names, override this method and optionally
     * call this parent method.
     * 
     * @param Omeka_Db_Select $select
     * @param array $params
     */
    public function applySearchFilters($select, $params)
    {
        $alias = $this->getTableAlias();
        $columns = $this->getColumns();
        foreach($columns as $column) {
            if(array_key_exists($column, $params)) {
                if (is_array($params[$column])) {
                    $nullIndex = array_search(null, $params[$column], true);
                    $where = "`$alias`.`$column` IN (?)";
                    if ($nullIndex !== false) {
                        unset($params[$column][$nullIndex]);
                        if (empty($params[$column])) {
                            $where = "`$alias`.`$column` IS NULL";
                        } else {
                            $where .= " OR `$alias`.`$column` IS NULL";
                        }
                    }
                    $select->where($where, $params[$column]);
                } else {
                    if ($params[$column] === null) {
                        $select->where("`$alias`.`$column` IS NULL");
                    } else {
                        $select->where("`$alias`.`$column` = ?", $params[$column]);
                    }
                }
            }
        }
    }
    
    /**
     * Apply default column-based sorting for a table.
     *
     * @param Omeka_Db_Select $select
     * @param string $sortField Field to sort on.
     * @param string $sortDir Direction to sort.
     */
    public function applySorting($select, $sortField, $sortDir)
    {
        if (empty($sortField) || empty($sortDir)) {
            return;
        }
        if (in_array($sortField, $this->getColumns())) {
            $alias = $this->getTableAlias();
            $select->order("$alias.$sortField $sortDir");
        } else if ($sortField == 'random') {
            $select->order('RAND()');
        }
    }
    
    /**
     * Apply pagination to a select object via the LIMIT and OFFSET clauses.
     * 
     * @param Zend_Db_Select $select
     * @param integer $limit Number of results per "page".
     * @param integer|null $page Page to retrieve, first if omitted.
     * @return Zend_Db_Select
     */
    public function applyPagination($select, $limit, $page = null) 
    {
        if ($page) {
            $select->limitPage($page, $limit);   
        } else {
            $select->limit($limit);
        }
        return $select;
    }   
     
    /**
     * Retrieve an object or set of objects based on an SQL WHERE predicate.
     *
     * @param string $sqlWhereClause
     * @param array $params optional Set of parameters to bind to the WHERE
     * clause. Used to prevent security flaws.
     * @param boolean $findOne optional Whether or not to retrieve a single
     * record or the whole set (retrieve all by default).
     * @return array|Omeka_Record_AbstractRecord|false
     */
    public function findBySql($sqlWhereClause, array $params = array(), $findOne = false)
    {
        $select = $this->getSelect();
        $select->where($sqlWhereClause);
        return $findOne ? $this->fetchObject($select, $params) : $this->fetchObjects($select, $params);
    }
    
    /**
     * Retrieve a count of all the rows in the table.
     *
     * @uses Omeka_Db_Table::getSelectForCount()
     * @param array $params optional Set of search filters upon which to base
     * the count.
     * @return integer
     */
    public function count($params=array())
    {
        $select = $this->getSelectForCount($params);
        return $this->getDb()->fetchOne($select);
    }
    
    /**
     * Check whether a row exists in the table.
     * 
     * @param int $id
     * @return bool
     */
    public function exists($id)
    {
        $alias = $this->getTableAlias();
        $select = $this->getSelect()
            ->reset(Zend_Db_Select::COLUMNS)
            ->columns('id')
            ->where("`$alias`.`id` = ?", (int) $id)
            ->limit(1);
        return (bool) $this->getDb()->fetchOne($select);
    }
    
    /**
     * Apply a public/not public filter to the select object.
     * 
     * A convenience function than derivative table classes may use while 
     * applying search filters.
     * 
     * @see self::applySearchFilters()
     * @param Omeka_Db_Select $select
     * @param bool $isPublic
     */
    public function filterByPublic(Omeka_Db_Select $select, $isPublic)
    {
        $alias = $this->getTableAlias();
        if ($isPublic) {
            $select->where("`$alias`.`public` = 1");
        } else {
            $select->where("`$alias`.`public` = 0");
        }
    }
    
    /**
     * Apply a featured/not featured filter to the select object.
     * 
     * A convenience function than derivative table classes may use while 
     * applying search filters.
     * 
     * @see self::applySearchFilters()
     * @param Omeka_Db_Select $select
     * @param bool $isFeatured
     */
    public function filterByFeatured(Omeka_Db_Select $select, $isFeatured)
    {
        $alias = $this->getTableAlias();
        if ($isFeatured) {
            $select->where("`$alias`.`featured` = 1");
        } else {
            $select->where("`$alias`.`featured` = 0");
        }
    }
    
    /**
     * Apply a date since filter to the select object.
     * 
     * A convenience function than derivative table classes may use while 
     * applying search filters.
     * 
     * @see self::applySearchFilters()
     * @param Omeka_Db_Select $select
     * @param string $dateSince ISO 8601 formatted date
     * @param string $dateField "added" or "modified"
     */
    public function filterBySince(Omeka_Db_Select $select, $dateSince, $dateField)
    {
        // Reject invalid date fields.
        if (!in_array($dateField, array('added', 'modified'))) {
            return;
        }
        
        // Accept an ISO 8601 date, set the tiemzone to the server's default 
        // timezone, and format the date to be MySQL timestamp compatible.
        $date = new Zend_Date($dateSince, Zend_Date::ISO_8601);
        $date->setTimezone(date_default_timezone_get());
        $date = $date->get('yyyy-MM-dd HH:mm:ss');
        
        // Select all dates that are greater than the passed date.
        $alias = $this->getTableAlias();
        $select->where("`$alias`.`$dateField` > ?", $date);
    }
    
    /**
     * Apply a user filter to the select object.
     * 
     * A convenience function than derivative table classes may use while 
     * applying search filters.
     * 
     * @see self::applySearchFilters()
     * @param Omeka_Db_Select $select
     * @param int $userId
     */
    public function filterByUser(Omeka_Db_Select $select, $userId, $userField)
    {
        // Reject invalid user ID fields.
        if (!in_array($userField, array('owner_id', 'user_id'))) {
            return;
        }
        $alias = $this->getTableAlias();
        $select->where("`$alias`.`$userField` = ?", $userId);
    }

    /**
     * Filter returned records by ID.
     *
     * Can specify a range of valid record IDs or an individual ID
     *
     * @version 2.2.2
     * @param Omeka_Db_Select $select
     * @param string $range Example: 1-4, 75, 89
     * @return void
     */
    public function filterByRange($select, $range)
    {
        // Comma-separated expressions should be treated individually
        $exprs = explode(',', $range);

        // Construct a SQL clause where every entry in this array is linked by 'OR'
        $wheres = array();

        $alias = $this->getTableAlias();

        foreach ($exprs as $expr) {
            // If it has a '-' in it, it is a range of item IDs.  Otherwise it is
            // a single item ID
            if (strpos($expr, '-') !== false) {
                list($start, $finish) = explode('-', $expr);

                // Naughty naughty koolaid, no SQL injection for you
                $start  = (int) trim($start);
                $finish = (int) trim($finish);

                $wheres[] = "($alias.id BETWEEN $start AND $finish)";

                //It is a single item ID
            } else {
                $id = (int) trim($expr);
                $wheres[] = "($alias.id = $id)";
            }
        }

        $where = join(' OR ', $wheres);

        $select->where('('.$where.')');
    }
    
    /**
     * Retrieve a select object used to retrieve a count of all the table rows.
     * 
     * @param array $params optional Set of search filters.
     * @return Omeka_Db_Select
     */
    public function getSelectForCount($params = array())
    {
        $select = $params ? $this->getSelectForFindBy($params) : $this->getSelect();
        
        // Make sure the SELECT only pulls down the COUNT() column.
        $select->reset(Zend_Db_Select::COLUMNS);        
        $alias = $this->getTableAlias();
        $select->from(array(), "COUNT(DISTINCT($alias.id))");
        
        // Reset the GROUP and ORDER BY clauses if necessary.
        $select->reset(Zend_Db_Select::ORDER)->reset(Zend_Db_Select::GROUP);
        
        // Reset the LIMIT and OFFSET clauses if necessary.
        $select->reset(Zend_Db_Select::LIMIT_COUNT)->reset(Zend_Db_Select::LIMIT_OFFSET);
        
        return $select;
    }
    
    /**
     * Check whether a given row exists in the database.
     *
     * Currently used to verify that a row exists even though the current user 
     * may not have permissions to access it.
     *
     * @param int $id The ID of the row.
     * @return boolean
     */
    public function checkExists($id)
    {
        $alias = $this->getTableAlias();
        $select = $this->getSelectForCount()->where("$alias.id = ?", $id);
        $count = $this->getDb()->fetchOne($select);
        return ($count == 1);
    }
    
    /**
     * Retrieve a set of record objects based on an SQL SELECT statement.
     *
     * @param string $sql This could be either a string or any object that can
     * be cast to a string (commonly Omeka_Db_Select).
     * @param array $params Set of parameters to bind to the SQL statement.
     * @return array|null Set of Omeka_Record_AbstractRecord instances, or null 
     * if none can be found.
     */
    public function fetchObjects($sql, $params=array())
    {        
        $res = $this->getDb()->query($sql, $params);
        $data = $res->fetchAll();
        
        // Would use fetchAll() but it can be memory-intensive.
        $objs = array();
        foreach ($data as $k => $row) {
            $objs[$k] = $this->recordFromData($row);
        }
        
        return $objs;
    }
    
    /**
     * Retrieve a single record object from the database.
     * 
     * @see Omeka_Db_Table::fetchObjects()
     * @param string $sql
     * @param string $params Parameters to substitute into SQL query.
     * @return Omeka_Record_AbstractRecord or null if no record 
     */
    public function fetchObject($sql, array $params=array())
    {
        $row = $this->getDb()->fetchRow($sql, $params);
        return !empty($row) ? $this->recordFromData($row): null;
    }
    
    /**
     * Populate a record object with data retrieved from the database.
     * 
     * @param array $data A keyed array representing a row from the database.
     * @return Omeka_Record_AbstractRecord
     */
    protected function recordFromData(array $data)
    {
        $class = $this->_target;
        $obj = new $class($this->_db);
        $obj->setArray($data);
        return $obj;
    }
    
    /**
     * Get and parse sorting parameters to pass to applySorting.
     *
     * A sorting direction of 'ASC' will be used if no direction parameter is 
     * passed.
     *
     * @param array $params
     * @return array|null Array of sort field, sort dir if params exist, null 
     * otherwise.
     */
    private function _getSortParams($params)
    {
        if (array_key_exists(self::SORT_PARAM, $params)) {
            $sortField = trim($params[self::SORT_PARAM]);
            $dir = 'ASC';
            // Default to ascending sort with no dir param.
            if (array_key_exists(self::SORT_DIR_PARAM, $params)) {
                $sortDir = trim($params[self::SORT_DIR_PARAM]);
                if ($sortDir === 'a') {
                    $dir = 'ASC';
                } else if ($sortDir === 'd') {
                    $dir = 'DESC';
                }
            }
            return array($sortField, $dir);
        }
        return null;
    }
    
    /**
     * Get the name for a model-specific hook or filter..
     *
     * @param string $suffix The hook-specific part of the hook name.
     * @return string
     */
    private function _getHookName($suffix)
    {
        $modelName = Inflector::tableize($this->_target);
        return "{$modelName}_{$suffix}";
    }
}
