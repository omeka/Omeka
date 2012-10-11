<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Set up the mail transport that Omeka uses to send mail.
 *
 * This makes use of Zend_Application_Resource_Mail for configuring the mail 
 * resource. config.ini can be set up using either the Zend Framework way or 
 * using the older Omeka configuration style (for backwards-compatibility), 
 * though the newer style is recommended.
 * 
 * @package Omeka\Application\Resource
 */
class Omeka_Application_Resource_Mail extends Zend_Application_Resource_ResourceAbstract
{
    private $_zendResource;
    
    public function __construct($options = null)
    {
        $this->_zendResource = new Zend_Application_Resource_Mail($options);
        parent::__construct($options);
    }
    
    /**
     * @return Zend_Mail
     */
    public function init()
    {
        $config = $this->getBootstrap()->bootstrap('Config')->config;
        // Skip configuration if we don't have any of the mail settings 
        // properly setup.
        if (!isset($config->mail)) {
            return;
        }
        // Old-style mail transport configuration.  Merging the 'options' array 
        // with its parent makes this equivalent to the Zend Framework 
        // configuration.
        $options = $config->mail->toArray();
        if (isset($options['transport']['options'])) {
            $options['transport'] = array_merge($options['transport'], 
                $options['transport']['options']);
            unset($options['transport']['options']);
        }
        
        $this->_zendResource->setOptions($options);
        $transport = $this->_zendResource->init();
        return new Zend_Mail;        
    }
}
