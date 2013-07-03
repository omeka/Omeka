<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * @package Omeka\Job\Worker
 */
class Omeka_Job_Worker_Beanstalk
{
    public function __construct(Pheanstalk_Pheanstalk $pheanstalk,
                                Omeka_Job_Factory $jobFactory,
                                Omeka_Db $db
    ) {
        $this->_pheanstalk = $pheanstalk;
        $this->_jobFactory = $jobFactory;
        $this->_db = $db;
    }

    public function work(Pheanstalk_Job $pJob)
    {
        try {
            $omekaJob = $this->_jobFactory->from($pJob->getData());
            if (!$omekaJob) {
                throw new UnexpectedValueException(
                    "Job factory returned null (should never happen)."
                );
            }

            if ($omekaJob instanceof Omeka_Job_AbstractJob) {
                $user = $omekaJob->getUser();
                if ($user) {
                    Zend_Registry::get('bootstrap')->getContainer()->currentuser = $user;
                }
            }

            $omekaJob->perform();
            $this->_pheanstalk->delete($pJob);
        } catch (Zend_Db_Exception $e) {
            // Bury any jobs with database problems aside from stale
            // connections, which should indicate to try the job a second time.
            if (strpos($e->getMessage(), 'MySQL server has gone away') === false) {
                $this->_pheanstalk->bury($pJob);
            } else {
                $this->_pheanstalk->release($pJob);
            }
            throw $e;
        } catch (Omeka_Job_Worker_InterruptException $e) {
            $this->_interrupt($omekaJob);
            $this->_pheanstalk->release($pJob);
            throw $e;
        } catch (Exception $e) {
            $this->_pheanstalk->bury($pJob);
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
