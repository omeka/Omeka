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
    
    // public function setUp()
    // {
    //     parent::setUp();
    // }
    // 
    // public function setUpBootstrap($bootstrap)
    // {
    //     var_dump($bootstrap->frontcontroller);exit;
    // }
    
    public function assertPreConditions()
    {
        $this->assertEquals(get_option('migration'), OMEKA_MIGRATION);
    }
    
    public function testAutomaticRedirectToUpgrade()
    {
        set_option('migration', (int)get_option('migration') + 1);
        
        $this->dispatch('/', true);
    }
    
    public function testCannotUpgradeWhenDatabaseIsUpToDate()
    {
        // var_dump('fooo');exit;
    }
}