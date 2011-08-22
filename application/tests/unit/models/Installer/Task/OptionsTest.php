<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * 
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Installer_Task_OptionsTest extends PHPUnit_Framework_TestCase
{
    const DB_PREFIX = 'test_';
    
    public function setUp()
    {
        $this->dbAdapter = new Zend_Test_DbAdapter;
        $this->db = new Omeka_Db($this->dbAdapter, self::DB_PREFIX);
        $this->profilerHelper = new Omeka_Test_Helper_DbProfiler($this->dbAdapter->getProfiler(),
            $this);
    }
    
    public function testThrowsExceptionForMissingOptions()
    {
        $task = new Installer_Task_Options;
        $task->setOptions(array(
            'foobar' => 'This option is fake',
            'site_title' => 'This option exists'
        ));
        try {
            $task->install($this->db);
            $this->fail("Should have thrown an exception when missing specific options.");
        } catch (Installer_Task_Exception $e) {
            $this->assertContains("Missing the following options", $e->getMessage());
            $this->assertContains("copyright", $e->getMessage());
            $this->assertNotContains("site_title", $e->getMessage());
        }
    }
    
    public function testThrowsExceptionForUnexpectedOptions()
    {
        $task = new Installer_Task_Options;
        $task->setOptions(array(
            'fake_option'                   => 'This option does not exist.',
            'administrator_email'           => 'foobar', 
            'copyright'                     => 'foobar', 
            'site_title'                    => 'foobar', 
            'author'                        => 'foobar', 
            'description'                   => 'foobar', 
            'thumbnail_constraint'          => 'foobar', 
            'square_thumbnail_constraint'   => 'foobar', 
            'fullsize_constraint'           => 'foobar', 
            'per_page_admin'                => 'foobar', 
            'per_page_public'               => 'foobar', 
            'show_empty_elements'           => 'foobar',
            'path_to_convert'               => 'foobar',
            Theme::ADMIN_THEME_OPTION       => Installer_Default::DEFAULT_ADMIN_THEME,
            Theme::PUBLIC_THEME_OPTION      => Installer_Default::DEFAULT_PUBLIC_THEME,
            Omeka_Validate_File_Extension::WHITELIST_OPTION => Omeka_Validate_File_Extension::DEFAULT_WHITELIST,
            Omeka_Validate_File_MimeType::WHITELIST_OPTION  => Omeka_Validate_File_MimeType::DEFAULT_WHITELIST,
            File::DISABLE_DEFAULT_VALIDATION_OPTION         => (string)!extension_loaded('fileinfo'),
            Omeka_Db_Migration_Manager::VERSION_OPTION_NAME => OMEKA_VERSION,
            'display_system_info'           => true,
            'tag_delimiter'                 => ',',
        ));
        try {
            $task->install($this->db);
            $this->fail("Should have thrown an exception when unknown options were given.");
        } catch (Installer_Task_Exception $e) {
            $this->assertContains("Unknown options given:", $e->getMessage());
            $this->assertContains("fake_option", $e->getMessage());
            $this->assertNotContains("path_to_convert", $e->getMessage());
        }
    }
    
    public function testInsertsOptions()
    {
        $task = new Installer_Task_Options();
        $task->setOptions(array(
            'administrator_email'           => 'foobar', 
            'copyright'                     => 'foobar', 
            'site_title'                    => 'foobar', 
            'author'                        => 'foobar', 
            'description'                   => 'foobar', 
            'thumbnail_constraint'          => 'foobar', 
            'square_thumbnail_constraint'   => 'foobar', 
            'fullsize_constraint'           => 'foobar', 
            'per_page_admin'                => 'foobar', 
            'per_page_public'               => 'foobar', 
            'show_empty_elements'           => 'foobar',
            'path_to_convert'               => 'foobar',
            Theme::ADMIN_THEME_OPTION       => Installer_Default::DEFAULT_ADMIN_THEME,
            Theme::PUBLIC_THEME_OPTION      => Installer_Default::DEFAULT_PUBLIC_THEME,
            Omeka_Validate_File_Extension::WHITELIST_OPTION => Omeka_Validate_File_Extension::DEFAULT_WHITELIST,
            Omeka_Validate_File_MimeType::WHITELIST_OPTION  => Omeka_Validate_File_MimeType::DEFAULT_WHITELIST,
            File::DISABLE_DEFAULT_VALIDATION_OPTION         => (string)!extension_loaded('fileinfo'),
            Omeka_Db_Migration_Manager::VERSION_OPTION_NAME => OMEKA_VERSION,
            'display_system_info'           => true,
            'tag_delimiter'                 => ',',
        ));
        $task->install($this->db);
        $this->profilerHelper->assertDbQuery(array(
            "INSERT INTO `test_options`",
            array(
                1 => File::DISABLE_DEFAULT_VALIDATION_OPTION,
                2 => (string)!extension_loaded('fileinfo'),
                3 => File::DISABLE_DEFAULT_VALIDATION_OPTION,
                4 => (string)!extension_loaded('fileinfo')
            )
        ), "Should have inserted options into the database.");
    }
}
