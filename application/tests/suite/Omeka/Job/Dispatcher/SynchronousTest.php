<?php
/**
 * 
 */
class Omeka_Job_Dispatcher_Adapter_SynchronousTest extends Omeka_Test_TestCase
{
    private $factory;
    private $adapter;

    public function setUpLegacy()
    {
        Zend_Registry::_unsetInstance();
        $this->factory = new Omeka_Job_FactoryMock;
        Zend_Registry::set('job_factory', $this->factory);
        $this->adapter = new Omeka_Job_Dispatcher_Adapter_Synchronous;
    }

    public function testSend()
    {
        $job = $this->adapter->send('foobar', array());
        $this->assertTrue($job->performed);
    }

    public function testNoNamedQueues()
    {
        $this->assertFalse($this->adapter->setQueueName('foobar'));
    }

    public function tearDownLegacy()
    {
        Zend_Registry::_unsetInstance();
    }
}

class Omeka_Job_FactoryMock
{
    public $string;

    public function from($str)
    {
        $this->string = $str;
        return new Omeka_Job_Mock(array());
    }
}
