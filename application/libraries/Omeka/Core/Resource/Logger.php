<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

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
        
        if (!empty($config->debug->email)) {            
            $this->_addMailWriter($logger, (string)$config->debug->email);
        }
        
        return $logger;
    }
    
    private function _addMailWriter(Zend_Log $log, $toEmail)
    {
        $mailer = new Zend_Mail;
        $mailer->addTo($toEmail);
        $mailer->setFrom(get_option('administrator_email'));
        $logWriter = new Zend_Log_Writer_Mail($mailer);
        $logWriter->setSubjectPrependText('[' . get_option('site_title') . ']');
        $log->addWriter($logWriter);
    }
}