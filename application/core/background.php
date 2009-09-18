<?php
/**
 * Bootstrap file for background processes.
 * 
 * @version $Id$
 * @package Omeka
 * @copyright Copyright (c) 2009 Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

// Require the necessary files.
$baseDir = dirname(__FILE__);

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

//echo $processClass . "\n";

// Get the process arguments
$processArgs = $process->getArguments();

//print_r($processArgs);
//echo "\n";

// if (class_exists($processClass)) {
//     echo 'class exists';
// } else {
//     echo 'no class';
// }
// echo "\n";

// Create a custom process object
$processObject = new $processClass($process);

// Run the custom process and pass in the arguments
$processObject->run($processArgs);