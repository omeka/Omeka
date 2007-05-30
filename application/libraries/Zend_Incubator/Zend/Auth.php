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
 * @version    $Id: Auth.php 2794 2007-01-16 01:29:51Z bkarwin $
 */


/**
 * @category   Zend
 * @package    Zend_Auth
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Auth
{
    /**
     * Default session namespace
     */
    const SESSION_NAMESPACE_DEFAULT = 'Zend_Auth';

    /**
     * Default session variable name for authentication token
     */
    const SESSION_TOKEN_NAME_DEFAULT = 'token';

    /**
     * Authentication adapter
     *
     * @var Zend_Auth_Adapter
     */
    protected $_adapter;

    /**
     * Whether or not to automatically use the session for persisting authentication token
     *
     * @var boolean
     */
    protected $_useSession;

    /**
     * Session namespace used for storing authentication token
     *
     * @var string
     */
    protected $_sessionNamespace;

    /**
     * Member name for authentication token
     */
    protected $_sessionTokenName;

    /**
     * Sets the authentication adapter
     *
     * @param  Zend_Auth_Adapter $adapter
     * @param  boolean           $useSession
     * @param  string            $sessionNamespace
     * @param  string            $sessionToken
     * @return void
     */
    public function __construct(Zend_Auth_Adapter $adapter, $useSession = true,
                                $sessionNamespace = self::SESSION_NAMESPACE_DEFAULT,
                                $sessionTokenName = self::SESSION_TOKEN_NAME_DEFAULT)
    {
        $this->_adapter = $adapter;
        $this->setUseSession($useSession);
        $this->setSessionNamespace($sessionNamespace);
        $this->setSessionTokenName($sessionTokenName);
    }

    /**
     * Authenticates against the attached adapter
     *
     * All parameters are passed along to the adapter's authenticate() method.
     *
     * @param  array $options
     * @uses   Zend_Auth_Adapter::authenticate()
     * @return Zend_Auth_Token_Interface
     */
    public function authenticate($options)
    {
        $token = $this->_adapter->authenticate($options);

        if ($this->_useSession) {
            require_once 'Zend/Session.php';
            $session = new Zend_Session($this->_sessionNamespace);
            $session->{$this->_sessionTokenName} = $token;
        }

        return $token;
    }

    /**
     * Returns whether or not the session is used automatically
     *
     * @return boolean
     */
    public function getUseSession()
    {
        return $this->_useSession;
    }

    /**
     * Set whether or not to use the session automatically
     *
     * @param  booolean $useSession
     * @return Zend_Auth Provides a fluent interface
     */
    public function setUseSession($useSession)
    {
        $this->_useSession = (boolean) $useSession;

        return $this;
    }

    /**
     * Returns the session namespace used for storing authentication token
     *
     * @return string
     */
    public function getSessionNamespace()
    {
        return $this->_sessionNamespace;
    }

    /**
     * Sets the session namespace used for storing authentication token
     *
     * @param  string $sessionNamespace
     * @return Zend_Auth Provides a fluent interface
     */
    public function setSessionNamespace($sessionNamespace)
    {
        $this->_sessionNamespace = (string) $sessionNamespace;
    }

    /**
     * Returns the name of the session object member where the authentication token is located
     *
     * @return string
     */
    public function getSessionTokenName()
    {
        return $this->_sessionTokenName;
    }

    /**
     * Sets the name of the session object member where the authentication token is located
     *
     * @param  string $sessionTokenName
     * @return Zend_Auth Provides a fluent interface
     */
    public function setSessionTokenName($sessionTokenName)
    {
        $this->_sessionTokenName = (string) $sessionTokenName;

        return $this;
    }


    /**
     * Returns an existing authentication token from the session, or null if there is no token
     * in the session
     *
     * The location in the session of the token determined by the session namespace and token
     * member name currently set for this object.
     *
     * @return Zend_Auth_Token_Interface|null
     */
    public function getToken()
    {
        require_once 'Zend/Session.php';
        $session = new Zend_Session($this->_sessionNamespace);
        if (isset($session->{$this->_sessionTokenName})) {
            return $session->{$this->_sessionTokenName};
        }
        return null;
    }

    /**
     * Returns true if and only if an existing authentication token exists at the location
     * determined by the session namespace and token member name currently set for this object
     * and the token represents a successful authentication attempt
     *
     * @return boolean
     */
    public function isLoggedIn()
    {
        if (null !== ($token = $this->getToken())) {
            return $token->isValid();
        }
        return false;
    }

    /**
     * Removes an existing authentication token from the location determined by the session
     * namespace and token member name currently set for this object
     *
     * @return void
     */
    public function logout()
    {
        require_once 'Zend/Session.php';
        $session = new Zend_Session($this->_sessionNamespace);
        if (isset($session->{$this->_sessionTokenName})) {
            unset($session->{$this->_sessionTokenName});
        }
    }

}
