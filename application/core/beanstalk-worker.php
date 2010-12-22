<?php
/**
 * Bootstrap file for background processes.
 * 
 * @access private
 * @version $Id$
 * @package Omeka
 * @copyright Copyright (c) 2009 Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Paths.php is required at minimum in order to define all path constants.
 */
require_once dirname(__FILE__) . "/../../paths.php";
require_once "Omeka/Core.php";

// Set the command line arguments.
$options = new Zend_Console_Getopt(array('queue|q=s' => 'Name of queue (tube) to use', 'host|h=s' => 'Beanstalk host IP'));

try {
    $options->parse();
} catch (Zend_Console_Getopt_Exception $e) {
    echo $e->getUsageMessage();
    exit;
}

// Load only the phases needed.
$core = new Omeka_Core;
$core->bootstrap(array('Config', 'Db', 'Options', 'Jobs'));
$host = isset($options->host) ? $options->host : '127.0.0.1';
$pheanstalk = new Pheanstalk($host);
if (isset($options->queue) && $options->queue != 'default') {
    $pheanstalk->watch($options->queue)
               ->ignore('default');
}
$job = $pheanstalk->reserve();
$task = Zend_Registry::get('job_factory')->from($job->getData());
if ($task === false) {
    echo "Task failed.  Faulty input:\n" . $job->getData();
    $pheanstalk->bury($job);
    exit(1);
} else {
    $task->perform();
    $pheanstalk->delete($job);
    exit(0);
}
