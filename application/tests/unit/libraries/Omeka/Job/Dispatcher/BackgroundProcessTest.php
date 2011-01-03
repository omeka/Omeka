<?php
class Omeka_Job_Dispatcher_Adapter_BackgroundProcessTest extends PHPUnit_Framework_TestCase
{
    public $jobMetadata = array(
        'createdAt' => 'now',
        'createdBy' => 1,
        'className' => 'foobar',
        'options'   => array('baz' => true),
    );

    public function setUp()
    {
        // The ProcessDispatcher is quite untestable so this test just calls 
        // a mock instead.
        $this->processDispatcher = new MockProcessDispatcher;
        MockProcessDispatcher::_reset();
        $this->adapter = new Omeka_Job_Dispatcher_Adapter_BackgroundProcess;
    }

    public function testDefaultUsesProcessDispatcher()
    {
        $this->assertEquals("ProcessDispatcher", get_class($this->adapter->getProcessDispatcher()));
    }

    public function testSendStartsProcess()
    {
        $this->adapter->setProcessDispatcher($this->processDispatcher);
        $this->adapter->send('foobar', $this->jobMetadata);
        $this->assertTrue(MockProcessDispatcher::$processStarted);
    }

    public function testSendArgs()
    {
        $this->adapter->setProcessDispatcher($this->processDispatcher);
        $this->adapter->send('foobar', $this->jobMetadata);
        $this->assertEquals(array('job' => 'foobar'), MockProcessDispatcher::$args);
        $this->assertEquals('Omeka_Job_ProcessWrapper', MockProcessDispatcher::$processClass);
        $this->assertEquals(1, MockProcessDispatcher::$user);
    }
}

class MockProcessDispatcher extends ProcessDispatcher
{
    public static $processStarted;
    public static $processClass;
    public static $user;
    public static $args;

    public static function startProcess($processClass, $user, $args)
    {
        self::$processStarted = true;
        self::$processClass = $processClass;
        self::$user = $user;
        self::$args = $args;
    }

    public static function _reset()
    {
        self::$processStarted = null;
        self::$processClass = null;
        self::$user = null;
        self::$args = null;
    }
}
