<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Wrapper for Zend_Session_SaveHandler_DbTable to hard code the table 
 * definition. This boosts performance by skipping the DESCRIBE query that 
 * retrieves this metadata by default.
 *
 * Note that this must be updated meticulously after any changes to the 
 * sessions table schema.
 * 
 * @package Omeka\Session
 */
class Omeka_Session_SaveHandler_DbTable extends Zend_Session_SaveHandler_DbTable
{
    public function init()
    {
        $db = Zend_Registry::get('bootstrap')->getResource('Db');
        $tableName = $db->prefix . 'sessions';
        $this->_metadata = array(
            'id' => array(
                'SCHEMA_NAME' => null,
                'TABLE_NAME' => $tableName,
                'COLUMN_NAME' => 'id',
                'COLUMN_POSITION' => 1,
                'DATA_TYPE' => 'varchar',
                'DEFAULT' => '',
                'NULLABLE' => false,
                'LENGTH' => '128',
                'SCALE' => null,
                'PRECISION' => null,
                'UNSIGNED' => null,
                'PRIMARY' => true,
                'PRIMARY_POSITION' => 1,
                'IDENTITY' => false,
            ),    
            'modified' => array(
                'SCHEMA_NAME' => null,
                'TABLE_NAME' => $tableName,
                'COLUMN_NAME' => 'modified',
                'COLUMN_POSITION' => 2,
                'DATA_TYPE' => 'int',
                'DEFAULT' => null,
                'NULLABLE' => true,
                'LENGTH' => null,
                'SCALE' => null,
                'PRECISION' => null,
                'UNSIGNED' => null,
                'PRIMARY' => false,
                'PRIMARY_POSITION' => null,
                'IDENTITY' => false,
            ),
            'lifetime' => array(
                'SCHEMA_NAME' => null,
                'TABLE_NAME' => $tableName,
                'COLUMN_NAME' => 'lifetime',
                'COLUMN_POSITION' => 3,
                'DATA_TYPE' => 'int',
                'DEFAULT' => null,
                'NULLABLE' => true,
                'LENGTH' => null,
                'SCALE' => null,
                'PRECISION' => null,
                'UNSIGNED' => null,
                'PRIMARY' => false,
                'PRIMARY_POSITION' => null,
                'IDENTITY' => false,
            ),
            'data' => array(
                'SCHEMA_NAME' => null,
                'TABLE_NAME' => $tableName,
                'COLUMN_NAME' => 'data',
                'COLUMN_POSITION' => 4,
                'DATA_TYPE' => 'text',
                'DEFAULT' => null,
                'NULLABLE' => true,
                'LENGTH' => null,
                'SCALE' => null,
                'PRECISION' => null,
                'UNSIGNED' => null,
                'PRIMARY' => false,
                'PRIMARY_POSITION' => null,
                'IDENTITY' => false,
            )
        );
    }

    /**
     * Write session data
     *
     * @param string $id
     * @param string $data
     * @return boolean
     */
    public function write($id, $data)
    {
        parent::write($id, $data);

        // Discard parent's return value and return true (PHP 7 actually cares about this)
        return true;
    }
}
