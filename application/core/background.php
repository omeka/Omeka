<?php
/**
 * Bootstrap file for background processes.
 * 
 * @access private
 * @package Omeka
 * @copyright Copyright (c) 2009 Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

// Require the necessary files.
$baseDir = dirname(__FILE__);

/**
 * Paths.php is required at minimum in order to define all path constants.
 */
require_once("{$baseDir}/../../paths.php");
require_once("{$baseDir}/../libraries/Omeka/Core.php");

// Set the command line arguments.
$options = new Zend_Console_Getopt(array('process|p=i' => 'process to run', 'lastphase|l=s' => 'last phase to load'));

try {
    $options->parse();
} catch (Zend_Console_Getopt_Exception $e) {
    echo $e->getUsageMessage();
    exit;
}

// Load only the phases needed.
$core = new Omeka_Core;
$lastPhase = $options->getOption('lastphase');
$core->phasedLoading($lastPhase);
$core->bootstrap('Jobs');

// Get the database object.
$db = get_db();

// Get the process to run
$processId = $options->getOption('process');
$process = $db->getTable('Process')->find($processId);

// Get the user to run the process under
$processUserId = $process->user_id;
$processUser = $db->getTable('User')->find($processUserId);
Omeka_Context::getInstance()->setCurrentUser($processUser);

// Get the name of the process class to run
$processClass = $process->class;

// Get the process arguments
$processArgs = $process->getArguments();

// Enable process logging.
$logFile = LOGS_DIR . '/processes.log';
$logger = null;
if ($core->getBootstrap()->getResource('Config')->log->processes && is_writable($logFile)) {
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
