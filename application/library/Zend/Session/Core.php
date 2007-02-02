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
 * @version    $Id: Core.php 2812 2007-01-16 01:40:54Z bkarwin $
 * @since      Preview Release 0.2
 */

/**
 * Zend
 */
require_once 'Zend.php';

/**
 * Zend_Session_Exception
 */
require_once 'Zend/Session/Exception.php';

/**
 * Zend_Session_SaveHandler_Interface
 */
require_once 'Zend/Session/SaveHandler/Interface.php';

/**
 * Zend_Session_Core
 *
 * @category Zend
 * @package Zend_Session
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
final class Zend_Session_Core
{

    /**
     * Check whether or not the session was started
     *
     * @var bool
     */
    static private $_sessionStarted = false;

    /**
     * Whether or not the session id has been regenerated this request.
     *
     * Id regeneration state
     * <0 - regenerate requested when session is started
     * 0  - do nothing
     * >0 - already called session_regenerate_id()
     *
     * @var int
     */
    static private $_regenerateIdState = 0;

    /**
     * Instance of Zend_Session_Core
     *
     * @var Zend_Session_Core
     */
    static private $_instance;

    /**
     * Private list of php's ini values for ext/session
     * null values will default to the php.ini value, otherwise
     * the value below will overwrite the default ini value, unless
     * the user has set an option explicity with setOptions()
     *
     * @var array
     */
    static private $_defaultOptions = array(
        'save_path'                 => null,
        'name'                      => null, /* this should be set to a unique value for each application */
        'save_handler'              => null,
        //'auto_start'                => null, /* intentionally excluded (see manual) */
        'gc_probability'            => null,
        'gc_divisor'                => null,
        'gc_maxlifetime'            => null,
        'serialize_handler'         => null,
        'cookie_lifetime'           => null,
        'cookie_path'               => null,
        'cookie_domain'             => null,
        'cookie_secure'             => null,
        'use_cookies'               => null,
        'use_only_cookies'          => 'on',
        'referer_check'             => null,
        'entropy_file'              => null,
        'entropy_length'            => null,
        'cache_limiter'             => null,
        'cache_expire'              => null,
        'use_trans_sid'             => null,
        'bug_compat_42'             => null,
        'bug_compat_warn'           => null,
        'hash_function'             => null,
        'hash_bits_per_character'   => null
    );

    /**
     * List of options pertaining to Zend_Session_Core that can be set by developers
     * using Zend_Session_Core::setOptions(). This list intentionally duplicates
     * the individual declaration of static "class" variables by the same names.
     *
     * @var array
     */
    static private $_localOptions = array(
        'strict'                => '_strict',
        'remember_me_seconds'   => '_rememberMeSeconds'
    );

    /**
     * Whether or not write close has been performed.
     *
     * @var bool
     */
    static private $_writeClosed = false;

    /**
     * Whether or not session id cookie has been deleted
     *
     * @var bool
     */
    static private $_sessionCookieDeleted = false;

    /**
     * Whether or not session has been destroyed via session_destroy()
     *
     * @var bool
     */
    static private $_destroyed = false;

    /**
     * Whether or not session permits writing (modification of $_SESSION[])
     *
     * @var bool
     */
    static private $_writable = false;

    /**
     * Whether or not session permits reading (reading data in $_SESSION[])
     *
     * @var bool
     */
    static private $_readable = false;

    /**
     * Whether or not session must be initiated before usage
     *
     * @var bool
     */
    static private $_strict = false;

    /**
     * Default number of seconds the session will be remembered for when asked to be remembered
     *
     * @var unknown_type
     */
    static private $_rememberMeSeconds = 1209600; // 2 weeks

    /**
     * Whether the default options listed in Zend_Session_Core::$_localOptions have been set
     *
     * @var unknown_type
     */
    static private $_defaultOptionsSet = false;

    /**
     * Since expiring data is handled at startup to avoid __destruct difficulties,
     * the data that will be expiring at end of this request is held here
     *
     * @var array
     */
    static private $_expiringData = array();


    /**
     * Error message thrown when an action requires modification,
     * but current Zend_Session_Core has been marked as read-only.
     */
     const _THROW_NOT_WRITABLE_MSG = 'Zend_Session_Core is currently marked as read-only.';


    /**
     * Error message thrown when an action requires reading session data,
     * but current Zend_Session_Core is not marked as readable.
     */
     const _THROW_NOT_READABLE_MSG = 'Zend_Session_Core is not marked as readable.';


    /**
     * setOptions - set both the class specified
     *
     * @param array $userOptions - pass-by-keyword style array of <option name, option value> pairs
     * @throws Zend_Session_Exception
     * @return void
     */
    static public function setOptions(Array $userOptions = array())
    {
        // set default options on first run only (before applying user settings)
        if (!self::$_defaultOptionsSet) {
            foreach (self::$_defaultOptions as $default_option_name => $default_option_value) {
                if (isset(self::$_defaultOptions[$default_option_name]) && $default_option_value !== null) {
                    ini_set('session.' . $default_option_name, $default_option_value);
                }
            }

            self::$_defaultOptionsSet = true;
        }

        // set the options the user has requested to set
        foreach ($userOptions as $user_option_name => $user_option_value) {

            $user_option_name = strtolower($user_option_name);

            // set the ini based values
            if (array_key_exists($user_option_name, self::$_defaultOptions)) {
                ini_set('session.' . $user_option_name, $user_option_value);
            }
            elseif (isset(self::$_localOptions[$user_option_name])) {
                self::${self::$_localOptions[$user_option_name]} = $user_option_value;
            }
            else {
                throw new Zend_Session_Exception("Unknown option: $user_option_name = $user_option_value");
            }
        }
    }


    /**
     * setSaveHandler() - Session Save Handler assignment
     *
     * @param Zend_Session_SaveHandler_Interface $interface
     * @return void
     */
    static public function setSaveHandler(Zend_Session_SaveHandler_Interface $interface)
    {
        session_set_save_handler(
            array(&$interface, 'open'),
            array(&$interface, 'close'),
            array(&$interface, 'read'),
            array(&$interface, 'write'),
            array(&$interface, 'destroy'),
            array(&$interface, 'gc')
            );
    }


    /**
     * getInstance() - Enfore the Singleton of the core.
     *
     * @param boolean $instanceMustExist - OPTIONAL prevents Zend_Session_Core from auto-starting itself, if needed
     * @throws Zend_Session_Exception
     * @return Zend_Session_Core
     */
    static public function getInstance($instanceMustExist = false)
    {
        if (self::$_instance === null) {
            if ($instanceMustExist === true) {
                throw new Zend_Session_Exception('A valid session must exist before calling getInstance() in this manner.');
            }
            self::$_instance = true; // allow creation of the instance by the constructor
            self::$_instance = new self();
        }

        if (self::$_readable === false) {
            throw new Zend_Session_Exception(self::_THROW_NOT_READABLE_MSG);
        }

        return self::$_instance;
    }


    /**
     * regenerateId() - Regenerate the session id.  Best practice is to call this after
     * session is started.  If called prior to session starting, session id will be regenerated
     * at start time.
     *
     * @throws Zend_Session_Exception
     * @return void
     */
    static public function regenerateId()
    {
        if (headers_sent($filename, $linenum)) {
            throw new Zend_Session_Exception("You must call ".__CLASS__.'::'.__FUNCTION__.
                "() before any output has been sent to the browser; output started in {$filename}/{$linenum}");
        }

        if (self::$_sessionStarted && self::$_regenerateIdState <=0) {
            session_regenerate_id(true);
            self::$_regenerateIdState = 1;
        } else {
            /*
            // If we can detect that this requester had no session previously,
            // then why regenerate the id before the session has started?
            // Feedback wanted for:
            if (isset($_COOKIE[session_name()])
                || (!use only cookies && isset($_REQUEST[session_name()]))) {
                self::$_regenerateIdState = 1;
            } else {
                self::$_regenerateIdState = -1;
            }
            */
            self::$_regenerateIdState = -1;
        }
    }


    /**
     * rememberMe() - Replace the session cookie with one that will expire after a number of seconds in the future
     * (not when the browser closes).  Seconds are determined by self::$_rememberMeSeconds.
     * plus $seconds (defaulting to self::$_rememberMeSeconds).  Due to clock errors on end users' systems,
     * large values are recommended to avoid undesireable expiration of session cookies.
     *
     * @param $seconds integer - OPTIONAL specifies TTL for cookie in seconds from present time()
     * @return void
     */
    static public function rememberMe($seconds = null)
    {
        $seconds = (int) $seconds;
        $seconds = ($seconds > 0) ? $seconds : self::$_rememberMeSeconds;

        self::rememberUntil($seconds);
    }


    /**
     * forgetMe() - The exact opposite of rememberMe(), a session cookie is ensured to be 'session based'
     *
     * @return void
     */
    static public function forgetMe()
    {
        self::rememberUntil(0); // this will make sure the session is not 'session based'
    }


    /**
     * rememberUntil() - This method does the work of changing the state of the session cookie and making
     * sure that it gets resent to the browser via regenerateId()
     *
     * @param int $seconds
     * @return void
     */
    static public function rememberUntil($seconds = 0)
    {
        $cookie_params = session_get_cookie_params();

        session_set_cookie_params(
            $seconds,
            $cookie_params['path'],
            $cookie_params['domain'],
            $cookie_params['secure']
            );

        // normally "rememberMe()" represents a security context change, so should use new session id
        self::regenerateId();
    }


    /**
     * sessionExists() - whether or not a session exists for the current request
     *
     * @return bool
     */
    static public function sessionExists()
    {
        if (ini_get('session.use_cookies') == '1' && isset($_COOKIE[session_name()])) {
            return true;
        } elseif (!empty($_REQUEST[session_name()])) {
            return true;
        }

        return false;
    }


    /**
     * start() - Start the session.
     *
     * @throws Zend_Session_Exception
     * @return void
     */
    static public function start()
    {
        // make sure our default options (at the least) have been set
        if (!self::$_defaultOptionsSet) {
            self::setOptions();
        }

        if (headers_sent($filename, $linenum)) {
            throw new Zend_Session_Exception("You must call ".__CLASS__.'::'.__FUNCTION__.
                "() before any output has been sent to the browser; output started in {$filename}/{$linenum}");
        }

        if (self::$_sessionStarted) {
            throw new Zend_Session_Exception('start() can only be called once.');
        }

        // See http://www.php.net/manual/en/ref.session.php for explanation
        if (defined('SID')) {
            throw new Zend_Session_Exception('session has already been started by session.auto-start or session_start()');
        }

        session_start();
        self::$_readable = true;
        self::$_writable = true;
        self::$_sessionStarted = true;
        if (self::$_regenerateIdState === -1) {
            self::regenerateId();
        }

        // run validators if they exist
        if (isset($_SESSION['__ZF']['VALID'])) {
            self::_processValidators();
        }

        self::_processStartupMetadataGlobal();
    }


    /**
     * isStarted() - convenience method to determine if the session is already started.
     *
     * @return bool
     */
    static public function isStarted()
    {
        return self::$_sessionStarted;
    }


    /**
     * isRegenerated() - convenience method to determine if session_regenerate_id()
     * has been called during this request by Zend_Session_Core.
     *
     * @return bool
     */
    static public function isRegenerated()
    {
        return ( (self::$_regenerateIdState > 0) ? true : false );
    }


    /**
     * getId() - get the current session id
     *
     * @return string
     */
    static public function getId()
    {
        return session_id();
    }


    /**
     * setId() - set an id to a user specified id
     *
     * @throws Zend_Session_Exception
     * @param string $id
     * @return void
     */
    static public function setId($id)
    {
        if (headers_sent($filename, $linenum)) {
            throw new Zend_Session_Exception("You must call ".__CLASS__.'::'.__FUNCTION__.
                "() before any output has been sent to the browser; output started in {$filename}/{$linenum}");
        }

        if (!is_string($id) || $id === '') {
            throw new Zend_Session_Exception('You must provide a non-empty string as a session identifier.');
        }

        session_id($id);
    }


    /**
     * registerValidator() - register a validator that will attempt to validate this session for
     * every future request
     *
     * @param Zend_Session_Validator_Interface $validator
     * @return void
     */
    static public function registerValidator(Zend_Session_Validator_Interface $validator)
    {
        $validator->setup();
    }


    /**
     * stop() - Disable write access.  Optionally disable read (not implemented).
     *
     * @return void
     */
    static public function stop()
    {
        self::$_writable = false;
    }


    /**
     * writeClose() - Shutdown the sesssion, close writing and detach $_SESSION from the back-end storage mechanism.
     * This will complete the internal data transformation on this request.
     *
     * @param bool $readonly - OPTIONAL remove write access (i.e. throw error if Zend_Session's attempt writes)
     * @return void
     */
    static public function writeClose($readonly = true)
    {
        if (self::$_writeClosed) {
            return;
        }

        if ($readonly) {
            self::$_writable = false;
        }

        session_write_close();
        self::$_writeClosed = true;
    }


    /**
     * destroy() - This is used to destroy session data, and optionally, the session cookie itself
     *
     * @param bool $remove_cookie - OPTIONAL remove session id cookie, defaults to true (remove cookie)
     * @param bool $readonly - OPTIONAL remove write access (i.e. throw error if Zend_Session's attempt writes)
     * @return void
     */
    static public function destroy($remove_cookie = true, $readonly = true)
    {
        if (self::$_destroyed) {
            return;
        }

        if ($readonly) {
            self::$_writable = false;
        }

        session_destroy();
        self::$_destroyed = true;

        if ($remove_cookie) {
            self::expireSessionCookie();
        }
    }


    /**
     * expireSessionCookie() - Sends an expired session id cookie, causing the client to delete the session cookie
     *
     * @return void
     */
    static public function expireSessionCookie()
    {
        if (self::$_sessionCookieDeleted) {
            return;
        }

        self::$_sessionCookieDeleted = true;

        if (isset($_COOKIE[session_name()])) {
            $cookie_params = session_get_cookie_params();

            setcookie(
                session_name(),
                false,
                315554400, // strtotime('1980-01-01'),
                $cookie_params['path'],
                $cookie_params['domain'],
                $cookie_params['secure']
                );
        }
    }


    /**
     * _processGlobalMetadata() - this method initizes the sessions GLOBAL
     * metadata, mostly global data expiration calculations.
     *
     * @return void
     */
    static private function _processStartupMetadataGlobal()
    {
        // process global metadata
        if (isset($_SESSION['__ZF'])) {

            // expire globally expired values
            foreach ($_SESSION['__ZF'] as $namespace => $namespace_metadata) {

                // Expire Namespace by Time (ENT)
                if (isset($namespace_metadata['ENT']) && ($namespace_metadata['ENT'] > 0) && (time() > $namespace_metadata['ENT']) ) {
                    unset($_SESSION[$namespace]);
                    unset($_SESSION['__ZF'][$namespace]['ENT']);
                }

                // Expire Namespace by Global Hop (ENGH)
                if (isset($namespace_metadata['ENGH']) && $namespace_metadata['ENGH'] >= 1) {
                    $_SESSION['__ZF'][$namespace]['ENGH']--;

                    if ($_SESSION['__ZF'][$namespace]['ENGH'] === 0) {
                        if (isset($_SESSION[$namespace])) {
                            self::$_expiringData[$namespace] = $_SESSION[$namespace];
                            unset($_SESSION[$namespace]);
                        }
                        unset($_SESSION['__ZF'][$namespace]['ENGH']);
                    }
                }

                // Expire Namespace Variables by Time (ENVT)
                if (isset($namespace_metadata['ENVT'])) {
                    foreach ($namespace_metadata['ENVT'] as $variable => $time) {
                        if (time() > $time) {
                            unset($_SESSION[$namespace][$variable]);
                            unset($_SESSION['__ZF'][$namespace]['ENVT'][$variable]);

                            if (empty($_SESSION['__ZF'][$namespace]['ENVT'])) {
                                unset($_SESSION['__ZF'][$namespace]['ENVT']);
                            }
                        }
                    }
                }

                // Expire Namespace Variables by Global Hop (ENVGH)
                if (isset($namespace_metadata['ENVGH'])) {
                    foreach ($namespace_metadata['ENVGH'] as $variable => $hops) {
                        $_SESSION['__ZF'][$namespace]['ENVGH'][$variable]--;

                        if ($_SESSION['__ZF'][$namespace]['ENVGH'][$variable] === 0) {
                            if (isset($_SESSION[$namespace][$variable])) {
                                self::$_expiringData[$namespace][$variable] = $_SESSION[$namespace][$variable];
                                unset($_SESSION[$namespace][$variable]);
                            }
                            unset($_SESSION['__ZF'][$namespace]['ENVGH'][$variable]);
                        }
                    }
                }
            }

            if (empty($_SESSION['__ZF'][$namespace])) {
                unset($_SESSION['__ZF'][$namespace]);
            }

        }

        if (empty($_SESSION['__ZF'])) {
            unset($_SESSION['__ZF']);
        }
    }


    /**
     * _processStartupMetadataNamespace() - this method processes the metadata specific only
     * to a given namespace.  This is typically run at the instantiation of a Zend_Session object.
     *
     * @param string $namespace
     * @return void
     */
    static private function _processStartupMetadataNamespace($namespace)
    {
        if (!isset($_SESSION['__ZF'])) {
            return;
        }

        if (isset($_SESSION['__ZF'][$namespace])) {

            // Expire Namespace by Namespace Hop (ENNH)
            if (isset($_SESSION['__ZF'][$namespace]['ENNH'])) {
                $_SESSION['__ZF'][$namespace]['ENNH']--;

                if ($_SESSION['__ZF'][$namespace]['ENNH'] === 0) {
                    if (isset($_SESSION[$namespace])) {
                        self::$_expiringData[$namespace] = $_SESSION[$namespace];
                        unset($_SESSION[$namespace]);
                    }
                    unset($_SESSION['__ZF'][$namespace]['ENNH']);
                }
            }

            // Expire Namespace Variables by Namespace Hop (ENVNH)
            if (isset($_SESSION['__ZF'][$namespace]['ENVNH'])) {
                foreach ($_SESSION['__ZF'][$namespace]['ENVNH'] as $variable => $hops) {
                    $_SESSION['__ZF'][$namespace]['ENVNH'][$variable]--;

                    if ($_SESSION['__ZF'][$namespace]['ENVNH'][$variable] === 0) {
                        if (isset($_SESSION[$namespace][$variable])) {
                            self::$_expiringData[$namespace][$variable] = $_SESSION[$namespace][$variable];
                            unset($_SESSION[$namespace][$variable]);
                        }
                        unset($_SESSION['__ZF'][$namespace]['ENVNH'][$variable]);
                    }
                }
            }
        }

        if (empty($_SESSION['__ZF'][$namespace])) {
            unset($_SESSION['__ZF'][$namespace]);
        }

        if (empty($_SESSION['__ZF'])) {
            unset($_SESSION['__ZF']);
        }
    }


    /**
     * _processValidator() - internal function that is called in the existence of VALID metadata
     *
     * @throws Zend_Session_Exception
     * @return void
     */
    static private function _processValidators()
    {
        foreach ($_SESSION['__ZF']['VALID'] as $validator_name => $valid_data) {
            Zend::loadClass($validator_name);
            $validator = new $validator_name;
            if ($validator->validate() === false) {
                throw new Zend_Session_Exception("This session is not valid according to {$validator_name}.");
            }
        }
    }


    /**
     * INSTANACE METHODS
     */


    /**
     * Constructor
     *
     * @access private - *not* really, but we would like it to be private access to enforce the singleton pattern
     * @throws Zend_Session_Exception
     * @return void
     */
    public function __construct()
    {
        // In strict mode, do not allow auto-starting Zend_Session_Core, such as via "new Zend_Session()"
        if (self::$_strict === true && self::$_sessionStarted === false) {
            throw new Zend_Session_Exception('You must start the session with Zend_Session_Core::start() when session options are set to strict.');
        }

        if (self::$_instance !== true) {
            throw new Zend_Session_Exception('Should be initialized through Zend_Session_Core::getInstance() only.');
        }

        if (self::$_sessionStarted === false) {
            self::start();
        }
    }


    /**
     * Clone overriding - make sure that a developer cannot clone the core instance
     *
     * @throws Zend_Session_Exception
     * @return void
     */
    public function __clone()
    {
        throw new Zend_Session_Exception('Zend_Session_Core follows the singleton pattern.  Cloning is not allowed.');
    }


    /**
     * _startNamespace() - while this method is public, its really only intended use is
     * by the constructor of Zend_Session object.  This method initializes the session namespace.
     *
     * @param string $namespace
     * @return void
     */
    public function _startNamespace($namespace)
    {
        self::_processStartupMetadataNamespace($namespace);
    }


    /**
     * namespaceIsset() - check to see if a namespace or a variable within a namespace is set
     *
     * @param string $namespace
     * @param string $name
     * @return bool
     */
    public function namespaceIsset($namespace, $name = null)
    {
        if (self::$_readable === false) {
            throw new Zend_Session_Exception(self::_THROW_NOT_READABLE_MSG);
        }

        $return_value = null;

        if ($name === null) {
            return ( isset($_SESSION[$namespace]) || isset(self::$_expiringData[$namespace]) );
        } else {
            return ( isset($_SESSION[$namespace][$name]) || isset(self::$_expiringData[$namespace][$name]) );
        }
    }


    /**
     * namespaceUnset() - unset a namespace or a variable within a namespace
     *
     * @param string $namespace
     * @param string $name
     * @throws Zend_Session_Exception
     * @return void
     */
    public function namespaceUnset($namespace, $name = null)
    {
        if (self::$_writable === false) {
            throw new Zend_Session_Exception(self::_THROW_NOT_WRITABLE_MSG);
        }

        $name = (string) $name;

        // check to see if the api wanted to remove a var from a namespace or a namespace
        if ($name === '') {
            unset($_SESSION[$namespace]);
            unset(self::$_expiringData[$namespace]);
        } else {
            unset($_SESSION[$namespace][$name]);
            unset(self::$_expiringData[$namespace]);
        }

        // if we remove the last value, remove namespace.
        if (empty($_SESSION[$namespace])) {
            unset($_SESSION[$namespace]);
        }
    }


    /**
     * namespaceSet() - set a variable within a namespace.
     *
     * @param string $namespace
     * @param string $name
     * @param mixed $value
     * @throws Zend_Session_Exception
     * @return void
     */
    public function namespaceSet($namespace, $name, $value)
    {
        if (self::$_writable === false) {
            throw new Zend_Session_Exception(self::_THROW_NOT_WRITABLE_MSG);
        }

        $name = (string) $name;

        $_SESSION[$namespace][$name] = $value;
    }


    /**
     * namespaceGet() - get a variable from a namespace.
     *
     * @param string $namespace
     * @param string $name
     * @return mixed
     */
    public function namespaceGet($namespace, $name = null)
    {
        if (self::$_readable === false) {
            throw new Zend_Session_Exception(self::_THROW_NOT_READABLE_MSG);
        }

        $current_data  = (isset($_SESSION[$namespace]) && is_array($_SESSION[$namespace])) ?
            $_SESSION[$namespace] : array();
        $expiring_data = (isset(self::$_expiringData[$namespace]) && is_array(self::$_expiringData[$namespace])) ?
            self::$_expiringData[$namespace] : array();

        $merged_data = array_merge($current_data, $expiring_data);

        if ($name !== null) {
            if (isset($merged_data[$name])) {
                return $merged_data[$name];
            } else {
                return null;
            }
        } else {
            return $merged_data;
        }
    }


    /**
     * namespaceSetExpirationSeconds() - exprire a namespace, or data within after a specified number
     * of seconds.
     *
     * @param string $namespace
     * @param int $seconds
     * @param mixed $variables
     * @throws Zend_Session_Exception
     * @return void
     */
    public function namespaceSetExpirationSeconds($namespace, $seconds, $variables = null)
    {
        if (self::$_writable === false) {
            throw new Zend_Session_Exception(self::_THROW_NOT_WRITABLE_MSG);
        }

        if ($seconds <= 0) {
            throw new Zend_Session_Exception('Seconds must be positive.');
        }

        if ($variables === null) {

            // apply expiration to entire namespace
            $_SESSION['__ZF'][$namespace]['ENT'] = time() + $seconds;

        } else {

            if (is_string($variables)) {
                $variables = array($variables);
            }

            foreach ($variables as $variable) {
                if (!empty($variable)) {
                    $_SESSION['__ZF'][$namespace]['ENVT'][$variable] = time() + $seconds;
                }
            }
        }
    }


    /**
     * namespaceSetExpirationHops() -
     *
     * @param string $namespace
     * @param int $hops
     * @param mixed $variables
     * @param bool $hopCountOnUsageOnly
     * @throws Zend_Session_Exception
     * @return void
     */
    public function namespaceSetExpirationHops($namespace, $hops, $variables = null, $hopCountOnUsageOnly = false)
    {
        if (self::$_writable === false) {
            throw new Zend_Session_Exception(self::_THROW_NOT_WRITABLE_MSG);
        }

        if ($hops <= 0) {
            throw new Zend_Session_Exception('Hops must be positive number.');
        }

        if ($variables === null) {

            // apply expiration to entire namespace
            if ($hopCountOnUsageOnly === false) {
                $_SESSION['__ZF'][$namespace]['ENGH'] = $hops;
            } else {
                $_SESSION['__ZF'][$namespace]['ENNH'] = $hops;
            }

        } else {

            if (is_string($variables)) {
                $variables = array($variables);
            }

            foreach ($variables as $variable) {
                if (!empty($variable)) {
                    if ($hopCountOnUsageOnly === false) {
                        $_SESSION['__ZF'][$namespace]['ENVGH'][$variable] = $hops;
                    } else {
                        $_SESSION['__ZF'][$namespace]['ENVNH'][$variable] = $hops;
                    }
                }
            }
        }
    }


    /**
     * getIterator() - return an iteratable object for use in foreach and the like,
     * this completes the IteratorAggregate interface
     *
     * @return ArrayObject
     */
    static public function getIterator()
    {
        if (self::$_readable === false) {
            throw new Zend_Session_Exception(self::_THROW_NOT_READABLE_MSG);
        }

        $spaces  = array();
        if (isset($_SESSION)) {
            $spaces = array_keys($_SESSION);
            foreach($spaces as $key => $space) {
                if (!strncmp($space, '__', 2) || !is_array($_SESSION[$space])) {
                    unset($spaces[$key]);
                }
            }
        }

        return new ArrayObject(array_merge($spaces, array_keys(self::$_expiringData)));
    }

}
