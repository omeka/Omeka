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

require "{$baseDir}/../../paths.php";
require "{$baseDir}/../libraries/Omeka/Core.php";

// Load only the required core phases.
$core = new Omeka_Core;
$core->phasedLoading('initializePluginBroker');

// Set the command line arguments.
$options = new Zend_Console_Getopt(array('process|p=i' => 'process to run'));

try {
    $options->parse();
} catch (Zend_Console_Getopt_Exception $e) {
    echo $e->getUsageMessage();
    exit;
}

// Get the database object.
$db = get_db();

// Get the process to run
$processId = $options->getOption('process');
$process = $db->getTable('Process')->find($processId);

// Get the name of the process class to run
$processClass = $process->class;

$processObject = new $processClass($process);
$processObject->run();