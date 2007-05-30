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
 * @category   Zend
 * @package    Zend_Log
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface Zend_Log_Adapter_Interface
{
	/**
	 * Open the storage resource.  If the adapter supports buffering, this may not
	 * actually open anything until it is time to flush the buffer.
	 */
	public function open();


	/**
	 * Write a message to the log.  If the adapter supports buffering, the
	 * message may or may not actually go into storage until the buffer is flushed.
	 *
	 * @param $fields     Associative array, contains keys 'message' and 'level' at a minimum.
	 */
	public function write($fields);


	/**
	 * Close the log storage opened by the log adapter.  If the adapter supports
	 * buffering, all log data must be sent to the log before the storage is closed.
	 */
	public function close();


	/**
	 * Sets an option specific to the implementation of the log adapter.
	 *
	 * @param $optionKey       Key name for the option to be changed.  Keys are adapter-specific
	 * @param $optionValue     New value to assign to the option
	 */
    public function setOption($optionKey, $optionValue);
}

