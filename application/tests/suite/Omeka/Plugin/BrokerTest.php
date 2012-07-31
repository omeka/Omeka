<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * 
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2009
 */
class Omeka_Plugin_BrokerTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->broker = new Omeka_Plugin_Broker;
        $this->testHooksFired = array();
        $this->broker->addHook('initialize', array($this, 'hookImpl1'), 'foobar');
        $this->broker->addHook('initialize', array($this, 'hookImpl2'), 'bazfoo');
    }
    
    public function testHookStorage()
    {
        $this->assertEquals(array($this, 'hookImpl1'), $this->broker->getHook('foobar', 'initialize'));
    }
        
    public function testHookStorageViaAddPluginHookFunction()
    {
        $this->broker->register();
        $this->broker->setCurrentPluginDirName('fake-plugin-name');
        add_plugin_hook('initialize', 'fake_plugin_initialize');
        // Using the registry should not interfere with the other unit tests.
        Zend_Registry::_unsetInstance();
        $this->assertEquals('fake_plugin_initialize', $this->broker->getHook('fake-plugin-name', 'initialize'));
    }    
    
    public function testCallSingleHook()
    {
        $this->broker->callHook('initialize', array('special info', '2nd argument'), 'foobar');
        // Only one of the two possible hooks should have be called.
        $this->assertEquals(1, count($this->testHooksFired));
        
        // See if the hook implementation was correctly passed the arguments from
        // callHook().
        $this->assertEquals('special info', $this->testHooksFired['hook1']);
    }
        
    public function testCallSetOfHooks()
    {
        $this->assertEquals(0, count($this->testHooksFired));
        $this->broker->callHook('initialize', array('special info', '2nd argument'));
        $this->assertEquals(2, count($this->testHooksFired));
        // Make sure that arguments were passed correctly to both of these hook
        // implementations.
        $this->assertEquals('special info', $this->testHooksFired['hook1']);
        $this->assertEquals('2nd argument', $this->testHooksFired['hook2']);
    }
    
    public function hookImpl1($arg1, $arg2)
    {
        $this->testHooksFired['hook1'] = $arg1;
    }
    
    public function hookImpl2($arg1, $arg2)
    {
        $this->testHooksFired['hook2'] = $arg2;
    }
}
