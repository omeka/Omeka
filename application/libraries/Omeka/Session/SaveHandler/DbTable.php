<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2011
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * Wrapper for Zend_Session_SaveHandler_DbTable to hard code the table 
 * definition. This boosts performance by skipping the DESCRIBE query that 
 * retrieves this metadata by default.
 *
 * Note that this must be updated meticulously after any changes to the 
 * sessions table schema.
 *
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2011
 */
class Omeka_Session_SaveHandler_DbTable 
    extends Zend_Session_SaveHandler_DbTable
{

    public function init()
    {
        $db = Omeka_Context::getInstance()->db;
        $tableName = $db->prefix . 'sessions';
        $this->_metadata = array(
            'id' => array(
                'SCHEMA_NAME' => null,
                'TABLE_NAME' => $tableName,
                'COLUMN_NAME' => 'id',
                'COLUMN_POSITION' => 1,
                'DATA_TYPE' => 'char',
                'DEFAULT' => '',
                'NULLABLE' => false,
                'LENGTH' => '32',
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
}
