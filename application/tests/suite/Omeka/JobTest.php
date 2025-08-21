<?php
class Omeka_JobTest extends Omeka_Test_TestCase
{
    private $db;
    private $dispatcher;

    public function setUpLegacy()
    {
        $this->db = $this->getMock('Omeka_Db', [], [], '', false);
        $this->dispatcher = $this->getMock('Omeka_Job_Dispatcher_DispatcherInterface', [], [], '', false);
    }

    public function testSetters()
    {
        $job = new Omeka_Job_Mock([]);
        $job->setJobDispatcher($this->dispatcher);
        $job->setDb($this->db);
        $this->assertSame($this->db, $job->getDb());
        $this->assertSame($this->dispatcher, $job->getDispatcher());
    }

    public function testConstructorCallsSetters()
    {
        $job = new Omeka_Job_Mock([
            'db' => $this->db,
            'jobDispatcher' => $this->dispatcher,
        ]);
        $this->assertSame($this->db, $job->getDb());
        $this->assertSame($this->dispatcher, $job->getDispatcher());
    }

    public function testConstructorSetsAllOtherOptions()
    {
        $job = new Omeka_Job_Mock([
            'foobar' => true,
            'db' => $this->db,
        ]);
        $this->assertEquals(['foobar' => true], $job->getMiscOptions());
    }
}
