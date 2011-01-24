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

    public function __construct(Zend_Console_GetOpt $options,
                                Omeka_Job_Factory $jobFactory,
                                Omeka_Db $db
    ) {
        $host = isset($options->host) ? $options->host : '127.0.0.1';
        $pheanstalk = new Pheanstalk($host);
        if (isset($options->queue) && $options->queue != 'default') {
            $pheanstalk->watch($options->queue)
                       ->ignore('default');
        }
        $this->_pheanstalk = $pheanstalk;
        $this->_jobFactory = $jobFactory;
        $this->_db = $db;
    }

    public function work()
    {
        // Setting wait_timeout to its maximum should prevent the majority
        // of "MySQL server has gone away" timeout errors.
        $this->_db->query("SET SESSION wait_timeout=" . self::MYSQL_TIMEOUT);
        $pheanJob = $this->_pheanstalk->reserve();
        try {
            $omekaJob = $this->_jobFactory->from($pheanJob->getData());
            $omekaJob->perform();
	    $this->_pheanstalk->delete($pheanJob);
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
