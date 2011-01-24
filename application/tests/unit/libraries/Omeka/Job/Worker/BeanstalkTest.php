<?php
class Omeka_Job_Worker_BeanstalkTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->pheanstalk = $this->getMock('Pheanstalk', array(), array('0.0.0.0'));
        $this->jobFactory = $this->getMock('Omeka_Job_Factory');
        $this->dbAdapter = $this->getMock('Zend_Test_DbAdapter');
        $this->db = new Omeka_Db($this->dbAdapter);
        $this->pheanJob = new Pheanstalk_Job(1, 'foo');
        $this->omekaJob = $this->getMock('Omeka_Job', array(), array(array()));
        $this->worker = new Omeka_Job_Worker_Beanstalk($this->pheanstalk, 
            $this->jobFactory, $this->db);
    }

    public function testWorkerReservesAndDecodesJob()
    {
        $this->_expectReserve();
        $this->_expectDecode();
        $this->worker->work();
    }

    public function testWorkerPerformsJob()
    {
        $this->_expectReserve();
        $this->_expectDecode();
        $this->_expectPerform();
        $this->worker->work();
    }

    public function testFinishedJobsGetDeleted()
    {
        $this->_expectReserve();
        $this->_expectDecode();
        $this->pheanstalk->expects($this->once())
            ->method('delete')
            ->with($this->pheanJob);
        $this->worker->work();
    }

    /**
     * @expectedException UnderflowException
     */
    public function testUnhandledExceptionBuriesJob()
    {
        $this->_expectReserve();
        $this->_expectDecode();
        $this->omekaJob->expects($this->once())
            ->method('perform')
            ->will($this->throwException(new UnderflowException()));
        $this->pheanstalk->expects($this->once())
            ->method('bury')
            ->with($this->pheanJob);
        $this->worker->work();
    }

    /**
     * @expectedException Omeka_Job_Worker_InterruptException
     */
    public function testInterruptDoesNotBury()
    {
        $this->_expectReserve();
        $this->_expectDecode();
        $this->omekaJob->expects($this->once())
            ->method('perform')
            ->will($this->throwException(new Omeka_Job_Worker_InterruptException()));
        $this->pheanstalk->expects($this->never())
            ->method('bury');
        $this->worker->work();
    }

    /**
     * @expectedException Omeka_Job_Worker_InterruptException
     */
    public function testRollbackOnInterrupt()
    {
        $this->_expectReserve();
        $this->_expectDecode();
        $this->omekaJob->expects($this->once())
            ->method('perform')
            ->will($this->throwException(new Omeka_Job_Worker_InterruptException()));
        $this->dbAdapter->expects($this->once())
            ->method('rollback');
        $this->worker->work();
    }

    /**
     * @expectedException Omeka_Job_Worker_InterruptException
     */
    public function testInterruptClosesDbConnection()
    {
        $this->_expectReserve();
        $this->_expectDecode();
        $this->omekaJob->expects($this->once())
            ->method('perform')
            ->will($this->throwException(new Omeka_Job_Worker_InterruptException()));
        $this->dbAdapter->expects($this->once())
            ->method('closeConnection');
        $this->worker->work();
    }

    /**
     * @expectedException Zend_Db_Statement_Mysqli_Exception
     */
    public function testServerGoneAwayDoesNotBury()
    {
        $this->_expectReserve();
        $this->_expectDecode();
        $this->omekaJob->expects($this->once())
            ->method('perform')
            ->will($this->throwException(new Zend_Db_Statement_Mysqli_Exception("Mysqli prepare error: MySQL server has gone away")));
        $this->pheanstalk->expects($this->never())
            ->method('bury');
        $this->worker->work();
    }

    private function _expectReserve()
    {
        $this->pheanstalk->expects($this->once())
            ->method('reserve')
            ->will($this->returnValue($this->pheanJob));
    }

    private function _expectDecode($input = 'foo', $output = null)
    {
        if (!$output) {
            $output = $this->omekaJob;
        }
        $this->jobFactory->expects($this->once())
            ->method('from')
            ->with($input)
            ->will($this->returnValue($output));
    }

    private function _expectPerform()
    {
        $this->omekaJob->expects($this->once())
            ->method('perform');
    }
}
