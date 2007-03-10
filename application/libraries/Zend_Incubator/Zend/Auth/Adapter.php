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
 * @version    $Id: Adapter.php 2794 2007-01-16 01:29:51Z bkarwin $
 */


/**
 * @category   Zend
 * @package    Zend_Auth
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Auth_Adapter
{
    /**
     * Extending classes may implement this method, accepting authentication
     * credentials as parameters, and returning the authentication results
     *
     * @param  array $options
     * @throws Zend_Auth_Adapter_Exception
     * @return Zend_Auth_Token_Interface
     */
    public static function staticAuthenticate(array $options)
    {
        require_once 'Zend/Auth/Adapter/Exception.php';
        throw new Zend_Auth_Adapter_Exception(__METHOD__ . '() must be implemented by a concrete adapter class');
    }

    /**
     * Extending classes should implement this method, accepting authentication
     * credentials as parameters, and returning the authentication results
     *
     * @param  $options
     * @throws Zend_Auth_Adapter_Exception
     * @return Zend_Auth_Token_Interface
     */
    public function authenticate(array $options)
    {
        require_once 'Zend/Auth/Adapter/Exception.php';
        throw new Zend_Auth_Adapter_Exception(__METHOD__ . '() must be implemented by a concrete adapter class');
    }
}
