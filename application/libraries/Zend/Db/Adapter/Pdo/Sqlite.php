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


/**
 * Zend_Db_Adapter_Pdo_Abstract
 */
require_once 'Zend/Db/Adapter/Pdo/Abstract.php';


/**
 * Class for connecting to MySQL databases and performing common operations.
 *
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Db_Adapter_Pdo_Sqlite extends Zend_Db_Adapter_Pdo_Abstract
{
    /**
     * PDO type
     *
     * @var string
     */
     protected $_pdoType = 'sqlite';
     
     
    /**
     * Constructor.
     *
     * $config is an array of key/value pairs containing configuration
     * options.  Note that the SQLite options are different than most of
     * the other PDO adapters in that no username or password are needed.
     * Also, an extra config key "sqlite2" specifies compatibility mode.
     *
     * dbname    => (string) The name of the database to user (required,
     *                       use :memory: for memory-based database)
     *
     * sqlite2   => (boolean) PDO_SQLITE defaults to SQLite 3.  For compatibility
     *                        with an older SQLite 2 database, set this to TRUE.
     *
     * @param array $config An array of configuration keys.
     */
    public function __construct($config)
    {
        if (isset($config['sqlite2']) && $config['sqlite2']) {
            $this->_pdoType = 'sqlite2';
        }

        // SQLite uses no username/password.  Stub to satisfy parent::_connect()
        $this->_config['username'] = null;
        $this->_config['password'] = null;
        
        return parent::__construct($config);
    }


    /**
     * DSN builder
     */
    protected function _dsn()
    {
        return $this->_pdoType .':'. $this->_config['dbname'];        
    }


    /**
     * Quotes an identifier.
     *
     * @param string $ident The identifier.
     * @return string The quoted identifier.
     */
    public function quoteIdentifier($ident)
    {
        return $this->quote($ident);
    }


    /**
     * Returns a list of the tables in the database.
     *
     * @return array
     */
    public function listTables()
    {
        $sql = "SELECT name FROM sqlite_master WHERE type='table' "
             . "UNION ALL SELECT name FROM sqlite_temp_master "
             . "WHERE type='table' ORDER BY name";

        return $this->fetchCol($sql);
    }


    /**
     * Returns the column descriptions for a table.
     *
     * @return array
     */
    public function describeTable($table)
    {
        $sql = "PRAGMA table_info($table)";
        $result = $this->fetchAll($sql);
        $descr = array();
        foreach ($result as $key => $val) {
            $descr[$val['name']] = array(
                'name'    => $val['name'],
                'type'    => $val['type'],
                'notnull' => (bool) $val['notnull'],
                'default' => $val['dflt_value'],
                'primary' => (bool) $val['pk'],
            );
        }
        return $descr;
    }


    /**
     * Adds an adapter-specific LIMIT clause to the SELECT statement.
     *
     * @return string
     */
    public function limit($sql, $count, $offset)
    {
        if ($count > 0) {
            $sql .= "LIMIT $count";
            if ($offset > 0) {
                $sql .= " OFFSET $offset";
            }
        }
        return $sql;
    }
}
