<?php
class Omeka_Job_DispatcherTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->user = $this->getMock('User', array(), array(), '', false);
        $this->adapter = new Omeka_Job_Dispatcher_Mock;
        $this->dispatcher = new Omeka_Job_Dispatcher($this->adapter, $this->user);
    }

    public function testSetQueueName()
    {
        $this->dispatcher->setQueueName('foobar');
        $this->assertEquals('foobar', $this->adapter->queueName);
    }

    public function testSendsJob()
    {
        $this->dispatcher->send('Omeka_Job_Mock');
        $this->assertTrue(isset($this->adapter->encodedJob));
    }

    public function testJobIsEncoded()
    {
        $this->dispatcher->send('Omeka_Job_Mock');
        $this->assertType('array', Zend_Json::decode($this->adapter->encodedJob));
    }

    public function testJobMetadataIsSent()
    {
        $this->dispatcher->send('Omeka_Job_Mock');
        $this->assertNotNull($this->adapter->metadata);
    }

    public static function metadataKeys()
    {
        return array(
            array('className'),
            array('options'),
            array('createdAt'),
            array('createdBy')
        );
    }

    /**
     * @dataProvider metadataKeys
     */
    public function testJobMetadataKeys($key)
    {
        $this->dispatcher->send('Omeka_Job_Mock');
        $this->assertTrue(array_key_exists($key, $this->adapter->metadata));
    }

    /**
     * @depends testJobMetadataKeys
     */
    public function testCreatedBy()
    {
        $this->user->id = 1;
        $this->dispatcher->send('Omeka_Job_Mock');
        $this->assertEquals($this->user->id, $this->adapter->metadata['createdBy']->id);
    }

    /**
     * @depends testJobMetadataKeys
     */
    public function testOptions()
    {
        $this->dispatcher->send('Omeka_Job_Mock', array('foobar' => true));
        $this->assertEquals(array('foobar' => true), $this->adapter->metadata['options']);
    }
}

class Omeka_Job_Dispatcher_Mock implements Omeka_Job_Dispatcher_Adapter
{
    public function setQueueName($name)
    {
        $this->queueName = $name;
    }

    public function send($encodedJob, array $metadata)
    {
        $this->encodedJob = $encodedJob;
        $this->metadata = $metadata;
    }
}
