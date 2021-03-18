<?php

class Omeka_Job_Dispatcher_Adapter_BeanstalkTest extends Omeka_Test_TestCase
{
    public function testPheanstalkRequiresHostOption()
    {
        $this->adapter = new Omeka_Job_Dispatcher_Adapter_Beanstalk;
        try {
            $this->adapter->setQueueName('foobar');
            $this->fail("No exception was thrown.");
        } catch (Omeka_Job_Dispatcher_Adapter_RequiredOptionException $e) {
            $this->assertStringContainsString("host", $e->getMessage());
            return;
        }
    }

    /**
     * Test Pheanstalk is wired up because it will throw an exception when 
     * given an invalid host.
     */
    public function testSetQueueNameWithInvalidHost()
    {
        $this->setExpectedException('Pheanstalk_Exception_ConnectionException');
        $this->adapter = new Omeka_Job_Dispatcher_Adapter_Beanstalk(array('host' => 'example.test'));
        $this->adapter->setQueueName('foobar');
    }

    /**
     * Test Pheanstalk is wired up because it will throw an exception when 
     * given an invalid host.
     */
    public function testSendWithInvalidHost()
    {
        $this->setExpectedException('Pheanstalk_Exception_ConnectionException');
        $this->adapter = new Omeka_Job_Dispatcher_Adapter_Beanstalk(array('host' => 'example.test'));
        $this->adapter->send('foobar', array());
    }
}
