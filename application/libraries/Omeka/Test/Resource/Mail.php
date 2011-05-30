<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2011
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * Testing resource for saving mail to the filesystem.
 *
 * @package Omeka
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 */
class Omeka_Test_Resource_Mail extends Zend_Application_Resource_ResourceAbstract
{    
    /**
     * @return Zend_Mail
     */
    public function init()
    {
        $this->getBootstrap()->bootstrap('Config');

        $config = Zend_Registry::get('test_config');
        
        // If there's no path set, configure a blank path.
        // This avoids errors on non-mail tests, but you'll still get
        // an exception on mail tests to remind you to set the path.
        if (isset($config->paths)) {
            $path = $config->paths->maildir;
        } else {
            $path = '';
        }

        $transport = new Zend_Mail_Transport_File(array(
            'path' => $path,
            'callback' => array(get_class($this), 'mailCallback')));
        Zend_Mail::setDefaultTransport($transport);
        
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
