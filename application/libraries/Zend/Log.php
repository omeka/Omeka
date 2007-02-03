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
 * @package    Zend_Log
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * Zend_Log_Adapter_Interface
 */
require_once 'Zend/Log/Adapter/Interface.php';

/**
 * Zend_Log_Adapter_Null
 */
require_once 'Zend/Log/Adapter/Null.php';

/**
 * Zend_Log_Exception
 */
require_once 'Zend/Log/Exception.php';


/**
 * @category   Zend
 * @package    Zend_Log
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Log
{
    /**
     * Mask that includes all log levels
     */
    const LEVEL_ALL     = 255;

    /**
     * Log levels
     */
    const LEVEL_DEBUG   = 1;
    const LEVEL_INFO    = 2;
    const LEVEL_WARNING = 4;
    const LEVEL_ERROR   = 8;
    const LEVEL_SEVERE  = 16;

    /**
     * This array contains the names of the log levels in order to support
     * logging the names of the log message level instead of its numeric value.
     *
     * @var array
     */
    static protected $_levelNames = array(
        1  => 'DEBUG',
        2  => 'INFO',
        4  => 'WARNING',
        8  => 'ERROR',
        16 => 'SEVERE'
        );

    /**
     * The static class Zend_Log holds an array of Zend_Log instances
     * in this variable that are created with registerLogger().
     *
     * @var      array
     */
    static private $_instances = array();

    /**
     * The static class Zend_Log holds an array of Zend_Log instances
     * in this variable that are created with registerLogger().
     *
     * @var      array
     */
    static private $_defaultLogName = 'LOG';

    /**
     * When this class is instantiated by registerLogger, it is
     * pushed onto the $_instances associative array.  The $_logName
     * is the key to instance in this array, and also how the user
     * will specify the instance when using the other static method
     * calls (e.g. Zend_Log::log() ).
     *
     * @var      string
     */
    protected $_logName = '';

    /**
     * Logging level mask, the bitwise OR of any of the
     * Zend_Log::LEVEL_* constants that will be logged by this
     * instance of Zend_Log.  All other levels will be ignored.
     *
     * @var      integer
     */
    protected $_levelMask = self::LEVEL_ALL;

    /**
     * Every instance of Zend_Log must contain a child object which
     * is an implementation of Zend_Log_Adapter that provides the log
     * storage.
     *
     * @var      Zend_Log_Adapter_Interface
     */
    protected $_adapter = null;

    /**
     * A string which is automatically prefixed to any message sent
     * to the Zend_Log::log() method.
     *
     * @var      string
     */
    protected $_messagePrefix = '';

    /**
     * A string which is automatically appended to any message sent
     * to the Zend_Log::log() method.
     *
     * @var      string
     */
    protected $_messageSuffix = '';

    /**
     * Array of available fields for logging
     *
     * @var array
     */
    protected $_fields = array('message' => '',
                               'level'   => '');



    /**
     * Class constructor.  Zend_Log uses the singleton pattern.  Only
     * a single Zend_Log static class may be used, however instances
     * of Zend_Log may be stored inside the Zend_Log static class by
     * calling registerLogger().
     *
     * @param string $logName Name of the Zend_Log instance, which
     * will be the key to the Zend_Log::$_instances array.
     *
     * @param Zend_Log_Adapter_Interface $adapter
     */
    private function __construct($logName, Zend_Log_Adapter_Interface $adapter)
    {
        $this->_adapter = $adapter;
        $this->_adapter->logName = $logName;
    }


    /**
     * Returns the instance of Zend_Log in the Zend_Log::$_instances
     * array.
     *
     * @param  logName $logName Key in the Zend_Log::$_instances associative array.
     * @throws Zend_Log_Exception
     * @return Zend_Log_Adapter_Interface
     */
    private static function _getInstance($logName = null)
    {
        if (is_null($logName)) {
            $logName = self::$_defaultLogName;
        }

        if (!self::hasLogger($logName)) {
            throw new Zend_Log_Exception("No instance of log named \"$logName\"");
        }

        return self::$_instances[$logName];
    }


    /**
     * Instantiates a new instance of Zend_Log carrying the supplied Zend_Log_Adapter_Interface and stores
     * it in the $_instances array.
     *
     * @param  Zend_Log_Adapter_Interface $logAdapter Log adapter implemented from Zend_Log_Adapter_Interface
     * @param  string                     $logName    Name of this instance, used to access it from other static functions.
     * @throws Zend_Log_Exception
     * @return boolean                    True
     */
    public static function registerLogger(Zend_Log_Adapter_Interface $logAdapter, $logName=null)
    {
        if (is_null($logName)) {
            $logName = self::$_defaultLogName;
        }

        /* @var $log Zend_Log */
        if (!self::hasLogger($logName)) {
            self::$_instances[$logName] = new Zend_Log($logName, $logAdapter);
        } else {
            throw new Zend_Log_Exception("Cannot register, \"$logName\" already exists.");
        }

        return true;
    }


    /**
     * Destroys an instance of Zend_Log in the $_instances array that was added by registerLogger()
     *
     * @param  string  $logName Name of this instance, used to access it from other static functions.
     * @throws Zend_Log_Exception
     * @return boolean True
     */
    public static function unregisterLogger($logName = null)
    {
        if (is_null($logName)) {
            $logName = self::$_defaultLogName;
        }

        if (!self::hasLogger($logName)) {
            throw new Zend_Log_Exception("Cannot unregister, no instance of log named \"$logName\".");
        }

        unset(self::$_instances[$logName]);
        return true;
    }


    /**
     * Returns True if the specified logName is a registered logger.  If no logName is supplied,
     * the function returns True if at least one logger exists.
     *
     * @param   string $logName   Name of registered logger to check, or null.
     * @return  boolean           Registered logger?
     */
    public static function hasLogger($logName = null)
    {
        if (!is_null($logName)) {
            return isset(self::$_instances[$logName]);
        }

        return sizeof(self::$_instances) > 0;
    }


    /**
     * Returns information about the registered loggers.
     *
     * array(2) {
     *   ["LOG"]=>          array key is the logger name
     *   array(2) {
     *      ["adapter"]=>   string,  name of the Zend_Log_AdapterClass class
     *      ["default"]=>   boolean, is this the default logger?
     *    }
     *  }
     *
     * @return  array       Is there at least one registered logger?
     */
    public static function getLoggerInfo()
    {
        if (!self::hasLogger()) {
            return false;
        }

        $loggerInfo = array();
        foreach (self::$_instances as $logName => $logger) {
            $loggerInfo[$logName]['adapter'] = get_class($logger->_adapter);
            $loggerInfo[$logName]['default'] = ($logName == self::$_defaultLogName);
        }

        return $loggerInfo;
    }


    /**
     * Sets the default logger.  If no logName is specified, then "LOG" is used.  For any
     * named logger other than "LOG", the logger must have been registered with registerLogger().
     *
     * @param  string        $logName        Name of this instance, used to access it from other static functions.
     * @return boolean       True
     */
    public static function setDefaultLogger($logName = null)
    {
        if (is_null($logName) || $logName == 'LOG') {
            $logName = 'LOG';
        } else if (!self::hasLogger($logName)) {
            throw new Zend_Log_Exception("Cannot set default, no instance of log named \"$logName\".");
        }

        self::$_defaultLogName = $logName;
        return true;
    }


    /**
     * Sets the values for log fields. Omitted fields are set to default values.
     *
     * @param  array $fields
     * @param  string $logName
     * @return boolean True
     */
    public static function setFields($fields, $logName = null)
    {
        if (is_null($logName)) {
            $logName = self::$_defaultLogName;
        }

        if (!array_key_exists('message', $fields)) {
            $fields['message'] = '';
        }

        if (!array_key_exists('level', $fields)) {
            $fields['level'] = '';
        }

        self::_getInstance($logName)->_fields = $fields;
        return true;
    }


    /**
     * Returns an array of the log fields.
     *
     * @param  string $logName
     * @return array
     */
    public static function getFields($logName = null)
    {
        if (is_null($logName)) {
            $logName = self::$_defaultLogName;
        }

        return self::_getInstance($logName)->_fields;
    }


    /**
     * Sends a message to the log.
     *
     * @param string $message
     * @param integer $level
     * @param mixed $logName_or_fields
     * @param string $logName
     * @throws Zend_Log_Exception
     * @return boolean True
     */
    public static function log($message, $level = self::LEVEL_DEBUG, $logName_or_fields = null, $logName = null)
    {
        // Check to see that the specified log level is actually a level
        // and not the LEVEL_ALL mask or an invalid level.
        if (!self::isLogLevel($level)) {
            throw new Zend_Log_Exception('Unknown log level specified.');
        }

        if (is_string($logName_or_fields)) {
            $logName = $logName_or_fields;
        } else {
            if (!is_null($logName_or_fields)) {
                // Fields must be specified as key=>value pairs.
                if (!is_array($logName_or_fields)) {
                    throw new Zend_Log_Exception('Optional fields must be supplied as an associative array of key/value pairs.');
                }

                /**
                 * If the first key in the $logName_or_fields array is numeric, we'll assume that this is an array
                 * that was generated by array() and as such it's an array of lognames.  Otherwise, assume fields.
                 */
                reset($logName_or_fields);
                if (is_numeric(key($logName_or_fields))) {
                    $logName = $logName_or_fields;
                    $fields = null;
                } else {
                    // Fields passed must be in the array with keys matching the keys that were set by setFields().
                    $fields = array();
                    foreach ($logName_or_fields as $fieldName => $fieldValue) {
                        $fields[$fieldName] = $fieldValue;
                    }
                }
            }
        }


        /**
         * A log may be specified or the default log will be selected.  A special logname, ZF, exists
         * only for internal logging of the framework.
         */
        if (is_null($logName)) {
            $logName = self::$_defaultLogName;
        } else {
            if ($logName == 'ZF' && !isset(self::$_instances['ZF'])) {
                self::registerLogger(new Zend_Log_Adapter_Null(), 'ZF');
            }
        }


        /**
         * For any fields that were not specified, use the defaults.
         */
        $fields['message'] = $message;
        $fields['level'] = self::$_levelNames[$level];
        foreach (self::getFields($logName) as $fieldName => $fieldValue) {
            if (!array_key_exists($fieldName, $fields)) {
                $fields[$fieldName] = $fieldValue;
            }
        }


        /**
         * If the supplied logName is actually an array of logNames, then
         * call the function recursively to post to all the logs.
         */
        if (is_array($logName)) {
            foreach ($logName as $l) {
                self::log($message, $level, $fields, $l);
            }
            return true;
        }

        // Write the message to the log if the current log level will accept it.
        /* @var $logger Zend_Log */
        $logger = self::_getInstance($logName);

        if ($level & $logger->_levelMask) {
            $fields['message'] = $logger->_messagePrefix . $message . $logger->_messageSuffix;
            $logger->_adapter->write($fields);
        }

        return true;
    }


    /**
     * Destroy all Zend_Log instances in Zend_Log::$_instances.  This is equivalent to calling unregister()
     * for each log instance.
     *
     * @return boolean True
     */
    public static function close()
    {
        // This will cause the destruction of the instances.  The destructor
        // in the Zend_Log_Adapter_File class will clean up on its way out.
        self::$_instances = null;

        return true;
    }


    /**
     * Sets a message prefix.  The prefix will be automatically prepended to any message that is
     * sent to the specified log.
     *
     * @param  string         $prefix         The prefix string
     * @param  string         $logName        Name of this instance
     * @return boolean        True
     */
    public static function setMessagePrefix($prefix, $logName = null)
    {
        self::_getInstance($logName)->_messagePrefix = $prefix;
        return true;
    }


    /**
     * Sets a message suffix.  The suffix will be automatically appended to any message that is
     * sent to the specified log.
     *
     * @param  string         $suffix         The suffix string
     * @param  string         $logName        Name of this instance
     * @return boolean        True
     */
    public static function setMessageSuffix($suffix, $logName = null)
    {
        self::_getInstance($logName)->_messageSuffix = $suffix;
        return true;
    }


    /**
     * Sets the logging level of the log instance to one of the Zend_Log::LEVEL_* constants.  Only
     * messages with this log level will be logged by the instance, all others will be ignored.
     *
     * @param  integer            $level
     * @param  string             $logName        Name of this instance
     * @throws Zend_Log_Exception
     * @return boolean            True
     */
    public static function setLevel($level, $logName = null)
    {
        if (!self::isLogLevel($level)) {
            throw new Zend_Log_Exception('Unknown log level specified.');
        }

        self::_getInstance($logName)->_levelMask = $level;
        return true;
    }


    /**
     * Sets the logging level of the log instance based on a mask.  The mask is the bitwise OR
     * of any of the Zend_Log::LEVEL_* constants.
     *
     * @param  integer            $mask           The log level mask
     * @param  string             $logName        Name of this instance
     * @throws Zend_Log_Exception
     * @return boolean            True
     */
    public static function setMask($mask, $logName = null)
    {
        if (!is_int($mask) || $mask < 0 || $mask > 255) {
            throw new Zend_Log_Exception('Level mask out of range (should be integer between 0 and 255).');
        }

        $logger = self::_getInstance($logName);
        $logger->_levelMask = $mask;
        return true;
    }


    /**
     * Sets and adapter-specific option.
     *
     * @param string    $optionKey      The option name
     * @param string    $optionValue    The option value
     * @param string    $logName        Name of this instance
     */
    public static function setAdapterOption($optionKey, $optionValue, $logName = null)
    {
        $logger = self::_getInstance($logName);
        return $logger->_adapter->setOption($optionKey, $optionValue);
    }


    /**
     * Tests if the supplied $level is one of the valid log levels (Zend_Log::LEVEL_* constants).
     *
     * @param  int     $level       Value to test
     * @return boolean              Is it a valid level?
     */
    public static function isLogLevel($level)
    {
        return in_array($level, array(self::LEVEL_SEVERE, self::LEVEL_ERROR, self::LEVEL_WARNING,
                        self::LEVEL_INFO, self::LEVEL_DEBUG));
    }

}

