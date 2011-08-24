<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

require_once FORM_DIR . '/Install.php';

/**
 * 
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Installer_DefaultTest extends PHPUnit_Framework_TestCase
{
    const DB_PREFIX = 'omeka_';
    const USER_ID = 1;
    const ENTITY_ID = 2;
    const USERS_PLANS_ID = 3;
    
    
    public function setUp()
    {
        $this->dbAdapter = new Zend_Test_DbAdapter;
        $this->db = new Omeka_Db($this->dbAdapter, self::DB_PREFIX);
        $this->form = new Omeka_Form_Install;
        $this->profilerHelper = new Omeka_Test_Helper_DbProfiler($this->dbAdapter->getProfiler(),
            $this);
    }
    
    public function testConstructor()
    {
        $installer = new Installer_Default($this->db);
    }
    
    /**
     * @expectedException Installer_Exception
     */
    public function testThrowsExceptionIfNoFormSet()
    {
        $installer = new Installer_Default($this->db);
        try {
            $installer->install();
        } catch (Exception $e) {
            $this->assertContains("Form was not set via setForm().", $e->getMessage());
            throw $e;
        }
    }
    
    public function testRunInstaller()
    {
        $this->dbAdapter->appendLastInsertIdToStack(self::USERS_PLANS_ID);
        $this->dbAdapter->appendLastInsertIdToStack(self::USER_ID);
        $this->dbAdapter->appendLastInsertIdToStack(self::ENTITY_ID);
        $installer = new Installer_Default($this->db);
        $this->form->setDefaults(array(
            'username' => 'foobar',
            'password' => 'foobar',
            'password_confirm'  => 'foobar',
            'super_email' => 'foobar@example.com',
            'administrator_email'           => Omeka_Test_Resource_Db::SUPER_EMAIL, 
            'copyright'                     => Omeka_Test_Resource_Db::DEFAULT_COPYRIGHT, 
            'site_title'                    => Omeka_Test_Resource_Db::DEFAULT_SITE_TITLE, 
            'author'                        => Omeka_Test_Resource_Db::DEFAULT_AUTHOR, 
            'description'                   => Omeka_Test_Resource_Db::DEFAULT_DESCRIPTION, 
            'thumbnail_constraint'          => Omeka_Form_Install::DEFAULT_THUMBNAIL_CONSTRAINT, 
            'square_thumbnail_constraint'   => Omeka_Form_Install::DEFAULT_SQUARE_THUMBNAIL_CONSTRAINT, 
            'fullsize_constraint'           => Omeka_Form_Install::DEFAULT_FULLSIZE_CONSTRAINT, 
            'per_page_admin'                => Omeka_Form_Install::DEFAULT_PER_PAGE_ADMIN, 
            'per_page_public'               => Omeka_Form_Install::DEFAULT_PER_PAGE_PUBLIC, 
            'show_empty_elements'           => Omeka_Form_Install::DEFAULT_SHOW_EMPTY_ELEMENTS,
            'path_to_convert'               => '',
            'tag_delimiter'                 => ',',
        ));
        $installer->setForm($this->form);
        $installer->install();
        $this->profilerHelper->assertDbQuery("CREATE TABLE IF NOT EXISTS `omeka_collections`",
            "Installer should have created the default Omeka schema.");
        $this->profilerHelper->assertDbQuery("CREATE TABLE IF NOT EXISTS `omeka_schema_migrations`",
            "Installer should have created the timestamp migrations table.");
        $this->profilerHelper->assertDbQuery("INSERT INTO `omeka_users`",
            "Installer should have created a new user.");
        $this->profilerHelper->assertDbQuery(array(
            "INSERT INTO `omeka_options`",
            array(1=>'site_title', 
                  2=>'Automated Test Installation', 
                  3=>'site_title', 
                  4=>'Automated Test Installation')
        ), "Installer should have added appropriate database options.");    
    }
    
    public function testIsInstalled()
    {
        $installer = new Installer_Default($this->db);
        $this->assertFalse($installer->isInstalled());
        $this->dbAdapter->appendStatementToStack(Zend_Test_DbStatement::createSelectStatement(array(
            'omeka_options'
        )));
        $this->assertTrue($installer->isInstalled());
        $this->profilerHelper->assertDbQuery("SHOW TABLES LIKE 'omeka_options'");
    }
}
