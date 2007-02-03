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


/** Zend_Db_Adapter_Pdo_Abstract */
require_once 'Zend/Db/Adapter/Pdo/Abstract.php';


/**
 * Class for connecting to MSSQL databases and performing common operations.
 *
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Db_Adapter_Pdo_Mssql extends Zend_Db_Adapter_Pdo_Abstract
{
    /**
     * PDO type.
     *
     * @var string
     */
    protected $_pdoType = 'mssql';


    /**
     * Quotes an identifier.
     *
     * @param string $ident The identifier.
     * @return string The quoted identifier.
     */
    public function quoteIdentifier($ident)
    {
        return '[' . str_replace(']', ']]', $ident) . ']';
    }


    /**
     * Returns a list of the tables in the database.
     *
     * @return array
     */
    public function listTables()
    {
        $sql = "SELECT name FROM sysobjects WHERE type = 'U' ORDER BY name";
        return $this->fetchCol($sql);
    }


    /**
     * Returns the column descriptions for a table.
     *
     * @return array
     */
    public function describeTable($table)
    {
        $sql = "exec sp_columns @table_name = " . $this->quoteIdentifier($table);
        $result = $this->fetchAll($sql);
        $descr = array();
        foreach ($result as $key => $val) {
            if (strstr($val['type_name'], ' ')) {
                list($type, $identity) = explode(' ', $val['type_name']);
            } else {
                $type = $val['type_name'];
                $identity = '';
            }

            if ($type == 'varchar') {
                // need to add length to the type so we are compatible with
                // Zend_Db_Adapter_Pdo_Mysql!
                $type .= '('.$val['length'].')';
            }

            $descr[$val['column_name']] = array(
                'name'    => $val['column_name'],
                'type'    => $type,
                'notnull' => (bool) ($val['is_nullable'] === 'NO'),
                'default' => $val['column_def'],
                'primary' => (strtolower($identity) == 'identity'),
            );
        }
        return $descr;
    }


    /**
     * Adds an adapter-specific LIMIT clause to the SELECT statement.
     *
     * @link http://lists.bestpractical.com/pipermail/rt-devel/2005-June/007339.html
     * @return string
     */
     public function limit($sql, $count, $offset)
     {
        if ($count) {

            $orderby = stristr($sql, 'ORDER BY');
            if ($orderby !== false) {
                $sort = (stripos($orderby, 'desc') !== false) ? 'desc' : 'asc';
                $order = str_ireplace('ORDER BY', '', $orderby);
                $order = trim(preg_replace('/ASC|DESC/i', '', $order));
            }

            $sql = preg_replace('/^SELECT /i', 'SELECT TOP '.($count+$offset).' ', $sql);

            $sql = 'SELECT * FROM (SELECT TOP '.$count.' * FROM ('.$sql.') AS inner_tbl';
            if ($orderby !== false) {
                $sql .= ' ORDER BY '.$order.' ';
                $sql .= (stripos($sort, 'asc') !== false) ? 'DESC' : 'ASC';
            }
            $sql .= ') AS outer_tbl';
            if ($orderby !== false) {
                $sql .= ' ORDER BY '.$order.' '.$sort;
            }
        }
        return $sql;
    }


    /**
     * Gets the last inserted ID.
     *
     * @param  string $tableName   not used in this adapter
     * @param  string $primaryKey  not used in this adapter
     * @return integer
     */
    public function lastInsertId($tableName = null, $primaryKey = null)
    {
        $sql = 'select @@IDENTITY';
        return (int)$this->fetchOne($sql);
    }

}
