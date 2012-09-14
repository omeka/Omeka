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
        $this->messenger->addMessage("First message to default namespace", 'default');
        $this->messenger->addMessage("Second message to error namespace", 'error');
        $this->messenger->addMessage("Third message to success namespace", 'success');
        
        $this->assertEquals(array(
            'default' => array('First message to default namespace'),
            'error' => array('Second message to error namespace'),
            'success' => array('Third message to success namespace')
            ), $this->messenger->getCurrentMessages());
    }
    
    public function testHasCurrentMessages()
    {
        $this->messenger->addMessage("Random message", 'foobar');
        $this->assertTrue($this->messenger->hasCurrentMessages('foobar'));
    }
}
