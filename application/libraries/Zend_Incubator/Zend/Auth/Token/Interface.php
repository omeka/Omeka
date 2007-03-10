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
 * @version    $Id: Interface.php 2794 2007-01-16 01:29:51Z bkarwin $
 */


/**
 * @category   Zend
 * @package    Zend_Auth
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface Zend_Auth_Token_Interface
{
    /**
     * Returns whether the authentication token is currently valid (i.e., whether it
     * represents a successful authentication attempt)
     *
     * @return boolean
     */
    public function isValid();

    /**
     * Returns the identity represented by the authentication token
     *
     * @return mixed
     */
    public function getIdentity();

    /**
     * Returns a message about why the authentication token is not valid
     * or null if the authentication token is valid
     *
     * @return string|null
     */
    public function getMessage();

}
