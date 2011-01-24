<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 *
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2010
 */
class Omeka_Job_Worker_Beanstalk
{
    /**
     * The maximum time (in seconds) that the MySQL connection can be kept
     * valid for this beanstalk worker instance.
     */
    const MYSQL_TIMEOUT = 2147483;

    public function __construct(Pheanstalk $pheanstalk,
                                Omeka_Job_Factory $jobFactory,
                                Omeka_Db $db
    ) {
        $this->_pheanstalk = $pheanstalk;
        $this->_jobFactory = $jobFactory;
        $this->_db = $db;
    }

    public function work()
    {
        try {
            // Setting wait_timeout to its maximum should prevent the majority
            // of "MySQL server has gone away" timeout errors.
            $this->_db->query("SET SESSION wait_timeout=" . self::MYSQL_TIMEOUT);
            $pheanJob = $this->_pheanstalk->reserve();
            if (!$pheanJob) {
                // Timeouts can occur when reserving a job, so this must be taken 
                // into account.  No cause for alarm.
                return;
            }
            $omekaJob = $this->_jobFactory->from($pheanJob->getData());
            if (!$omekaJob) {
                throw new UnexpectedValueException(
                    "Job factory returned null (should never happen)."
                );
            }
            $omekaJob->perform();
	        $this->_pheanstalk->delete($pheanJob);
        } catch (Zend_Db_Exception $e) {
            // Bury any jobs with database problems aside from stale 
            // connections, which should indicate to try the job a second time.
            if (strpos($e->getMessage(), 'MySQL server has gone away') === false) {
                $this->_pheanstalk->bury($pheanJob);
            }
            throw $e;
        } catch (Omeka_Job_Worker_InterruptException $e) {
            $this->_interrupt($omekaJob);
            throw $e;
        } catch (Exception $e) {
            $this->_pheanstalk->bury($pheanJob);
            throw $e;
        }
    }

    private function _interrupt($job = null)
    {
        // Just in case there was a transaction.
        $this->_db->rollback();
        $this->_db->closeConnection();
    }
}
