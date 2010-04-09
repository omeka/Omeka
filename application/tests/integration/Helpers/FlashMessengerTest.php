<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * 
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2007-2010
 **/
class Omeka_Controller_Action_Helper_FlashMessengerTest extends Omeka_Test_AppTestCase
{
    
    public function setUp()
    {
        parent::setUp();
        $this->messenger = new Omeka_Controller_Action_Helper_FlashMessenger;
    }
    
    public function testRetrieveFromNamespace()
    {
        $this->messenger->addMessage("First message to default namespace");
        $this->messenger->addMessage("Second message to error namespace", 'error');
        $this->messenger->addMessage("Third message to success namespace", 'success');
        
        // Messages are stored in the session until the next request.
        $this->messenger->loadFromSession();
        $this->assertEquals(array("First message to default namespace"), $this->messenger->getMessages('default'));
        $this->assertEquals(array("Second message to error namespace"), $this->messenger->getMessages('error'));
        $this->assertEquals(array("Third message to success namespace"), $this->messenger->getMessages('success'));
    }
    
    public function testHasMessagesInNamespace()
    {
        $this->assertFalse($this->messenger->hasMessages());
        $this->messenger->addMessage("Random message in foobar namespace", 'foobar');
        $this->messenger->loadFromSession();
        $this->assertTrue($this->messenger->hasMessages('foobar'));
    }
    
    public function testHasCurrentMessagesInNamespace()
    {
        $this->messenger->addMessage("Random message", 'foobar');
        $this->assertTrue($this->messenger->hasCurrentMessages('foobar'));
    }
    
    public function testGetCurrentMessagesInNamespace()
    {
        $this->messenger->addMessage("Random message", 'foobar');
        $this->assertEquals(array("Random message"), $this->messenger->getCurrentMessages('foobar'));
    }
    
    public function tearDown()
    {
        parent::tearDown();
        Omeka_Controller_Action_Helper_FlashMessenger::reset();
    }
}