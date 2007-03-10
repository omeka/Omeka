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
 * Zend_Auth_Adapter
 */
require_once 'Zend/Auth/Adapter.php';


/**
 * Zend_Auth_Digest_Token
 */
require_once 'Zend/Auth/Digest/Token.php';


/**
 * @category   Zend
 * @package    Zend_Auth
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Auth_Digest_Adapter extends Zend_Auth_Adapter
{
    /**
     * Filename against which authentication queries are performed
     *
     * @var string
     */
    protected $_filename;

    /**
     * Creates a new digest authentication object against the $filename provided
     *
     * @param  string $filename
     * @throws Zend_Auth_Digest_Exception
     * @return void
     */
    public function __construct($filename)
    {
        $this->_filename = (string) $filename;
    }

    /**
     * Authenticates against the given parameters
     *
     * $options requires the following key-value pairs:
     *
     *      'filename' => path to digest authentication file
     *      'realm'    => digest authentication realm
     *      'username' => digest authentication user
     *      'password' => password for the user of the realm
     *
     * @param  array $options
     * @throws Zend_Auth_Digest_Exception
     * @return Zend_Auth_Digest_Token
     */
    public static function staticAuthenticate(array $options)
    {
        $optionsRequired = array('filename', 'realm', 'username', 'password');
        foreach ($optionsRequired as $optionRequired) {
            if (!isset($options[$optionRequired]) || !is_string($options[$optionRequired])) {
                require_once 'Zend/Auth/Digest/Exception.php';
                throw new Zend_Auth_Digest_Exception("Option '$optionRequired' is required to be a string");
            }
        }

        if (false === ($fileHandle = @fopen($options['filename'], 'r'))) {
            require_once 'Zend/Auth/Digest/Exception.php';
            throw new Zend_Auth_Digest_Exception("Cannot open '{$options['filename']}' for reading");
        }

        $id       = "{$options['username']}:{$options['realm']}";
        $idLength = strlen($id);

        $tokenValid    = false;
        $tokenIdentity = array(
            'realm'    => $options['realm'],
            'username' => $options['username']
            );

        while ($line = trim(fgets($fileHandle))) {
            if (substr($line, 0, $idLength) === $id) {
                if (substr($line, -32) === md5("{$options['username']}:{$options['realm']}:{$options['password']}")) {
                    $tokenValid   = true;
                    $tokenMessage = null;
                } else {
                    $tokenMessage = 'Password incorrect';
                }
                return new Zend_Auth_Digest_Token($tokenValid, $tokenIdentity, $tokenMessage);
            }
        }

        $tokenMessage = "Username '{$options['username']}' and realm '{$options['realm']}' combination not found";
        return new Zend_Auth_Digest_Token($tokenValid, $tokenIdentity, $tokenMessage);
    }

    /**
     * Authenticates the realm, username and password given
     *
     * $options requires the following key-value pairs:
     *
     *      'realm'    => digest authentication realm
     *      'username' => digest authentication user
     *      'password' => password for the user of the realm
     *
     * @param  array $options
     * @uses   Zend_Auth_Digest_Adapter::staticAuthenticate()
     * @return Zend_Auth_Digest_Token
     */
    public function authenticate(array $options)
    {
        $options['filename'] = $this->_filename;

        return self::staticAuthenticate($options);
    }

}
