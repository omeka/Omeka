<?php
/**
 * Omeka
 * 
 * Bootstrap file for background processes.
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 * @package Omeka
 */

/**
 * Paths.php is required at minimum in order to define all path constants.
 */
require_once dirname(__FILE__) . "/../../bootstrap.php";
require_once "Omeka/Application.php";

declare(ticks = 1);

// Set the command line arguments.
$options = new Zend_Console_Getopt(array(
    'queue|q=s' => 'Name of queue (tube) to use', 
    'host|h=s' => 'Beanstalkd host IP',
    'port|p-i' => 'Beanstalkd port',
));

try {
    $options->parse();
} catch (Zend_Console_Getopt_Exception $e) {
    echo $e->getUsageMessage();
    exit;
}

$application = new Omeka_Application(APPLICATION_ENV);

function handle_exception($e)
{
    _log($e, Zend_Log::ERR);
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


$application->bootstrap(array('Autoloader', 'Logger'));
$host = isset($options->host) ? $options->host : '127.0.0.1';
$port = isset($options->port) ? $options->port : 11300;
$pheanstalk = new Pheanstalk_Pheanstalk("$host:$port");
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
$application->bootstrap(array(
    'Autoloader', 'Config', 'Db', 'Filederivatives', 'Locale', 'Options',
    'Pluginbroker', 'Plugins', 'Jobs', 'Storage', 'Mail', 'View'
));

// resend() must send jobs to the original queue by default.
$jobDispatcher = Zend_Registry::get('job_dispatcher');
if ($options->queue) {
    $jobDispatcher->setQueueName($options->queue);
}

// Log all to stdout.
$log = $application->getBootstrap()->logger;
$log->addWriter(new Zend_Log_Writer_Stream('php://output'));

$worker = new Omeka_Job_Worker_Beanstalk($pheanstalk, 
    Zend_Registry::get('job_factory'), $application->getBootstrap()->db);
$worker->work($pheanJob);
