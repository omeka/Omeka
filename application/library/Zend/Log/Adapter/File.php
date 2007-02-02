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
 * Zend_Log_Adapter_Exception
 */
require_once 'Zend/Log/Adapter/Exception.php';


/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Log_Adapter_File implements Zend_Log_Adapter_Interface
{
    /**
    * Holds the PHP resource for an open file, or null.
    *
    * @var      +resource
    *           +null
    */
    private $_fileResource = null;


    /**
    * Filename on the filesystem where the log file is stored.
    *
    * @var      string
    */
    private $_filename = '';


    /**
    * PHP access mode of the file, either 'a'ppend or over'w'rite
    *
    * @var      string
    */
    private $_accessMode = '';


    /**
    * Termination character(s) that are automatically appended to each line.
    *
    * @var      string
    */
    private $_lineEnding = "\n";


    /**
    * Buffer, array of lines waiting to be written to the filesystem.
    *
    * @var      array
    */
    private $_buffer = array();


    /**
    * Number of lines in the buffer
    *
    * @var      string
    */
    private $_bufferedLines = 0;


    /**
    * Options:
    *   buffer          True:  use buffering
    *                   False: no buffering, write immediately
    *
    *   bufferLines     Maximum number of lines in the buffer
    *
    *   keepOpen        True:  keep file resource open between writes
    *                   False: close the resource immediately after each write
    * @var      array
    */
    private $_options = array('buffer'      => false,
                              'bufferLines' => 20,
                              'keepOpen'    => false,
                              'format' => '%message%, %level%');


    /**
    * Class Constructor
    *
    * @var      filename    Name of the file on the filesystem to write the log.
    */
    public function __construct($filename, $accessMode='a')
    {
        $this->_filename = $filename;
        $this->_setAccessMode($accessMode);
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
	 * Sets the access mode of the log file on the filesystem
	 *
	 * @param  $accessMode     Access mode: either 'a' append or 'w' overwrite
	 * @return bool            True
	 */
    protected function _setAccessMode($accessMode)
    {
        // Check for valid access mode
        $accessMode = substr(strtolower($accessMode), 0, 1);
        if ($accessMode!='w' && $accessMode!='a') {
            throw new Zend_Log_Adapter_Exception("Illegal access mode specified.  Specify 'a' for append or 'w' for overwrite.");
        }
        $this->_accessMode = $accessMode;

        return true;
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
        if (!array_key_exists($optionKey, $this->_options)) {
            throw new Zend_Log_Adapter_Exception("Unknown option \"$optionKey\".");
        }
        $this->_options[$optionKey] = $optionValue;

        return true;
    }


	/**
	 * Opens the logfile for writing.
	 *
	 * @param  $filename       Filename to open
	 * @param  $accessMode     Either "w"rite or "a"ppend
	 * @return bool            True
	 */
	public function open($filename=null, $accessMode=null)
	{
        if ($filename !== null) {
            $this->_filename = $filename;
        }

        if ($accessMode !== null) {
            $this->_setAccessMode($accessMode);
        }
        
        if (! $this->_fileResource = @fopen($this->_filename, $this->_accessMode, false)) {
            throw new Zend_Log_Adapter_Exception("Log file \"$filename\" could not be opened");
        }

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
	    // Add the message to the buffer.
	    $this->_buffer[] = $this->_parseLogLine($fields);
	    $this->_bufferedLines += 1;

	    // If the buffer is full, or buffering is not used,
	    // then flush the contents of the buffer to the filesystem now.
        if (!$this->_options['buffer'] || $this->_bufferedLines >= $this->_options['bufferLines']) {
            $this->flush();
        }

	    return true;
	}


	/**
	 * Format a line before sending into the storage.
	 *
	 * @param string $message
	 * @param int $level
	 * @return string
	 */
	protected function _parseLogLine($fields)
	{
        $output = $this->_options['format'];
	    foreach ($fields as $fieldName=>$fieldValue) {
	        $output = str_replace("%$fieldName%", $fieldValue, $output);
	    }
	    return $output;
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
	    // Nothing to flush if the buffer is empty.
	    if (!$this->_bufferedLines) {
	        return false;
	    }

	    // If the file resource is not yet open, then open it now.
	    if (!is_resource($this->_fileResource)) {
            $this->open();
	    }

	    // Flush the buffer to the filesystem
	    foreach ($this->_buffer as $line) {
	        if (!fwrite($this->_fileResource, $line . $this->_lineEnding)) {
	            throw new Zend_Log_Adapter_Exception("Log file \"{$this->_filename}\" could not be written.");
	        }
	    }

	    // Clean the buffer
        $this->_buffer = array();
        $this->_bufferedLines = 0;

        // If the file is not to be kept open, close it until the next flush.
        if (!$this->_options['keepOpen']) {
            $this->close();
        }

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
	    // If a file resource is open, then close it.
	    if (is_resource($this->_fileResource)) {
	        fclose($this->_fileResource);
	        $this->_fileResource = null;
	    }

	    return true;
	}


}

