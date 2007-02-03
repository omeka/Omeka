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
 * Class for connecting to MySQL databases and performing common operations.
 *
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Db_Adapter_Pdo_Mysql extends Zend_Db_Adapter_Pdo_Abstract
{

    /**
     * PDO type.
     *
     * @var string
     */
    protected $_pdoType = 'mysql';


    /**
     * Quotes an identifier.
     *
     * @param string $ident The identifier.
     * @return string The quoted identifier.
     * @todo Quoting in this function does not work for versions older than 4.1.x:
     *       http://dev.mysql.com/doc/refman/4.1/en/legal-names.html
     * @todo filter according to *all* of the rules in:
     *       http://dev.mysql.com/doc/refman/5.0/en/legal-names.html
     * @todo this function is an exact duplicate of the one in Pdo/Mysql.php
     */
    public function quoteIdentifier($ident)
    {
        $ident = str_replace('`', '``', $ident);
        return "`$ident`";
    }


    /**
     * Returns a list of the tables in the database.
     *
     * @return array
     */
    public function listTables()
    {
        return $this->fetchCol('SHOW TABLES');
    }


    /**
     * Returns the column descriptions for a table.
     *
     * @return array
     */
    public function describeTable($table)
    {
        $sql = "DESCRIBE $table";
        $result = $this->fetchAll($sql);
        $descr = array();
        foreach ($result as $key => $val) {
            $descr[$val['field']] = array(
                'name'    => $val['field'],
                'type'    => $val['type'],
                'notnull' => (bool) ($val['null'] != 'YES'), // not null is NO or empty, null is YES
                'default' => $val['default'],
                'primary' => (strtolower($val['key']) == 'pri'),
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
            $offset = ($offset > 0) ? $offset : 0;
            $sql .= "LIMIT $offset, $count";
        }
        return $sql;
    }
}
