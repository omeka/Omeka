<?php
class Omeka_JobTest extends Omeka_Test_TestCase
{
    public function setUpLegacy()
    {
        $this->db = $this->getMock('Omeka_Db', array(), array(), '', false);
        $this->dispatcher = $this->getMock('Omeka_Job_Dispatcher_DispatcherInterface', array(), array(), '', false);
    }

    public function testSetters()
    {
        $job = new Omeka_Job_Mock(array());
        $job->setJobDispatcher($this->dispatcher);
        $job->setDb($this->db);
        $this->assertSame($this->db, $job->getDb());
        $this->assertSame($this->dispatcher, $job->getDispatcher());
    }

    public function testConstructorCallsSetters()
    {
        $job = new Omeka_Job_Mock(array(
            'db' => $this->db,
            'jobDispatcher' => $this->dispatcher,
        ));
        $this->assertSame($this->db, $job->getDb());
        $this->assertSame($this->dispatcher, $job->getDispatcher());
    }

    public function testConstructorSetsAllOtherOptions()
    {
        $job = new Omeka_Job_Mock(array(
            'foobar' => true,
            'db' => $this->db,
        ));
        $this->assertEquals(array('foobar' => true), $job->getMiscOptions());
    }
}
