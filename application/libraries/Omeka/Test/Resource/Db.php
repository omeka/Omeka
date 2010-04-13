<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka_Test
 **/

class Omeka_Test_Resource_Db extends Zend_Application_Resource_Db
{
    const SUPER_USERNAME = 'foobar123';
    const SUPER_PASSWORD = 'foobar123';
    const SUPER_EMAIL    = 'foobar@example.com';
    
    const DEFAULT_USER_ID  = 1;
    
    public function init()
    {        
        $config = $this->_loadConfig();
        $this->setAdapter('Mysqli');
        $this->setParams($config->db->toArray());
        $omekaDb = $this->_getOmekaDb();
        $this->_dropTables($this->getDbAdapter());
        $this->install($omekaDb);
        return $omekaDb;
    }
    
    /**
     * Install the Omeka database as part of the test bootstrap process.
     */
    public function install(Omeka_Db $db)
    {
        $installInfo = array(
            'administrator_email'           => self::SUPER_EMAIL, 
            'copyright'                     => '2010', 
            'site_title'                    => 'Automated Test Installation', 
            'author'                        => 'CHNM', 
            'description'                   => 'This database will be reset after every test run.  DO NOT USE WITH PRODUCTION SITES', 
            'thumbnail_constraint'          => '200', 
            'square_thumbnail_constraint'   => '200', 
            'fullsize_constraint'           => '800', 
            'per_page_admin'                => '10', 
            'per_page_public'               => '10', 
            'show_empty_elements'           => '1',
            'path_to_convert'               => '',
            'super_email'                   => self::SUPER_EMAIL,
            'username'                      => self::SUPER_USERNAME,
            'password'                      => self::SUPER_PASSWORD
        );
        require_once INSTALL_DIR . '/models/Installer.php';
        $installer = new Installer($db, new Installer_Requirements);
        $installer->install($installInfo);        
    }
    
    private function _loadConfig()
    {   
        if (!Zend_Registry::isRegistered('test_config')) {
            //Config dependency
            $config = new Zend_Config_Ini(APP_DIR . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR.'config.ini', 'testing');
            Zend_Registry::set('test_config', $config);
        }
        
        return Zend_Registry::get('test_config');
    }
    
    private function _getOmekaDb()
    {
        $omekaDb = new Omeka_Db($this->getDbAdapter(), 'omeka_');
        return $omekaDb;
    }
        
    /**
     * Drop all the tables in the test database.
     */
    private function _dropTables(Zend_Db_Adapter_Abstract $dbAdapter)
    {
        $dbHelper = new Omeka_Test_DbHelper($dbAdapter);
        $dbHelper->dropTables();
    }    
}