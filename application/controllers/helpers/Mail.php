<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Action helper for sending email.
 * 
 * @package Omeka\Controller\ActionHelper
 */
class Omeka_Controller_Action_Helper_Mail extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * @var Zend_View
     */
    private $_view;
    
    /**
     * Subject of the email.
     * @var string
     */
    private $_subject;
    
    /**
     * Prefix (prepended to the subject).
     * @var string
     */
    private $_subjectPrefix;
    
    /**
     * @param Zend_View $view View to render as the message body.
     */
    public function __construct(Zend_View $view)
    {
        $this->_view = $view;
        $this->_mail = new Zend_Mail;
        $this->_mail->addHeader('X-Mailer', 'PHP/' . phpversion());
    }
    
    /**
     * Delegate to the Zend_Mail instance.
     *
     * @param string $method Method called.
     * @param array $args Arguments to method.
     */
    public function __call($method, $args)
    {
        if (method_exists($this->_mail, $method)) {
            return call_user_func_array(array($this->_mail, $method), $args);
        }
        throw new BadMethodCallException("Method named '$method' does not exist.");
    }
    
    /**
     * Set the prefix for the subject header.  Typically takes the form "[Site Name] ".
     *
     * @param string $prefix Subject prefix.
     */
    public function setSubjectPrefix($prefix)
    {
        $this->_subjectPrefix = $prefix;
    }
    
    /**
     * Set the subject of the email.
     *
     * @param string $subject Email subject.
     */
    public function setSubject($subject)
    {
        $this->_subject = $subject;
    }
    
    /**
     * Render the given view and use it as the body of the email.
     * 
     * @param string $viewScript View script path.
     * @param boolean $html Whether or not the assigned view script will render
     * as HTML.  Defaults to false.
     */
    public function setBodyFromView($viewScript, $html = false)
    {
        $rendered = $this->_view->render($viewScript);
        $html ? $this->_mail->setBodyHtml($rendered) 
              : $this->_mail->setBodyText($rendered);
    }
    
    /**
     * Send the email.
     * 
     * @internal Delegates to Zend_Mail::send().  Is only necessary for additional
     * processing of the subject line prior to sending.
     * @param Zend_Mail_Transport_Abstract $transport Optional defaults to null.
     * @see Zend_Mail::send()
     */
    public function send($transport = null)
    {
        // Prepare the subject line.
        $this->_mail->setSubject($this->_subjectPrefix . $this->_subject);
        return $this->_mail->send($transport);
    }            
}
