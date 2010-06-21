<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * 
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2010
 **/
class Omeka_Controller_UpgradeControllerTest extends Omeka_Test_AppTestCase
{
    protected $_isAdminTest = true;
    
    public function assertPreConditions()
    {
        // No one is logged in.
        $this->assertFalse($this->core->getBootstrap()->currentUser);
    }
    
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