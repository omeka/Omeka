<?php

class Omeka_Job_Dispatcher_Adapter_BeanstalkTest extends PHPUnit_Framework_TestCase
{
    public function testPheanstalkRequiresHostOption()
    {
        $this->adapter = new Omeka_Job_Dispatcher_Adapter_Beanstalk;
        try {
            $this->adapter->setQueueName('foobar');
            $this->fail("No exception was thrown.");
        } catch (Omeka_Job_Dispatcher_Adapter_RequiredOptionException $e) {
            $this->assertContains("host", $e->getMessage());
            return;
        }
    }

    /**
     * Test Pheanstalk is wired up because it will throw an exception when 
     * given an invalid host. 
     */
    public function testSetQueueName()
    {
        $this->adapter = new Omeka_Job_Dispatcher_Adapter_Beanstalk(array('host' => 'example.test'));
        try {
            $this->adapter->setQueueName('foobar');
            $this->fail("No exception was thrown.");
        } catch (Pheanstalk_Exception_ConnectionException $e) {
        }
    }

    public function testSend()
    {
        $this->adapter = new Omeka_Job_Dispatcher_Adapter_Beanstalk(array('host' => 'example.test'));
        try {
            $this->adapter->send('foobar', array());
            $this->fail("No exception was thrown.");
        } catch (Pheanstalk_Exception_ConnectionException $e) {
        }
    }
}
