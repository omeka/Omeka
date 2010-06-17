<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka_Test
 **/

require_once INSTALL_DIR . '/models/Installer.php';

class Omeka_Test_Resource_Db extends Zend_Application_Resource_Db
{
    const SUPER_USERNAME = 'foobar123';
    const SUPER_PASSWORD = 'foobar123';
    const SUPER_EMAIL    = 'foobar@example.com';
    
    const DEFAULT_USER_ID  = 1;
    
    const DEFAULT_SITE_TITLE    = 'Automated Test Installation';
    const DEFAULT_AUTHOR        = 'CHNM';
    const DEFAULT_COPYRIGHT     = '2010';
    const DEFAULT_DESCRIPTION   = 'This database will be reset after every test run.  DO NOT USE WITH PRODUCTION SITES';
    
    public function init()
    {   
        $this->getBootstrap()->bootstrap('Config');
        $this->setAdapter('Mysqli');
        $this->setParams(Zend_Registry::get('test_config')->db->toArray());
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
            'copyright'                     => self::DEFAULT_COPYRIGHT, 
            'site_title'                    => self::DEFAULT_SITE_TITLE, 
            'author'                        => self::DEFAULT_AUTHOR, 
            'description'                   => self::DEFAULT_DESCRIPTION, 
            'thumbnail_constraint'          => Installer::DEFAULT_THUMBNAIL_CONSTRAINT, 
            'square_thumbnail_constraint'   => Installer::DEFAULT_SQUARE_THUMBNAIL_CONSTRAINT, 
            'fullsize_constraint'           => Installer::DEFAULT_FULLSIZE_CONSTRAINT, 
            'per_page_admin'                => Installer::DEFAULT_PER_PAGE_ADMIN, 
            'per_page_public'               => Installer::DEFAULT_PER_PAGE_PUBLIC, 
            'show_empty_elements'           => Installer::DEFAULT_SHOW_EMPTY_ELEMENTS,
            'path_to_convert'               => '',
            'super_email'                   => self::SUPER_EMAIL,
            'username'                      => self::SUPER_USERNAME,
            'password'                      => self::SUPER_PASSWORD
        );
        $installer = new Installer($db, new Installer_Requirements);
        $installer->install($installInfo);        
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