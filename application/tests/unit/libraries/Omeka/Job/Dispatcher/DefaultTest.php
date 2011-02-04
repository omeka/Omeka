<?php
class Omeka_Job_Dispatcher_DefaultTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->user = $this->getMock('User', array(), array(), '', false);
        $this->adapter = new Omeka_Job_Dispatcher_Adapter_Array();
        $this->dispatcher = new Omeka_Job_Dispatcher_Default($this->adapter, $this->user);
    }

    public function testSendsJob()
    {
        $this->dispatcher->send('Omeka_Job_Mock');
        $this->assertNotNull($this->adapter->getJob());
    }

    public function testSetQueueName()
    {
        $this->dispatcher->setQueueName('foobar');
        $this->dispatcher->send('Omeka_Job_Mock');
        $job = $this->adapter->getJob();
        $this->assertEquals('foobar', $job['queue']);
    }

    public function testJobIsEncoded()
    {
        $this->dispatcher->send('Omeka_Job_Mock');
        $job = $this->adapter->getJob();
        $this->assertInternalType('array', Zend_Json::decode($job['encoded']));
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
        $job = $this->adapter->getJob();
        $this->assertTrue(array_key_exists($key, $job['metadata']));
    }

    /**
     * @depends testJobMetadataKeys
     */
    public function testCreatedBy()
    {
        $this->user->id = 1;
        $this->dispatcher->send('Omeka_Job_Mock');
        $job = $this->adapter->getJob();
        $this->assertEquals($this->user->id, $job['metadata']['createdBy']->id);
    }

    /**
     * @depends testJobMetadataKeys
     */
    public function testOptions()
    {
        $this->dispatcher->send('Omeka_Job_Mock', array('foobar' => true));
        $job = $this->adapter->getJob();
        $this->assertEquals(array('foobar' => true), $job['metadata']['options']);
    }
}
