<?php
/**
 * 
 */
class Omeka_Job_Dispatcher_Adapter_SynchronousTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
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

    public function tearDown()
    {
        Zend_Registry::_unsetInstance();
    }
}

class Omeka_Job_FactoryMock 
{
    public function from($str)
    {
        $this->string = $str;
        return new Omeka_Job_Mock(array());
    }
}


