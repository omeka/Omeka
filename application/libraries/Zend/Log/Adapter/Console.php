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
class Zend_Log_Adapter_Console implements Zend_Log_Adapter_Interface
{

	public $logName;
    protected $_options = array('format' => '%logname%, %message%, %level%');


	public function __construct($params=null)
	{
		$this->params = $params;
	}


	public function setOption($optionKey, $optionValue)
	{
	    if (!array_key_exists($optionKey, $this->_options)) {
	        throw new Zend_Log_Adapter_Exception("Unknown option \"$optionKey\".");
	    }
	    $this->_options[$optionKey] = $optionValue;
	    return true;
	}


	public function open()
	{
		return true;
	}


	public function wipe()
	{
        return true;
	}


	public function close()
	{
		return true;
	}



	public function write($fields)
	{
	    $fields['logname'] = $this->logName;
	    echo $this->_parseLogLine($fields);
		return true;
	}


	protected function _parseLogLine($fields)
	{
        $output = $this->_options['format'];
	    foreach ($fields as $fieldName=>$fieldValue) {
	        $output = str_replace("%$fieldName%", $fieldValue, $output);
	    }
	    return $output;
	}

}


