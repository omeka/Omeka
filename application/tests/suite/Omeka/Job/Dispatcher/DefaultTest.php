<?php
class Omeka_Job_Dispatcher_DefaultTest extends Omeka_Test_TestCase
{
    private $user;
    private $adapter;
    private $dispatcher;

    public function setUpLegacy()
    {
        $this->user = $this->getMock('User', [], [], '', false);
        $this->adapter = new Omeka_Job_Dispatcher_Adapter_Array();
        $this->dispatcher = new Omeka_Job_Dispatcher_Default($this->adapter, $this->adapter, $this->user);
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
        $this->assertTrue(is_array(Zend_Json::decode($job['encoded'])));
    }

    public static function metadataKeys()
    {
        return [
            ['className'],
            ['options'],
            ['createdAt'],
            ['createdBy']
        ];
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
        $this->dispatcher->send('Omeka_Job_Mock', ['foobar' => true]);
        $job = $this->adapter->getJob();
        $this->assertEquals(['foobar' => true], $job['metadata']['options']);
    }
}
