<?php

/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Auth
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Token.php 2794 2007-01-16 01:29:51Z bkarwin $
 */


/**
 * Zend_Auth_Token_Interface
 */
require_once 'Zend/Auth/Token/Interface.php';


/**
 * @category   Zend
 * @package    Zend_Auth
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Auth_Digest_Token implements Zend_Auth_Token_Interface
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
