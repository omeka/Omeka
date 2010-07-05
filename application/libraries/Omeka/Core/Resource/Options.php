<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * Retrieve all the options from the database.  
 *
 * Options are essentially site-wide variables that are stored in the 
 * database, for example the title of the site.
 * 
 * @package Omeka
 * @copyright Center for History and New Media, 2009
 **/
class Omeka_Core_Resource_Options extends Zend_Application_Resource_ResourceAbstract
{
    public function init()
    {
        $bootstrap = $this->getBootstrap();
        $bootstrap->bootstrap('Db');
        $db = $bootstrap->getResource('Db');
        
        // This will throw an exception if the options table does not exist
        $options = $db->fetchPairs("SELECT name, value FROM $db->Option");
        
        $this->_convertMigrationSchema($options);
        
        return $options;
    }
    
    private function _convertMigrationSchema(array $options)
    {
        if (!isset($options[Omeka_Db_Migration_Manager::ORIG_MIGRATION_OPTION_NAME])) {
            return;
        }
        
        $migrationManager = Omeka_Db_Migration_Manager::getDefault($this->getBootstrap()->db);
        $migrationManager->setupTimestampMigrations();
    }
}
