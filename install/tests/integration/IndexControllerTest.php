<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * 
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2009
 **/
class IndexControllerTest extends Zend_Test_PHPUnit_ControllerTestCase
{
    public function setUp()
    {
        $this->bootstrap = array($this, 'appBootstrap');
        parent::setUp();
    }
    
    public function appBootstrap()
    {
        // Include the install app's bootstrap.
        include '../index.php';
        $this->app = $application;
    }
    
    public function assertPreConditions()
    {
        // Make sure that we haven't initialized a database connection prior
        // to running these tests.
        // Fake a database connection to convince the installer that Omeka is already installed.
        $db = $this->app->getBootstrap()->getResource('db');
        $this->assertNull($db);
    }
    
    public function testOmekaIsAlreadyInstalled()
    {
        $db = $this->getMock('Omeka_Db', array(), array(), '', false);
        $db->prefix = 'omeka_';
        
        $db->expects($this->once())->method('query');
        Omeka_Context::getInstance()->setDb($db);
        Zend_Controller_Front::getInstance()->setParam('bootstrap', $this->app->getBootstrap());
        
        $this->dispatch('');
    }
    
    // public function testFatalError
}
