<?php

/**
 * If logging has been enabled in the config file, then set up 
 * Zend's logging mechanism.
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2009
 **/
class Omeka_Core_Resource_Logger extends Zend_Application_Resource_ResourceAbstract
{
    public function init()
    {
        $bootstrap = $this->getBootstrap();
        $bootstrap->bootstrap('Config');
        $config = $bootstrap->getResource('Config');

        if (!$config->log->errors && !$config->log->sql) {
            return;
        }
        
        $logFile = LOGS_DIR.DIRECTORY_SEPARATOR . 'errors.log';
        
        if (!is_writable($logFile)) {
            throw new Exception('Error log file cannot be written to. Please give this file read/write permissions for the web server.');
        }
        
        $writer = new Zend_Log_Writer_Stream($logFile);
        $logger = new Zend_Log($writer);
        
        return $logger;
    }
}