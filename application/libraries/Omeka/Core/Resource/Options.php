<?php 
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2009-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * Retrieve all the options from the database.  
 *
 * Options are essentially site-wide variables that are stored in the 
 * database, for example the title of the site.
 *
 * Failure to load this resource currently indicates that Omeka needs to be
 * installed.
 * 
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2009-2010
 */
class Omeka_Core_Resource_Options extends Zend_Application_Resource_ResourceAbstract
{
    private $_installerRedirect = true;

    /**
     * @return array
     */
    public function init()
    {
        $bootstrap = $this->getBootstrap();
        $bootstrap->bootstrap('Db');
        $db = $bootstrap->getResource('Db');
        
        try {
            // This will throw an exception if the options table does not exist
	        $options = $db->fetchPairs("SELECT name, value FROM $db->Option");
        } catch (Zend_Db_Statement_Exception $e) {
            if ($this->_installerRedirect) {
                // Redirect to the install script.
                header('Location: '.WEB_ROOT.'/install/');
                exit;
            } else {
                throw $e;
            }
        }
        
        $this->_convertMigrationSchema($options);

        // Merge in options from config.ini, options specified in config.ini
        // override options from the DB.
        $config = $this->getBootstrap()->bootstrap('Config')->getResource('Config');
        if (isset($config->options)) {
            $options = array_merge($options, $config->options->toArray());
        }
        
        return $options;
    }
    
    /*
     * Indicate whether or not this bootstrap resource should redirect to the 
     * installer if database exceptions are thrown, i.e. if the options table
     * does not exist.
     *
     * @param boolean $flag
     */
    public function setInstallerRedirect($flag) {
        $this->_installerRedirect = (boolean)$flag;
    }

    /**
     * If necessary, convert from the old sequentially-numbered migration scheme
     * to the new timestamped migrations.
     *
     * @param array Omeka options.
     * @return void.
     */
    private function _convertMigrationSchema(array $options)
    {
        if (!isset($options[Omeka_Db_Migration_Manager::ORIG_MIGRATION_OPTION_NAME])) {
            return;
        }

        // 47 is the migration of the completely-updated 1.2.x database.
        // Due to the changed migrations starting with 1.3, we must disallow
        // upgrades from pre-1.2.x versions to post-1.2 versions.
        if ($options[Omeka_Db_Migration_Manager::ORIG_MIGRATION_OPTION_NAME] != '47') {
            throw new Omeka_Db_Migration_Exception;
        }

        $migrationManager = Omeka_Db_Migration_Manager::getDefault($this->getBootstrap()->db);
        $migrationManager->setupTimestampMigrations();
    }
}
