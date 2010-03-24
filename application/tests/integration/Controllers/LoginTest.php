<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * Test logins.
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2007-2010
 **/
class Omeka_Controller_LoginTest extends Omeka_Test_AppTestCase
{
    
    public function setUp()
    {
        parent::setUp();
        
        // Need to use admin view scripts for login b/c shared login view not
        // available until 2.0.
        $this->view = Zend_Registry::get('view');
        $this->view->addScriptPath(ADMIN_THEME_DIR . DIRECTORY_SEPARATOR . 'default');
        // And this stupid bullshit is needed so the CSS files load correctly.
        // $this->view->addAssetPath
    }
    
    public function testValidLogin()
    {
        $r = $this->getRequest();
        $r->setPost(array('username'=>'foobar123', 'password'=>'foobar123'))
          ->setMethod('post');
          
        $this->dispatch('users/login', true);    
        $this->assertRedirectTo('/');
    }
    
    public function testInvalidLogin()
    {
        $r = $this->getRequest();
        $r->setPost(array('username'=>'foo', 'password'=>'bar'))
          ->setMethod('post');
         
        $this->dispatch('users/login', true);
        $this->assertNotRedirect();
        $this->assertContains("Username could not be found.", $this->getResponse()->sendResponse());
    }
    
    // public function testUpgradingHashedPasswordForUser()
    // {
    //     // Replace the existing 'users' table with the older table schema for
    //     // purposes of testing the upgrade.
    //     $omekaDb = $this->core->getBootstrap()->getResource('Db');
    //     assert('$omekaDb instanceof Omeka_Db');
    //     $dbAdapter = $omekaDb->getAdapter();
    //     assert('$dbAdapter instanceof Zend_Db_Adapter_Mysqli');
    //     $dbHelper = new Omeka_Test_DbHelper($dbAdapter);
    //     $dbHelper->dropTables(array('omeka_users'));
    //     $oldTableFixture = dirname(__FILE__) . '/_files/pre-salt-upgrade.sql';
    //     $this->assertFileExists($oldTableFixture);
    //     $dbHelper->loadDbSchema($oldTableFixture);
    //     
    //     
    // }
}
