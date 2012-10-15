<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * If logging has been enabled in the config file, then set up Zend's logging 
 * mechanism.
 * 
 * @package Omeka\Application\Resource
 */
class Omeka_Application_Resource_Logger extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * @return Zend_Log
     */
    public function init()
    {
        $bootstrap = $this->getBootstrap();
        $bootstrap->bootstrap('Config');
        $config = $bootstrap->getResource('Config');

        if (!$config->log->errors && !$config->log->sql) {
            return;
        }
        
        $logFile = LOGS_DIR . '/' . 'errors.log';
        
        if (!is_writable($logFile)) {
            throw new RuntimeException('Error log file cannot be written to. Please give this file read/write permissions for the web server.');
        }
        
        $writer = new Zend_Log_Writer_Stream($logFile);
        $logger = new Zend_Log($writer);

        if (isset($config->log->priority)) {
            $priority = $config->log->priority;
            if (defined($priority)) {
                $writer->addFilter(constant($priority));
            }
        }
        
        if (!empty($config->debug->email)) {
            $bootstrap->bootstrap('Mail');            
            $this->_addMailWriter($logger, (string)$config->debug->email,
                $config->debug->emailLogPriority);
        }
        
        return $logger;
    }
    
    /**
     * Set up debugging emails.
     *
     * @param Zend_Log $log
     * @param string $toEmail Email address of debug message recipient.
     */
    private function _addMailWriter(Zend_Log $log, $toEmail, 
        $filter = null)
    {
        $mailer = new Zend_Mail;
        $mailer->addTo($toEmail);
        $mailer->setFrom(get_option('administrator_email'));
        $logWriter = new Zend_Log_Writer_Mail($mailer);
        $logWriter->setSubjectPrependText('[' . get_option('site_title') . ']');
        if ($filter) {
            // Zend_Log::ERR, e.g.
            if (defined($filter)) {
                $filter = constant($filter);
            }
            $logWriter->addFilter($filter);
        }
        $log->addWriter($logWriter);
    }
}
