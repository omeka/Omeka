<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * 
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2009
 **/
class Omeka_Core_Resource_Mail extends Zend_Application_Resource_ResourceAbstract
{
    public function init()
    {
        $config = $this->getBootstrap()->bootstrap('Config')->getResource('Config');
        // Skip configuration if we don't have any of the mail settings properly setup.
        if (!isset($config->mail)) {
            return;
        }
        
        $transportMethod = (string)$config->mail->transport->type;
        
        switch ($transportMethod) {
            // Don't do anything, just use the default transport.
            case 'Sendmail':
                break;
            case 'Smtp':
                $this->_configureSmtp($config);
                break;
            default:
                throw new InvalidArgumentException("Mail client must be configured with a valid protocol (mail.transport.type in config.ini).");
                break;
        }
        
        return new Zend_Mail;        
    }
    
    /**
     * Instantiate the Zend_Mail SMTP transport object using settings from 
     * config.ini.
     */
    private function _configureSmtp(Zend_Config $config)
    {
        if (empty($config->mail->transport->host)) {
            throw new InvalidArgumentException("SMTP hostname must be properly configured (mail.transport.host in config.ini).");
        }
        
        if (!isset($config->mail->transport->options)) {
            $smtpOptions = array();
            // throw new InvalidArgumentException("SMTP options must be properly configured (mail.transport.options in config.ini).");
        } else {
            $smtpOptions = $config->mail->transport->options->toArray();
        }
        
        $transport = new Zend_Mail_Transport_Smtp($config->mail->transport->host, 
                                                  $smtpOptions);
        
        Zend_Mail::setDefaultTransport($transport);
    }
}
