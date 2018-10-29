<?php

class Omeka_Mail_TransportWrapper extends Zend_Mail_Transport_Abstract
{
    /**
     * @var Zend_Mail_Transport_Abstract
     */
    private $_transport;

    /**
     * @var string The From address to force for outgoing messages
     */
    private $_from;

    /**
     * @var string The From "friendly" name to force for outgoing messages
     */
    private $_fromName;

    public function __construct(Zend_Mail_Transport_Abstract $transport, $from, $fromName = null)
    {
        $this->_transport = $transport;
        $this->_from = $from;
        $this->_fromName = $fromName;
    }

    /**
     * Send a message by proxing to the underlying transport.
     *
     * Replace the From address, setting the original one to Reply-To if
     * there is no Reply-To already.
     */
    public function send(Zend_Mail $mail)
    {
        $originalFrom = $mail->getFrom();
        $mail->clearFrom();
        $mail->setFrom($this->_from, $this->_fromName);
        if (!$mail->getReplyTo()) {
            $mail->setReplyTo($originalFrom);
        }
        $this->_transport->send($mail);
    }

    /**
     * Dummy _sendMail().
     *
     * We're required to implement this but since send() just proxies
     * to another transport, our _sendMail will never get used.
     */
    protected function _sendMail()
    {}
}
