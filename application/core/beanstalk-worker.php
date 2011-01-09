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

function handle_exception($e)
{
    echo "Beanstalk worker error (" . get_class($e) . "): " . $e->getMessage() . 
         PHP_EOL . $e->getTraceAsString() . PHP_EOL;
    exit(1);
}
set_exception_handler('handle_exception');

$core->bootstrap(array('Autoloader', 'Config', 'Db', 'Options', 'Jobs'));
$worker = new Omeka_Job_Worker_Beanstalk($options, Zend_Registry::get('job_factory'));
$worker->work();
