<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * Table classes are instantiated by Omeka_Db.  
 * A new instance is created for each call to a table method, so keep these 
 * lightweight.  Classes that override Omeka_Db_Table must follow the naming 
 * convention: model name + Table (e.g, ExhibitTable).  Classes that override 
 * Omeka_Db_Table are not loaded automatically so they must be req_once'd within 
 * the model itself.
 *
 * @package Omeka
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class Omeka_Db_Table
{
    /**
     * The name of the model that this table will retrieve objects for.
     *
     * @var string
     **/
    protected $_target;
    
    /**
     * The name of the table (sans prefix).  If this is not given, it will
     * be inflected magically.
     *
     * @var string
     **/
    protected $_name;
    
    /**
     * The alias used for this table.  If not given, it will be inflected.
     * 
     * @see Omeka_Db_Table::getTableAlias()
     * @var string
     **/
    protected $_alias;
    
    /**
     * Instantiate
     * 
     * @param string Class name of the table's model
     * @param Omeka_Db Database object to use for queries
     * @return void
     **/
    public function __construct($targetModel, $db)
    {
        $this->_target = $targetModel;
        $this->_db = $db;
    }
    
    /**
     * Delegate to the DB adapter. Used primarily as a convenience method.
     * 
     * For example, now you can call fetchOne() from this table object.
     * 
     * @since 0.10
     * @param string
     * @param array
     * @return mixed
     */
    public function __call($m, $a)
    {
        return call_user_func_array(array($this->_db, $m), $a);
    }
    
    /**
     * @internal HACK But it will do for now.
     * 
     * @param string Class name
     * @return string
     **/
    public function getTableAlias() {
        return !empty($this->_alias) ? $this->_alias : strtolower($this->_target[0]);
    }
    
    /**
     * Retrieve the Omeka_Db instance.
     * 
     * @return Omeka_Db
     **/
    public function getDb()
    {
        return $this->_db;
    }
    
    /**
     * Determine whether or not a model has a given column
     * 
     * @param string Field name
     * @return bool
     **/
    public function hasColumn($field)
    {
        $cols = $this->getColumns();
        
        return in_array($field, $cols);
    }

    /**
     * Retrieve a list of all the columns for a given model
     * 
     * Note to self: This has to be here and not in the model itself because get_class_vars() returns private/protected
     * when called inside its own class    
     *
     * @return array
     **/
    public function getColumns()
    {
        return array_keys(get_class_vars($this->_target));
    }
    
    /**
     * Retrieve the name of the table for the current table (used in SQL statements).
     * If the table name has not been set, then it will automagically inflect a table name
     *
     * @return string
     **/
    public function getTableName()
    {
        if (empty($this->_name)) {
           $this->setTableName();
        }
        
        //Return the table name with the prefix added
        return $this->getDb()->prefix . $this->_name;
    }
    
    public function setTableName($name=null)
    {
        if ($name) {
            $this->_name = (string) $name;
        } else {
            $this->_name = Inflector::tableize($this->_target);
        }
    }
    
    /**
     * Retrieve a single record given an ID
     *
     * @param int $id
     * @return Omeka_Record | false
     **/
    public function find($id)
    {        
        $select = $this->getSelectForFind($id);
        return $this->fetchObject($select, array());
    }
    
    /**
     * Get a set of objects corresponding to all the rows in the table
     * 
     * WARNING: This may be memory/time intensive and is not recommended for large data sets.
     * So far this gets used for any model that does not paginate, i.e. all of them except Items.
     *
     * @return array
     **/
    public function findAll()
    {
        $select = $this->getSelect();
        return $this->fetchObjects($select);
    }
    
    /**
     * Retrieve an array of key=>value pairs that can be used as options in a 
     * <select> form input.  As it applies to the given table.
     * 
     * @return array
     **/
    public function findPairsForSelectForm()
    {
        $select = $this->getSelect();
        $select->reset('columns');
        $select->from(array(), $this->_getColumnPairs());
        $pairs = $this->getDb()->fetchPairs($select);
        return $pairs;
    }
    
    /**
     * Retrieve the array of columns that are used by findPairsForSelectForm().
     * 
     * This is a template method because these columns are different for every
     * table, but the underlying logic that retrieves the pairs from the 
     * database is the same in every instance.  This is an attempt to stay DRY.
     * 
     * @see Omeka_Db_Table::findPairsForSelectForm()
     * @return array
     **/
    protected function _getColumnPairs()
    {
        throw new Exception('Column pairs must be defined in order to use ' .
            'Omeka_Db_Table::findPairsForSelectForm()!');
    }
    
    /**
     * Retrieve a set of model objects based on a given number of parameters
     * 
     * @param array A set of parameters by which to filter the objects
     * that get returned from the DB
     * @return array|null The set of objects that is returned
     **/
    public function findBy($params=array())
    {
        $select = $this->getSelectForFindBy($params);
        return $this->fetchObjects($select);
    }
    
    /**
     * Retrieve a Select object for this table
     * 
     * @param string
     * @return Omeka_Db_Select
     **/
    public function getSelect()
    {
        $select = new Omeka_Db_Select;
        $alias = $this->getTableAlias();
        $select->from(array($alias=>$this->getTableName()), "$alias.*");    
        return $select;    
    }
    
    /**
     * Retrieve a Select object that has had browsing filters applied to it
     * 
     * @param array
     * @return Omeka_Db_Select
     **/
    public function getSelectForFindBy($params=array())
    {
        $select = $this->getSelect();
        $this->applySearchFilters($select, $params);
        return $select;
    }
    
    public function getSelectForFind($recordId)
    {
        //Cast to integer to prevent SQL injection
        $id = (int) $id;

        $select = $this->getSelect();
        $select->where( $this->getTableAlias().'.id = ?', $recordId);
        $select->limit(1);     
        
        return $select;   
    }
    
    /**
     * Apply a set of filters to a SELECT statement based on the parameters given
     * 
     * @param Zend_Db_Select
     * @param array
     * @return void
     **/
    public function applySearchFilters($select, $params) {}
        
    /**
     * Return a set of objects based on a SQL WHERE predicate (see RoR / other frameworks)
     *
     * @return array|false
     **/
    public function findBySql($sqlWhereClause, array $params=array(), $findOne=false)
    {
        $select = $this->getSelect();
        $select->where($sqlWhereClause);
        return $findOne ? $this->fetchObject($select, $params) : $this->fetchObjects($select, $params);
    }
    
    /**
     * Retrieve a count of all the rows in the table.
     *
     * @return integer
     **/
    public function count($params=array())
    {
        $select = $this->getSelectForCount($params);
        return $this->getDb()->fetchOne($select);
    }
    
    /**
     * 
     * 
     * @param array
     * @return Omeka_Db_Select
     **/
    public function getSelectForCount($params=array())
    {
        $select = $params ? $this->getSelectForFindBy($params) : $this->getSelect();
        
        //Make sure the SELECT only pulls down the COUNT() column
        $select->reset('columns');        
        $alias = $this->getTableAlias();
        $select->from(array(), "COUNT(DISTINCT($alias.id))");
        
        //Reset the GROUP and ORDER BY clauses if necessary
        $select->reset('order')->reset('group');
        return $select;        
    }
    
    /**
     * Check whether or not a given row exists in the database
     *
     * Right now this is used mainly to verify that a row exists even though the current user does not have permissions to access it
     *
     * @param int $id The ID of the row
     * @return bool
     **/
    public function checkExists($id)
    {
        $alias = $this->getTableAlias();
        $select = $this->getSelectForCount()->where("$alias.id = ?", $id);
        $count = $this->getDb()->fetchOne($select);
        
        return ($count == 1);
    }
    
    /**
     * Take a SQL SELECT statement and use the resulting data to populate record objects
     *
     * @param string $sql
     * @param array $params To bind to prepared SQL statement
     * @param bool $onlyOne If true, then return only the first object from the result set
     * @return mixed - array of Omeka_Record | Omeka_Record | null | empty array
     **/
    public function fetchObjects($sql, $params=array())
    {        
        $res = $this->getDb()->query($sql, $params);
        $data = $res->fetchAll();
                            
        //Would use fetchAll() but it can be memory-intensive
        $objs = array();
        foreach ($data as $k => $row) {
            $objs[$k] = $this->recordFromData($row);
        }
        
        return $objs;
    }
    
    /**
     * Populate and return one model object based on the SQL statement that
     * is provided.
     * 
     * @param string|Omeka_Db_Select
     * @return Omeka_Record
     **/
    public function fetchObject($sql, array $params=array())
    {
        $row = $this->getDb()->fetchRow($sql, $params);
        return !empty($row) ? $this->recordFromData($row): null;
    }
    
    /**
     *
     * @return Omeka_Record
     **/
    protected function recordFromData(array $data)
    {
        $class = $this->_target;
        $obj = new $class;
        $obj->setArray($data);
        return $obj;
    }
}