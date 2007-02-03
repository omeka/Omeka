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
 * @package    Zend_Gdata
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License

/**
 * Zend_Http_Client
 */
require_once 'Zend/Http/Client.php';

/**
 * Zend_Gdata_AuthException
 */
require_once 'Zend/Gdata/AuthException.php';

/**
 * Zend_Gdata_HttpException
 */
require_once 'Zend/Gdata/HttpException.php';


/**
 * Wrapper around Zend_Http_Client to facilitate Google's "Account Authentication 
 * Proxy for Web-Based Applications". 
 * 
 * @see http://code.google.com/apis/accounts/AuthForWebApps.html
 * 
 * @category   Zend
 * @package    Zend_Gdata
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_AuthSub
{
    
    const AUTHSUB_REQUEST_URI      = 'https://www.google.com/accounts/AuthSubRequest';
    
    const AUTHSUB_SESSION_TOKEN_URI = 'https://www.google.com/accounts/AuthSubSessionToken';
    
    const AUTHSUB_REVOKE_TOKEN_URI  = 'https://www.google.com/accounts/AuthSubRevokeToken';
    
    const AUTHSUB_TOKEN_INFO_URI    = 'https://www.google.com/accounts/AuthSubTokenInfo';

     /**
      * Creates a URI to request a single-use AuthSub token.
      *
      * @param string $next (required) URL identifying the service to be accessed.
      *  The resulting token will enable access to the specified service only. 
      *  Some services may limit scope further, such as read-only access.
      * @param string $scope (required) URL identifying the service to be accessed.
      *  The resulting token will enable access to the specified service only. 
      * Some services may limit scope further, such as read-only access.
      * @param int $secure (optional) Boolean flag indicating whether the authentication
      *  transaction should issue a secure token (1) or a non-secure token (0). Secure tokens
      *  are available to registered applications only.
      * @param int $session (optional) Boolean flag indicating whether the one-time-use 
      *  token may be exchanged for a session token (1) or not (0).
      */
     static public function getAuthSubTokenUri($next, $scope, $secure=0, $session=0)
     {
         $querystring = '?next=' . urlencode($next)
             . '&scope=' . urldecode($scope)
             . '&secure=' . urlencode($secure)
             . '&session=' . urlencode($session);
         return self::AUTHSUB_REQUEST_URI.$querystring;
     } 
    
    
    /**
     * Upgrades a single use token to a session token
     *
     * @param string $token
     * @throws Zend_Gdata_AuthException
     * @throws Zend_Gdata_HttpException
     */
    static public function getAuthSubSessionToken($token, $client = null)
    {
        if ($client == null) {
            $client = new Zend_Http_Client();
        }
        if (!$client instanceof Zend_Http_Client) {
            throw new Zend_Gdata_HttpException('Client is not an instance of Zend_Http_Client.');
        }
        $client->setUri(self::AUTHSUB_SESSION_TOKEN_URI);
        $headers['authorization'] = 'AuthSub token="' . $token . '"';
        $client->setHeaders($headers);
        ob_start();
        try {
            $response = $client->request('GET');
        } catch (Zend_Http_Client_Exception $e) {
            throw new Zend_Gdata_HttpException($e->getMessage(), $e);
        }
        ob_end_clean();
        // Parse Google's response
        if ($response->isSuccessful()) {
            $goog_resp = array();
            foreach (explode("\n", $response->getBody()) as $l) {
                $l = chop($l);
                if ($l) {
                    list($key, $val) = explode('=', chop($l), 2);
                    $goog_resp[$key] = $val;
                }
            }
            return $goog_resp['Token'];
        } else {
            throw new Zend_Gdata_AuthException('Token upgrade failed. Reason: ' . $response->getBody());
        }
    }

    /**
     * Revoke a token
     *
     * @param string $token
     * @return boolean
     * @throws Zend_Gdata_HttpException
     */
    static public function AuthSubRevokeToken($token, $client = null)
    {
        if ($client == null) {
            $client = new Zend_Http_Client();
        }
        if (!$client instanceof Zend_Http_Client) {
            throw new Zend_Gdata_HttpException('Client is not an instance of Zend_Http_Client.');
        }
        $client->setUri(self::AUTHSUB_REVOKE_TOKEN_URI);
        $headers['authorization'] = 'AuthSub token="' . $token . '"';
        $client->setHeaders($headers);
        ob_start();
        try {
            $response = $client->request('GET');
        } catch (Zend_Http_Client_Exception $e) {
            throw new Zend_Gdata_HttpException($e->getMessage(), $e);
        }
        ob_end_clean();
        // Parse Google's response
        if ($response->isSuccessful()) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * get token information
     *
     * @param string $token
     */
    static public function getAuthSubTokenInfo($token, $client = null)
    {
        if ($client == null) {
            $client = new Zend_Http_Client();
        }
        if (!$client instanceof Zend_Http_Client) {
            throw new Zend_Gdata_HttpException('Client is not an instance of Zend_Http_Client.');
        }
        $client->setUri(self::AUTHSUB_TOKEN_INFO_URI);
        $headers['authorization'] = 'AuthSub token="' . $token . '"';
        $client->setHeaders($headers);
        ob_start();
        try {
            $response = $client->request('GET');
        } catch (Zend_Http_Client_Exception $e) {
            throw new Zend_Gdata_HttpException($e->getMessage(), $e);
        }
        ob_end_clean();
        return $response->getBody();
    }

    static public function getHttpClient($token, $client = null)
    {
        if ($client == null) {
            $client = new Zend_Http_Client();
        }
        if (!$client instanceof Zend_Http_Client) {
            throw new Zend_Gdata_HttpException('Client is not an instance of Zend_Http_Client.');
        }
        $client->setConfig(array('strictredirects' => true));
        $headers['authorization'] = 'AuthSub token="' . $token . '"';
        $client->setHeaders($headers);
        return $client;
    }

}
