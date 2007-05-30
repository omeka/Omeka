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
 * @package    Zend_Session
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Session.php 2883 2007-01-18 05:56:31Z gavin $
 * @since      Preview Release 0.2
 */

/**
 * Zend_Session_Core
 */
require_once 'Zend/Session/Core.php';

/**
 * Zend_Session_Exception
 */
require_once 'Zend/Session/Exception.php';

/**
 * Zend_Session
 *
 * @category Zend
 * @package Zend_Session
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Session implements IteratorAggregate
{

    /**
     * used as option to constructor to prevent additional instances to the same namespace
     */
    const SINGLE_INSTANCE = true;

    /**
     * Session_Core instance
     *
     * @var Zend_Session_Core
     */
    protected $_sessionCore = null;

    /**
     * Namespace - which namespace this instance of zend-session is saving-to/getting-from
     *
     * @var string
     */
    protected $_namespace = "Default";

    /**
     * Namespace locking mechanism
     *
     * @var array
     */
    static protected $_namespaceLocks = array();

    /**
     * Single instance namespace array to ensure data security.
     *
     * @var array
     */
    static protected $_singleInstances = array();

    /**
     * __construct() - This will create an instance that saves to/gets from an
     * instantiated core.  An optional namespace allows for saving/getting
     * to isolated sections of the session.  An optional argument $singleInstance
     * will prevent any futured attempts of getting a Zend_Session object in the
     * same namespace that is provided.
     *
     * @param string $namespace       - programmatic name of the requested namespace
     * @param bool $singleInstance    - prevent creation of additional instances for this namespace
     * @param Zend_Session_Core $core - OPTIONAL instance of Zend_Session_Core, used only for testing purposes
     * @return void
     */
    public function __construct($namespace = 'Default', $singleInstance = false, Zend_Session_Core $core = null)
    {
        if ($namespace === '') {
            throw new Zend_Session_Exception('Session namespace must be a non-empty string.');
        }

        if ($namespace[0] == "_") {
            throw new Zend_Session_Exception('Session namespace must not start with an underscore.');
        }

        if (isset(self::$_singleInstances[$namespace])) {
            throw new Zend_Session_Exception('A session namespace "'
                . $namespace . '" already exists and has been set as the only instance of this namespace.');
        }

        if ($singleInstance === true) {
            self::$_singleInstances[$namespace] = true;
        }

        $this->_namespace = $namespace;
        $this->_sessionCore = $core ? $core : Zend_Session_Core::getInstance();
        $this->_sessionCore->_startNamespace($namespace);
    }


    /**
     * getIterator() - return an iteratable object for use in foreach and the like,
     * this completes the IteratorAggregate interface
     *
     * @return ArrayObject - iteratable container of the namespace contents
     */
    public function getIterator()
    {
        $name_values = $this->_sessionCore->namespaceGet($this->_namespace);

        return new ArrayObject($name_values);
    }


    /**
     * setExpirationSeconds() - expire the namespace, or specific variables after a specified
     * number of seconds
     *
     * @param int $seconds     - expires in this many seconds
     * @param mixed $variables - OPTIONAL list of variables to expire (defaults to all)
     * @return void
     */
    public function setExpirationSeconds($seconds, $variables = null)
    {
        $this->_sessionCore->namespaceSetExpirationSeconds($this->_namespace, $seconds, $variables);
    }


    /**
     * setExpirationHops() - expire the namespace, or specific variables after a specified
     * number of page hops
     *
     * @param int $hops        - how many "hops" (number of subsequent requests) before expiring
     * @param mixed $variables - OPTIONAL list of variables to expire (defaults to all)
     * @param boolean $hopCountOnUsageOnly - OPTIONAL if set, only count a hop/request if this namespace is used
     * @return void
     */
    public function setExpirationHops($hops, $variables = null, $hopCountOnUsageOnly = false)
    {
        $this->_sessionCore->namespaceSetExpirationHops($this->_namespace, $hops, $variables, $hopCountOnUsageOnly);
    }


    /**
     * lock() - mark a session/namespace as readonly
     *
     * @return void
     */
    public function lock()
    {
        self::$_namespaceLocks[$this->_namespace] = true;
    }


    /**
     * unlock() - unmark a session/namespace to enable read & write
     *
     * @return void
     */
    public function unlock()
    {
        unset(self::$_namespaceLocks[$this->_namespace]);
    }


    /**
     * unlockAll() - unmark all session/namespaces to enable read & write
     *
     * @return void
     */
    static public function unlockAll()
    {
        self::$_namespaceLocks = array();
    }


    /**
     * isLocked() - return lock status, true if, and only if, read-only
     *
     * @return bool
     */
    public function isLocked()
    {
        return isset(self::$_namespaceLocks[$this->_namespace]);
    }


    /**
     * unsetAll() - unset all variables in this namespace
     *
     * @return true
     */
    public function unsetAll()
    {
        return $this->_sessionCore->namespaceUnset($this->_namespace);
    }

    /**
     * __get() - method to get a variable in this objects current namespace
     *
     * @param string $name - programmatic name of a key, in a <key,value> pair in the current namespace
     * @return mixed
     */
    protected function __get($name)
    {
        if ($name === '') {
            throw new Zend_Session_Exception("The '$name' key must be a non-empty string");
        }

        return $this->_sessionCore->namespaceGet($this->_namespace, $name);
    }


    /**
     * __set() - method to set a variable/value in this objects namespace
     *
     * @param string $name - programmatic name of a key, in a <key,value> pair in the current namespace
     * @param mixed $value - value in the <key,value> pair to assign to the $name key
     * @return true
     */
    protected function __set($name, $value)
    {
        if (isset(self::$_namespaceLocks[$this->_namespace])) {
            throw new Zend_Session_Exception('This session/namespace has been marked as read-only.');
        }

        if ($name === '') {
            throw new Zend_Session_Exception("The '$name' key must be a non-empty string");
        }

        return $this->_sessionCore->namespaceSet($this->_namespace, $name, $value);
    }


    /**
     * __isset() - determine if a variable in this objects namespace is set
     *
     * @param string $name - programmatic name of a key, in a <key,value> pair in the current namespace
     * @return bool
     */
    protected function __isset($name)
    {
        if ($name === '') {
            throw new Zend_Session_Exception("The '$name' key must be a non-empty string");
        }

        return $this->_sessionCore->namespaceIsset($this->_namespace, $name);
    }


    /**
     * __unset() - unset a variable in this objects namespace.
     *
     * @param string $name - programmatic name of a key, in a <key,value> pair in the current namespace
     * @return true
     */
    protected function __unset($name)
    {
        if ($name === '') {
            throw new Zend_Session_Exception("The '$name' key must be a non-empty string");
        }

        return $this->_sessionCore->namespaceUnset($this->_namespace, $name);
    }

}
