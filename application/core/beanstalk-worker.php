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

declare(ticks = 1);

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

function handle_exception($e)
{
    echo "Beanstalk worker error (" . get_class($e) . "): " . $e->getMessage() . 
         PHP_EOL . $e->getTraceAsString() . PHP_EOL;
    exit(1);
}
set_exception_handler('handle_exception');

function handle_signal($signal)
{
    switch ($signal) {
        case SIGINT:
            throw new Omeka_Job_Worker_InterruptException("Caught SIGINT, shutting down."); 
            break;
        default:
            break;
    }    
}
pcntl_signal(SIGINT, "handle_signal");


$core->bootstrap(array('Autoloader'));
$host = isset($options->host) ? $options->host : '127.0.0.1';
$pheanstalk = new Pheanstalk($host);
if (isset($options->queue) && $options->queue != 'default') {
    $pheanstalk->watch($options->queue)
               ->ignore('default');
}
// Reserving a job BEFORE bootstrapping the database will ensure that there are 
// never any MySQL timeout issues and help prevent any number of other database 
// usage-related problems.
$pheanJob = $pheanstalk->reserve();
if (!$pheanJob) {
    // Timeouts can occur when reserving a job, so this must be taken 
    // into account.  No cause for alarm.
    echo "Beanstalk worker timed out when reserving a job.";
    exit(0);
}
$core->bootstrap(array('Autoloader', 'Config', 'Db', 'Options', 'Pluginbroker', 'Plugins', 'Jobs', 'Storage'));
$worker = new Omeka_Job_Worker_Beanstalk($pheanstalk, 
    Zend_Registry::get('job_factory'), $core->getBootstrap()->db);
$worker->work($pheanJob);
