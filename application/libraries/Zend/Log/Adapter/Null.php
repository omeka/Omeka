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
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * Zend_Log_Adapter_Interface
 */
require_once 'Zend/Log/Adapter/Interface.php';


/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Log_Adapter_Null implements Zend_Log_Adapter_Interface
{
    /**
    * Class Constructor
    *
    */
    public function __construct()
    {
        return true;
    }


    /**
    * Class Destructor
    *
    * Always check that the file has been closed and the buffer flushed before destruction.
    */
    public function __destruct()
    {
        $this->flush();
        $this->close();
    }


	/**
	 * Sets an option specific to the implementation of the log adapter.
	 *
	 * @param  $optionKey      Key name for the option to be changed.  Keys are adapter-specific
	 * @param  $optionValue    New value to assign to the option
	 * @return bool            True
	 */
    public function setOption($optionKey, $optionValue)
    {
        return true;
    }


	/**
	 * Sets an option specific to the implementation of the log adapter.
	 *
	 * @param  $optionKey      Key name for the option to be changed.  Keys are adapter-specific
	 * @param  $optionValue    New value to assign to the option
	 * @return bool            True
	 */
	public function open($filename=null, $accessMode='a')
	{
        return true;
	}


	/**
	 * Write a message to the log.  This function really just writes the message to the buffer.
	 * If buffering is enabled, the message won't hit the filesystem until the buffer fills
	 * or is flushed.  If buffering is not enabled, the buffer will be flushed immediately.
	 *
	 * @param  $message    Log message
	 * @param  $level      Log level, one of Zend_Log::LEVEL_* constants
	 * @return bool        True
	 */
    public function write($fields)
    {
	    return true;
	}


	/**
	 * Write a message to the log.  This function really just writes the message to the buffer.
	 *
	 * @param  $message    Log message
	 * @param  $level      Log level, one of Zend_Log::LEVEL_* constants
	 * @return bool        True
	 */
	public function flush()
	{
        return true;
	}


	/**
	 * Closes the file resource for the logfile.  Calling this function does not write any
	 * buffered data into the log, so flush() must be called before close().
	 *
	 * @return bool        True
	 */
	public function close()
	{
	    return true;
	}

}

