<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Job dispatcher for Beanstalk.
 *
 * Requires Pheanstalk library (Beanstalk client) in order to work properly.
 *
 * This adapter must be instantiated with the 'host' option (IP address of 
 * beanstalk daemon) in order to work properly.
 * 
 * @package Omeka\Job\Dispatcher\Adapter
 */
class Omeka_Job_Dispatcher_Adapter_Beanstalk extends Omeka_Job_Dispatcher_Adapter_AbstractAdapter
{
    private $_pheanstalk;

    /**
     * Because of the potential for long-running imports (and the fact that 
     * jobs are not idemopotent), TTR should be pretty high by default.
     */
    const DEFAULT_TTR = 3600;

    /**
     * Beanstalk understands the concept of 'tubes' instead of named queues, so 
     * set the appropriate 'tube' to dispatch jobs.
     *
     * @param string $name
     */
    public function setQueueName($name)
    {
        return $this->_pheanstalk()->useTube($name);
    }

    public function send($encodedJob, array $metadata)
    {
        return $this->_pheanstalk()->put(
            $encodedJob,
            Pheanstalk_Pheanstalk::DEFAULT_PRIORITY,
            Pheanstalk_Pheanstalk::DEFAULT_DELAY,
            $this->getOption('ttr')
        );
    }

    private function _pheanstalk()
    {
        if (!$this->_pheanstalk) {
            $this->_pheanstalk = new Pheanstalk_Pheanstalk($this->getOption('host'));
        }
        return $this->_pheanstalk;
    }

    public function getOption($name)
    {
        switch ($name) {
            case 'ttr':
                if ($this->hasOption('ttr')) {
                    return parent::getOption('ttr');
                } else {
                    return self::DEFAULT_TTR;
                }
        }
        return parent::getOption($name);
    }
}
