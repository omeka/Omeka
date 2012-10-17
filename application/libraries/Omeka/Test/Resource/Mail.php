<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Testing resource for saving mail to the filesystem.
 * 
 * @package Omeka\Test\Resource
 */
class Omeka_Test_Resource_Mail extends Zend_Application_Resource_ResourceAbstract
{    
    /**
     * @return Zend_Mail
     */
    public function init()
    {
        $bootstrap = $this->getBootstrap();
        if (!$bootstrap->hasPluginResource('Tempdir')) {
            $bootstrap->registerPluginResource('Tempdir');
        }
        $bootstrap->bootstrap('Tempdir');
        $tempDir = $bootstrap->getResource('Tempdir');

        $path = "{$tempDir}/mail";
        mkdir($path);

        $transport = new Zend_Mail_Transport_File(array(
            'path' => $path,
            'callback' => array(get_class($this), 'mailCallback')));
        Zend_Mail::setDefaultTransport($transport);
        Zend_Registry::set('test_mail_dir', $path);
        
        return new Zend_Mail;
    }

    /**
     * Makes mail output names contain both the timestamp and an incrementing
     * counter. The timestamp ensures mails between test runs don't
     * collide, the counter differentiates between messages sent during
     * the same timestamp unit (common).
     */
    static public function mailCallback($transport)
    {
        static $count = 0;
        return 'message_' . time() . '_' . ($count++) . '.tmp';
    }
}
