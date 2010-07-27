<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Load the database schema for an Omeka installation.  
 * 
 * Schema should be defined in an SQL file.
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2007-2010
 */
class Installer_Task_Schema implements Installer_TaskInterface
{    
    private $_schemaFilePath;
    
    public function setSchemaFile($path)
    {
        $this->_schemaFilePath = $path;
    }
    
    public function install(Omeka_Db $db)
    {
        if (!$this->_schemaFilePath) {
            throw new Installer_Task_Exception("Schema file was not given.");
        }
        
        if (!file_exists($this->_schemaFilePath)) {
            throw new Installer_Task_Exception("Schema file does not exist.");
        }
        
        if (!is_readable($this->_schemaFilePath)) {
            throw new Installer_Task_Exception("Schema file is not readable.");
        }
        
        $db->loadSqlFile($this->_schemaFilePath);
    }
}
