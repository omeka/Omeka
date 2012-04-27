<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2012
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * FlashMessenger action helper.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2012
 */
class Omeka_Controller_Action_Helper_FlashMessenger extends Zend_Controller_Action_Helper_FlashMessenger
{    
    const MESSAGE_KEY = 'message';
    const STATUS_KEY = 'status';

    /**
     * addMessage() - Add a message to flash message
     *
     * @param  string|array $message The message to add
     * @param  string|null $status The message status
     * @return Mu_Controller_Action_Helper_FlashMessenger Provides a fluent interface
     */
    public function addMessage($message, $status = null) 
    {
        return parent::addMessage(array(self::MESSAGE_KEY => $message,
            self::STATUS_KEY => $status));
    }
    
    
    /**
     * getMessages() - Get messages from a specific status
     *
     * @param string|null $status The status from which to get messages.
     * @return array
     */
    public function getMessages($status = null)
    {
        return $this->_getMessages(parent::getMessages(), $status);
    }

    /**
     * Iterate through messages and extract based on status.
     */
    private function _getMessages($iter, $status)
    {
        $filtered = array();
        if ($status) {
            foreach ($iter as $message) {
                if ($status == $message[self::STATUS_KEY]) {
                    $filtered[] = $message[self::MESSAGE_KEY];
                }
            }
        } else {
            foreach ($iter as $message) {
                $filtered[$message[self::STATUS_KEY]][] = 
                    $message[self::MESSAGE_KEY];
            }
        }
        return $filtered;
    }

    /**
     * Clear all messages from the previous request & specified status
     *
     * @param string $status The namespace to clear
     * @return boolean True if messages were cleared, false if none existed
     */
    public function clearMessages($status = null)
    {
        if (!$status) {
            return parent::clearMessages();
        }
        $existed = false;
        foreach (self::$_messages[$this->_namespace] as $key => $message) {
            if ($message[self::STATUS_KEY] == $status) {
                unset(self::$_messages[$this->_namespace][$key]);
                $existed = true;
            }
        }
        return $existed;
    }

    /**
     * Clear all current messages with specified status
     *
     * @param string $status The status to clear
     * @return boolean True if messages were cleared, false if none existed
     */
    public function clearCurrentMessages($status = null)
    {
        if (!$status) {
            return parent::clearCurrentMessages();
        }
        $existed = false;
        foreach (self::$_session->{$this->_namespace} as $key => $message) {
            if ($message[self::STATUS_KEY] == $status) {
                unset(self::$_session->{$this->_namespace}[$key]);
                $existed = true;
            }
        }
        return $existed;
    }

    /**
     * Whether has messages with a specific status (or any messages, if null).
     */
    public function hasMessages($status = null)
    {
        if (!$status) {
            return parent::hasMessages();
        }
        return $this->_hasMessages(self::$_messages[$this->_namespace], $status);
    }
    
    public function hasCurrentMessages($status = null)
    {
        if (!$status) {
            return parent::hasCurrentMessages();
        }
        return $this->_hasMessages(self::$_session->{$this->_namespace}, $status);
    }
    
    private function _hasMessages($iter, $status)
    {
        $existed = false;
        foreach ($iter as $key => $message) {
            if ($message[self::STATUS_KEY] == $status) {
                $existed = true;
            }
        }
        return $existed;
    }

    public function getCurrentMessages($status = null)
    {
        return $this->_getMessages(parent::getCurrentMessages(), $status);
    }
    
    /**
     * For testing purposes, reset all the static properties of this class.
     */
    public static function reset()
    {
        self::$_messages = array();
        self::$_session = null;
        self::$_messageAdded = false;
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
    
    public function getNamespace() 
    {
        return $this->_namespace;
    }
}
