<?php
require_once 'Zend/Auth/Token/Interface.php';
class Omeka_Auth_Token implements Zend_Auth_Token_Interface
{
    /**
     * Whether or not this token represents a successful authentication attempt
     *
     * @var boolean
     */
    protected $_valid;

    /**
     * Array containing the username and realm from the authentication attempt
     *
     * @var array
     */
    protected $_identity;

    /**
     * Message from the authentication adapter describing authentication failure
     *
     * @var string|null
     */
    protected $_message;

    /**
     * Sets the token values, as appropriate
     *
     * @param  boolean $valid
     * @param  array   $identity
     * @param  string  $message
     * @return void
     */
    public function __construct($valid, $identity, $message = null)
    {
        $this->_valid    = $valid;
        $this->_identity = $identity;
        $this->_message  = $message;
    }

    /**
     * Defined by Zend_Auth_Token_Interface
     *
     * @return boolean
     */
    public function isValid()
    {
        return $this->_valid;
    }

    /**
     * Defined by Zend_Auth_Token_Interface
     *
     * Returns an array having keys of 'realm' and 'username', having string values that
     * correspond to those provided in the authentication request.
     *
     * @return array
     */
    public function getIdentity()
    {
        return $this->_identity;
    }

    /**
     * Defined by Zend_Auth_Token_Interface
     *
     * @return string|null
     */
    public function getMessage()
    {
        return $this->_message;
    }
}
?>