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

// Require the necessary files.
$baseDir = dirname(__FILE__);

/**
 * bootstrap.php is required at minimum in order to define all path constants.
 */
require_once("{$baseDir}/../../bootstrap.php");
require_once("{$baseDir}/../libraries/Omeka/Application.php");

// Set the command line arguments.
$options = new Zend_Console_Getopt(array('process|p=i' => 'process to run'));

try {
    $options->parse();
} catch (Zend_Console_Getopt_Exception $e) {
    echo $e->getUsageMessage();
    exit;
}

// Load a core set of resources.
$application = new Omeka_Application(APPLICATION_ENV);
$application->bootstrap(array('Autoloader', 'Config', 'Db', 'Options', 
                              'Pluginbroker', 'Plugins', 'Mainnavigation', 'Jobs', 'Storage', 'Mail',
));

// Get the database object.
$db = get_db();

// Get the process to run
$processId = $options->getOption('process');
$process = $db->getTable('Process')->find($processId);

// Get the user to run the process under
$processUserId = $process->user_id;
$processUser = $db->getTable('User')->find($processUserId);
Zend_Registry::get('bootstrap')->getContainer()->currentuser = $processUser;

// Get the name of the process class to run
$processClass = $process->class;

// Get the process arguments
$processArgs = $process->getArguments();

// Enable process logging.
$logFile = LOGS_DIR . '/processes.log';
$logger = null;
if ($application->getBootstrap()->getResource('Config')->log->processes && is_writable($logFile)) {
    // Set the writer.
    $writer = new Zend_Log_Writer_Stream($logFile);
    $format = '%processClass% (%processId%) %timestamp% %priorityName% (%priority%): %message%' . PHP_EOL;
    $formatter = new Zend_Log_Formatter_Simple($format);
    $writer->setFormatter($formatter);
    // Set the logger.
    $logger = new Zend_Log($writer);
    $logger->setEventItem('processClass', $processClass);
    $logger->setEventItem('processId', $processId);
}

// Create a custom process object
$processObject = new $processClass($process, $logger);

// Run the custom process and pass in the arguments
try {
    $processObject->run($processArgs);
} catch (Exception $e) {
    $process->status = Process::STATUS_ERROR;
    if ($logger instanceof Zend_Log) {
        $logger->log($e, Zend_Log::ERR);
    }
}
