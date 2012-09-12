<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * Load the database schema for an Omeka installation.  
 * 
 * Schema should be defined in an SQL file.
 *
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Installer_Task_Schema implements Installer_TaskInterface
{   
    private $_defaultTables = array(
        'collections',
        'element_texts',
        'item_types',
        'mime_element_set_lookup',
        'processes',
        'tags',
        'elements',
        'item_types_elements',
        'options',
        'users',
        'element_sets',
        'files',
        'items',
        'plugins',
        'taggings',
        'users_activations',
        'sessions',
        'search_texts',
    );
    
    private $_tables = array();
    
    /**
     * Add an SQL table to the list of tables to create.
     * 
     * @param string $tableName
     * @param string $sqlFilePath
     */
    public function addTable($tableName, $sqlFilePath)
    {
        if (!(file_exists($sqlFilePath) && is_readable($sqlFilePath))) {
            throw new Installer_Task_Exception(__("Invalid SQL file given: %s.", $sqlFilePath));
        }
        $this->_tables[$tableName] = $sqlFilePath;
    }
    
    /**
     * Add a set of SQL tables to the list.
     * 
     * @param array $tables
     */
    public function addTables(array $tables)
    {
        foreach ($tables as $tableName => $sqlFilePath) {
            $this->addTable($tableName, $sqlFilePath);
        }
    }
    
    /**
     * Set the list of SQL tables.
     * 
     * @param array $tables
     */
    public function setTables(array $tables)
    {
        $this->_tables = array();
        $this->addTables($tables);
    }
    
    /**
     * Remove an SQL table from the list.
     * 
     * @param string $tableName
     */
    public function removeTable($tableName)
    {
        if (!array_key_exists($tableName, $this->_tables)) {
            throw new Installer_Task_Exception(__("Table named '%s' cannot be removed from the list (not found).", $tableName));
        }
        unset($this->_tables[$tableName]);
    }
    
    /**
     * Retrieve list of tables being installed.
     */
    public function getTables()
    {
        return $this->_tables;
    }
    
    /**
     * Add all tables corresponding to the default Omeka installation.
     */        
    public function useDefaultTables()
    {
        foreach ($this->_defaultTables as $tableName) {
            $this->_tables[$tableName] = CORE_DIR . '/'
                . 'schema' . '/' . $tableName . '.sql';
        }
    }
            
    public function install(Omeka_Db $db)
    {
        if (empty($this->_tables)) {
            throw new Installer_Task_Exception(__("No SQL files were given to create the schema."));
        }
        
        foreach ($this->_tables as $tableName => $sqlFilePath) {
            try {
                $db->loadSqlFile($sqlFilePath);
            } catch (Zend_Db_Exception $e) {
                throw new Installer_Task_Exception(__("Schema task failed on table '%s' with %s: %s", $db->prefix . $tableName, get_class($e), $e->getMessage()));
            }
        }
    }
}
