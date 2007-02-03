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
 * Zend_Db_Adapter_Pdo
 */
require_once 'Zend/Db/Adapter/Pdo/Abstract.php';


/**
 * Class for connecting to Oracle databases and performing common operations.
 *
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Db_Adapter_Pdo_Oci extends Zend_Db_Adapter_Pdo_Abstract
{

    /**
     * PDO type.
     *
     * @var string
     */
    protected $_pdoType = 'oci';


    /**
     * Quotes an identifier.
     *
     * @param string $ident The identifier.
     * @return string The quoted identifier.
     * @todo this function is an exact duplicate of the one in Pdo/Oracle.php
     */
    public function quoteIdentifier($ident)
    {
        $ident = str_replace('"', '""', $ident);
        return '"'.$ident.'"';
    }


    /**
     * Returns a list of the tables in the database.
     *
     * @return array
     */
    public function listTables()
    {
        return $this->fetchCol('SELECT table_name FROM all_tables');
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
     * Quote a raw string.
     *
     * @param string $value     Raw string
     * @return string           Quoted string
     */
    public function _quote($value)
    {
        $value = str_replace("'", "''", $value);
        return "'".$value."'";
    }

    /**
     * Adds an adapter-specific LIMIT clause to the SELECT statement.
     *
     * @return string
     */
    public function limit($sql, $count, $offset)
    {
        return $sql;
    }
}
