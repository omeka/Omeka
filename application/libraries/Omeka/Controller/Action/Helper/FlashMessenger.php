<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka-MU
 **/

/**
 * FlashMessenger action helper.
 *
 * @package Omeka-MU
 * @copyright Center for History and New Media, 2010
 **/
class Omeka_Controller_Action_Helper_FlashMessenger extends Zend_Controller_Action_Helper_FlashMessenger
{    
    /**
     * addMessage() - Add a message to flash message
     *
     * @param  string $message The message to add
     * @param  string $namespace The namespace to which the message is added
     * @return Mu_Controller_Action_Helper_FlashMessenger Provides a fluent interface
     */
    public function addMessage($message, $namespace = 'default') 
    {
        return $this->_runInNamespace($namespace, 'addMessage', array($message));
    }
    
    
    /**
     * getMessages() - Get messages from a specific namespace
     *
     * @param $namespace The namespace from which to get messages.
     * @return array
     */
    public function getMessages($namespace = 'default')
    {
        return $this->_runInNamespace($namespace, 'getMessages');
    }
    
    /**
     * Clear all messages from the previous request & specified namespace
     *
     * @param string $namespace The namespace to clear
     * @return boolean True if messages were cleared, false if none existed
     */
    public function clearMessages($namespace = 'default')
    {
        return $this->_runInNamespace($namespace, 'clearMessages');
    }
    
    public function hasMessages($namespace = 'default')
    {
        return $this->_runInNamespace($namespace, 'hasMessages');
    }
    
    public function hasCurrentMessages($namespace = 'default')
    {
        return $this->_runInNamespace($namespace, 'hasCurrentMessages');
    }
    
    /**
     * @internal Copied from parent::getCurrentMessages().  Did not work otherwise.
     */
    public function getCurrentMessages($namespace = 'default')
    {
        if ($this->hasCurrentMessages($namespace)) {
            return self::$_session->{$namespace};
        }

        return array();
    }
    
    private function _runInNamespace($namespace, $methodName, $methodArgs = array())
    {
        // store the current namespace
        $cNamespace = $this->getNamespace();
        
        // add the message to $namespace
        $this->setNamespace($namespace);
        $result = call_user_func_array(array($this, "parent::$methodName"), $methodArgs);
        
        // restore the current namespace
        $this->setNamespace($cNamespace);
        
        // return the result (true or false)
        return $result;
    }
    
    public function loadFromSession()
    {
        foreach (self::$_session as $namespace => $messages) {
            self::$_messages[$namespace] = $messages;
            unset(self::$_session->{$namespace});
        }
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
    public function direct($message, $namespace = 'default')
    {
        return $this->addMessage($message, $namespace);
    }
    
    public function getNamespace() 
    {
        return $this->_namespace;
    }
}