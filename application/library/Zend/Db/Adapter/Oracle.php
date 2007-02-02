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


/** Zend_Db_Adapter_Abstract */
require_once 'Zend/Db/Adapter/Abstract.php';

/** Zend_Db_Adapter_Oracle_Exception */
require_once 'Zend/Db/Adapter/Oracle/Exception.php';

/** Zend_Db_Statement_Oracle */
require_once 'Zend/Db/Statement/Oracle.php';


/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Db_Adapter_Oracle extends Zend_Db_Adapter_Abstract
{
    /**
     * User-provided configuration.
     *
     * Basic keys are:
     *
     * username => (string) Connect to the database as this username.
     * password => (string) Password associated with the username.
     * database => Either the name of the local Oracle instance, or the
     *             name of the entry in tnsnames.ora to which you want to connect.
     *
     * @todo fix inconsistency between "database" used here and "dbname" use elsewhere
     * @var array
     */
    protected $_config = array(
        'dbname' => null,
        'username' => null,
        'password' => null,
    );

	protected $_execute_mode = OCI_COMMIT_ON_SUCCESS;

    /**
     * Constructor.
     *
     * $config is an array of key/value pairs containing configuration
     * options.  These options are common to most adapters:
     *
     * username => (string) Connect to the database as this username.
     * password => (string) Password associated with the username.
     * database => Either the name of the local Oracle instance, or the
     *             name of the entry in tnsnames.ora to which you want to connect.
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
        if (! array_key_exists('password', $config) || ! array_key_exists('username', $config)) {
            throw new Zend_Db_Adapter_Exception('config array must have at least a username and a password');
        }

        // @todo Let this protect backward-compatibility for one release, then remove
        if (array_key_exists('database', $config) || ! array_key_exists('dbname', $config)) {
            $config['dbname'] = $config['database'];
            unset($config['database']);
            trigger_error("Deprecated config key 'database', use 'dbname' instead.", E_USER_NOTICE);
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
        /**
         * @todo should check resource here
         */
        if ($this->_connection) {
            return;
        }
		
		if (isset($this->_config['dbname'])) {
			$this->_connection = oci_connect($this->_config['username'], $this->_config['password'], $this->_config['dbname']);
		} else {
			$this->_connection = oci_connect($this->_config['username'], $this->_config['password']);
		}

        // check the connection
		if (!$this->_connection) {
            throw new Zend_Db_Adapter_Oracle_Exception(oci_error());
        }
    }


    /**
     * Returns an SQL statement for preparation.
     *
     * @param string $sql The SQL statement with placeholders.
     * @return Zend_Db_Statement_Oracle
     */
    public function prepare($sql)
    {
        $this->_connect();
        $stmt = new Zend_Db_Statement_Oracle($this, $sql);
        $stmt->setFetchMode($this->_fetchMode);
        return $stmt;
    }

    /**
     * Gets the last inserted ID.
     *
     * @param  string $tableName   name of table associated with sequence
     * @param  string $primaryKey  not used in this adapter
     * @return integer
     */
    public function lastInsertId($tableName = null, $primaryKey = null)
    {
		if (!$tableName) {
			throw new Zend_Db_Adapter_Exception("Sequence name must be specified");
		}
        $this->_connect();
		$data = $this->fetchCol("SELECT $tableName.currval FROM dual");
		return $data[0]; //we can't fail here, right? if the sequence doesn't exist we should fail earlier.
    }

    /**
     * Returns a list of the tables in the database.
     *
     * @return array
     */
    public function listTables()
    {
        $this->_connect();
		$data = $stmt->fetchCol('SELECT table_name FROM all_tables');
		return $data;
    }

    /**
     * Returns the column descriptions for a table.
     *
     * @return array
     */
    public function describeTable($table)
    {
        $table = strtoupper($table);
        $sql = "SELECT column_name, data_type, data_length, nullable, data_default from all_tab_columns WHERE table_name='$table' ORDER BY column_name";
        $result = $this->query($sql);
        while ($val = $result->fetch()) {
			$descr[$val['column_name']] = array(
               'name'    => $val['column_name'],
               'notnull' => (bool)($val['nullable'] === 'N'), // nullable is N when mandatory
               'type'    => $val['data_type'],
               'default' => $val['data_default'],
               'length'  => $val['data_length']
            );
        }
        return $descr;
    }


    /**
     * Leave autocommit mode and begin a transaction.
     *
     * @return void
     */
    protected function _beginTransaction()
    {
        $this->_setExecuteMode(OCI_DEFAULT);
    }

    /**
     * Commit a transaction and return to autocommit mode.
     *
     */
    protected function _commit()
    {
		if (!oci_commit($this->_connection)) {
			throw new Zend_Db_Adapter_Oracle_Exception(oci_error($this->_connection));
		}
        $this->_setExecuteMode(OCI_COMMIT_ON_SUCCESS);
    }

    /**
     * Roll back a transaction and return to autocommit mode.
     *
     * @return void
     */
    protected function _rollBack()
    {
		if (!oci_rollback($this->_connection)) {
			throw new Zend_Db_Adapter_Oracle_Exception(oci_error($this->_connection));
		}
        $this->_setExecuteMode(OCI_COMMIT_ON_SUCCESS);
    }


    /**
     * Set the fetch mode.
     *
     * @param int $mode A fetch mode.
     * @return void
     * @todo Support FETCH_CLASS and FETCH_INTO.
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
                throw new Zend_Db_Adapter_Exception('Invalid fetch mode specified');
                break;
        }
    }


    /**
     * Quote a raw string.
     *
     * @param string $value     Raw string
     * @return string           Quoted string
     */
    protected function _quote($value)
    {
        //@todo should we throw an exception here?
		return $value;
    }


    /**
     * Quotes an identifier.
     *
     * @param string $ident The identifier.
     * @return string The quoted identifier.
     */
    public function quoteIdentifier($ident)
    {
        //@todo should we throw an exception here?
		return $ident;
    }


    /**
     * Adds an adapter-specific LIMIT clause to the SELECT statement.
     *
     * @return string
     */
    public function limit($sql, $count, $offset)
    {
        /*
        Oracle doesn't have a LIMIT statement implementation, so we have to "emulate" it using rnum
        */
        $limit_sql = "SELECT
                            zsubselect2.*
                        FROM
                            (
                             SELECT
                                   rownum zrownum,
                                   zsubselect1.*
                               FROM
                                   (
                                    ".$sql."
                                   )
                                   zsubselect1
                            )
                            zsubselect2
                       WHERE
                            zrownum BETWEEN ".$offset." AND ".($offset+$count)."
                      ";
        return $limit_sql;
    }

	private function _setExecuteMode($mode) {
		switch($mode) {
			case OCI_COMMIT_ON_SUCCESS:
			case OCI_DEFAULT:
			case OCI_DESCRIBE_ONLY:
				$this->_execute_mode = $mode;
				break;
			default:
				throw new Zend_Db_Adapter_Exception('wrong execution mode specified');
				break;
		}
	}

	public function _getExecuteMode() {
		return $this->_execute_mode;
	}
}


