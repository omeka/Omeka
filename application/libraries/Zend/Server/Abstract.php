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
 * @package    Zend_Server
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

require_once 'Zend/Server/Interface.php';

abstract class Zend_Server_Abstract implements Zend_Server_Interface {
	/**
     * @var array PHP's Magic Methods, these are ignored
     */
    static protected $magic_methods = array(
                                '__construct',
                                '__destruct',
                                '__get',
                                '__set',
                                '__call',
                                '__sleep',
                                '__wakeup',
                                '__isset',
                                '__unset',
                                '__tostring',
                                '__clone',
                                '__set_state',
                                );

   	/**
	 * Lowercase a string
	 *
	 * @param string $value
	 * @param string $key
	 * @return string Lower cased string
	 */
	static public function lowerCase(&$value, &$key)
	{
		return $value = strtolower($value);
	}
}
