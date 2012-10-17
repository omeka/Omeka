<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * FlashMessenger action helper.
 * 
 * @package Omeka\Controller\ActionHelper
 */
class Omeka_Controller_Action_Helper_FlashMessenger extends Zend_Controller_Action_Helper_Abstract
{    
    const MESSAGE_KEY = 'message';
    const STATUS_KEY = 'status';

    private $_messenger;

    public function __construct()
    {
        $this->_messenger = new Zend_Controller_Action_Helper_FlashMessenger;
    }
    /**
     * addMessage() - Add a message to flash message
     *
     * @param  string|Omeka_Validate_Exception $message The message to add
     * @param  string|null $status The message status
     * @return Mu_Controller_Action_Helper_FlashMessenger Provides a fluent interface
     */
    public function addMessage($message, $status = null) 
    {
        if ($message instanceof Omeka_Validate_Exception) {
            $message = $message->getErrors();
            if ($status === null) {
                $status = 'error';
            }
        } else if ($message instanceof Omeka_Validate_Errors) {
            $message = (string) $message;
            if ($status === null) {
                $status = 'error';
            }
        } else if ($status === null) {
            $status = 'alert';
        }
        
        return $this->_messenger->addMessage(array(self::MESSAGE_KEY => $message,
            self::STATUS_KEY => $status));
    }
    
    
    /**
     * getMessages() - Get messages
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->_filterMessages($this->_messenger->getMessages());
    }

    public function _filterMessages($messages)
    {
        $filtered = array();
        foreach ($messages as $message) {
            $filtered[$message[self::STATUS_KEY]][] = $message[self::MESSAGE_KEY];
        }
        return $filtered;
    }

    /**
     * Clear all messages from the previous request & specified status
     *
     * @return boolean True if messages were cleared, false if none existed
     */
    public function clearMessages()
    {
        return $this->_messenger->clearMessages();
    }

    /**
     * Clear all current messages with specified status
     *
     * @return boolean True if messages were cleared, false if none existed
     */
    public function clearCurrentMessages()
    {
        return $this->_messenger->clearCurrentMessages();
    }

    /**
     * Whether has messages with a specific status (or any messages, if null).
     */
    public function hasMessages()
    {
        return $this->_messenger->hasMessages();
    }
    
    public function hasCurrentMessages()
    {
        return $this->_messenger->hasCurrentMessages();
    }

    public function getCurrentMessages($status = null)
    {
        return $this->_filterMessages($this->_messenger->getCurrentMessages());
    }
    
    /**
     * Strategy pattern: proxy to addMessage()
     *
     * @param  string $message
     * @return void
     */
    public function direct($message, $status = null)
    {
        return $this->addMessage($message, $status);
    }
}
