<?php
class Omeka_Job_Worker_BeanstalkTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->pheanstalk = $this->getMock('Pheanstalk_Pheanstalk', array(), array('0.0.0.0'));
        $this->jobFactory = $this->getMock('Omeka_Job_Factory');
        $this->dbAdapter = $this->getMock('Zend_Test_DbAdapter');
        $this->db = new Omeka_Db($this->dbAdapter);
        $this->pheanJob = new Pheanstalk_Job(1, 'foo');
        $this->omekaJob = $this->getMock('Omeka_Job_JobInterface', array(), array(array()));
        $this->worker = new Omeka_Job_Worker_Beanstalk($this->pheanstalk, 
            $this->jobFactory, $this->db);
    }

    public function testWorkerReservesAndDecodesJob()
    {
        $this->_expectDecode();
        $this->worker->work($this->pheanJob);
    }

    public function testWorkerPerformsJob()
    {
        $this->_expectDecode();
        $this->_expectPerform();
        $this->worker->work($this->pheanJob);
    }

    public function testFinishedJobsGetDeleted()
    {
        $this->_expectDecode();
        $this->pheanstalk->expects($this->once())
            ->method('delete')
            ->with($this->pheanJob);
        $this->worker->work($this->pheanJob);
    }

    /**
     * @expectedException UnderflowException
     */
    public function testUnhandledExceptionBuriesJob()
    {
        $this->_expectDecode();
        $this->omekaJob->expects($this->once())
            ->method('perform')
            ->will($this->throwException(new UnderflowException()));
        $this->pheanstalk->expects($this->once())
            ->method('bury')
            ->with($this->pheanJob);
        $this->worker->work($this->pheanJob);
    }

    /**
     * @expectedException Omeka_Job_Worker_InterruptException
     */
    public function testInterruptDoesNotBury()
    {
        $this->_expectDecode();
        $this->omekaJob->expects($this->once())
            ->method('perform')
            ->will($this->throwException(new Omeka_Job_Worker_InterruptException()));
        $this->pheanstalk->expects($this->never())
            ->method('bury');
        $this->worker->work($this->pheanJob);
    }

    /**
     * @expectedException Omeka_Job_Worker_InterruptException
     */
    public function testRollbackOnInterrupt()
    {
        $this->_expectDecode();
        $this->omekaJob->expects($this->once())
            ->method('perform')
            ->will($this->throwException(new Omeka_Job_Worker_InterruptException()));
        $this->dbAdapter->expects($this->once())
            ->method('rollback');
        $this->worker->work($this->pheanJob);
    }

    /**
     * @expectedException Omeka_Job_Worker_InterruptException
     */
    public function testInterruptClosesDbConnection()
    {
        $this->_expectDecode();
        $this->omekaJob->expects($this->once())
            ->method('perform')
            ->will($this->throwException(new Omeka_Job_Worker_InterruptException()));
        $this->dbAdapter->expects($this->once())
            ->method('closeConnection');
        $this->worker->work($this->pheanJob);
    }

    public static function exceptionReleases()
    {
        return array(
            array('Omeka_Job_Worker_InterruptException', true),
            array('LogicException', false),
            array('Zend_Db_Exception', false),
        );
    }

    /**
     * @dataProvider exceptionReleases
     */
    public function testJobReleasedIfNotDeletedOrBuried($exceptionClass, $isReleased)
    {
        $this->_expectDecode();
        $this->omekaJob->expects($this->once())
            ->method('perform')
            ->will($this->throwException(new $exceptionClass()));
        $expectMethod = $isReleased ? 'once' : 'never';
        if ($isReleased) {
            $this->pheanstalk->expects($this->once())
                ->method('release')
                ->with($this->pheanJob);
        } else {
            $this->pheanstalk->expects($this->never())
                ->method('release');
        }
        try {
            $this->worker->work($this->pheanJob);
            $this->fail("Worker should have thrown an exception.");
        } catch (Exception $e) {
            $this->assertInstanceOf($exceptionClass, $e);
        }
    }

    /**
     * @expectedException Zend_Db_Statement_Mysqli_Exception
     */
    public function testServerGoneAwayDoesNotBury()
    {
        $this->_expectDecode();
        $this->omekaJob->expects($this->once())
            ->method('perform')
            ->will($this->throwException(new Zend_Db_Statement_Mysqli_Exception("Mysqli prepare error: MySQL server has gone away")));
        $this->pheanstalk->expects($this->never())
            ->method('bury');
        $this->worker->work($this->pheanJob);
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
