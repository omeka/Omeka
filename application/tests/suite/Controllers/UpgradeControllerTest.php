<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2010-2011
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Tests for correct access to the upgrade controller.
 *
 * @package Omeka
 */
class Omeka_Controller_UpgradeControllerTest extends Omeka_Test_AppTestCase
{
    protected $_isAdminTest = true;
    
    public function testAutomaticRedirectToUpgrade()
    {
        set_option('omeka_version', '1.0');
        $this->db->query("TRUNCATE omeka_schema_migrations");
        
        $this->dispatch('/');
        $this->assertRedirectTo('/upgrade');
    }
    
    public function testCanReachUpgradePageWithoutBeingLoggedIn()
    {
        set_option('omeka_version', '1.0');
        $this->db->query("TRUNCATE omeka_schema_migrations");
        
        $this->dispatch('/upgrade');
        $this->assertNotRedirectTo('/users/login');
    }
    
    public function testCannotUpgradeWhenDatabaseIsUpToDate()
    {
        $this->dispatch('/upgrade');
    }
}
