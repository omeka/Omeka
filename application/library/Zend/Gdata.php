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
 */

/**
 * Zend_Feed
 */
require_once 'Zend/Feed.php';

/**
 * Zend_Gdata_Exception
 */
require_once 'Zend/Gdata/Exception.php';

/**
 * Zend_Gdata_HttpException
 */
require_once 'Zend/Gdata/HttpException.php';

/**
 * Zend_Gdata_InvalidArgumentException
 */
require_once 'Zend/Gdata/InvalidArgumentException.php';

/**
 *
 * @link http://code.google.com/apis/gdata/overview.html
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata
{
    /**
     * Client object used to communicate
     *
     * @var Zend_Http_Client
     */
    protected $_httpClient;

    /**
     * Query parameters.
     *
     * @var array
     */
    protected $_params = array();

    /**
     * Default URI to which to POST.
     *
     * @var string
     */
    protected $_defaultPostUri = null;

    /**
     * Create Gdata object
     *
     * @param Zend_Http_Client $client
     */
    public function __construct($client = null)
    {
        $this->setHttpClient($client);
    }

    /**
     * @return string querystring
     */
    protected function getQueryString()
    {
        $queryArray = array();
        foreach ($this->_params as $name => $value) {
            if (substr($name, 0, 1) == '_') {
                continue;
            }
            $queryArray[] = urlencode($name) . '=' . urlencode($value);
        }
        if (count($queryArray) > 0) {
            return '?' . implode('&', $queryArray);
        } else {
            return '';
        }
    }

    /**
     *
     */
    public function resetParameters()
    {
        $this->_params = array();
    }

    /**
     * Retreive feed object
     *
     * @param string $uri
     * @return Zend_Feed
     */
    public function getFeed($uri)
    {
        $feed = new Zend_Feed();
        $this->_httpClient->resetParameters();
        $feed->setHttpClient($this->_httpClient);
        return $feed->import($uri);
    }

    /**
     * @return Zend_Http_Client
     */
    public function getHttpClient()
    {
        return $this->_httpClient;
    }

    /**
     * @param Zend_Http_Client $client
     * @throws Zend_Gdata_HttpException
     */
    public function setHttpClient($client)
    {
        if ($client == null) {
            $client = new Zend_Http_Client();
        }
        if (!$client instanceof Zend_Http_Client) {
            throw new Zend_Gdata_HttpException('Argument is not an instance of Zend_Http_Client.');
        }
        $client->setConfig(array('strictredirects' => true));
        $this->_httpClient = $client;
    }

    /**
     * POST xml data to Google with authorization headers set
     *
     * @param string $xml
     * @param string $uri POST URI
     * @return Zend_Http_Response
     * @throws Zend_Gdata_Exception
     * @throws Zend_Gdata_HttpException
     * @throws Zend_Gdata_InvalidArgumentException
     */
    public function post($xml, $uri = null)
    {
        if ($uri == null) {
            $uri = $this->_defaultPostUri;
        }
        if ($uri == null) {
            throw new Zend_Gdata_InvalidArgumentException('You must specify an URI to which to post.');
        }
        $this->_httpClient->setUri($uri);
        $this->_httpClient->setConfig(array('maxredirects' => 0));
        $this->_httpClient->setRawData($xml,'application/atom+xml');
        try {
            $response = $this->_httpClient->request('POST');
        } catch (Zend_Http_Client_Exception $e) {
            throw new Zend_Gdata_HttpException($e->getMessage(), $e);
        }
        /**
         * set "S" cookie to avoid future redirects.
         */
        if($cookie = $response->getHeader('Set-cookie')) {
            list($cookieName, $cookieValue) = explode('=', $cookie, 2);
            $this->_httpClient->setCookie($cookieName, $cookieValue);
        }
        if ($response->isRedirect()) {
            /**
             * Re-POST with redirected URI.
             * This happens frequently.
             */
            $this->_httpClient->setUri($response->getHeader('Location'));
            $this->_httpClient->setRawData($xml,'application/atom+xml');
            try {
                $response = $this->_httpClient->request('POST');
            } catch (Zend_Http_Client_Exception $e) {
                throw new Zend_Gdata_HttpException($e->getMessage(), $e);
            }
        }
        
        if (!$response->isSuccessful()) {
            throw new Zend_Gdata_Exception('Post to Google failed. Reason: ' . $response->getBody());
        }
        return $response;
    }

    /**
     * Delete an entry by its ID uri
     *
     * @param string $uri
     */
    public function delete($uri)
    {
        $feed = $this->getFeed($uri);
        $entry = $feed->current();
        $entry->delete();
        return true;
    }

    /**
     * @param string $ale
     */
    public function setAlt($value)
    {
        $this->alt = $value;
    }

    /**
     * @param int $value
     */
    public function setMaxResults($value)
    {
        $this->maxResults = $value;
    }

    /**
     * @param string $value
     */
    public function setQuery($value)
    {
        $this->query = $value;
    }

    /**
     * @param int $value
     */
    public function setStartIndex($value)
    {
        $this->startIndex = $value;
    }

    /**
     * @param int $value
     */
    public function setUpdatedMax($value)
    {
        $this->updatedMax = $value;
    }

    /**
     * @param int $value
     */
    public function setUpdatedMin($value)
    {
        $this->updatedMin = $value;
    }

    /**
     * @return string rss or atom
     */
    public function getAlt()
    {
        return $this->alt;
    }

    /**
     * @return int maxResults
     */
    public function getMaxResults()
    {
        return $this->maxResults;
    }

    /**
     * @return string query
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @return int startIndex
     */
    public function getStartIndex()
    {
        return $this->startIndex;
    }

    /**
     * @return int updatedMax
     */
    public function getUpdatedMax()
    {
        return $this->updatedMax;
    }

    /**
     * @return int updatedMin
     */
    public function getUpdatedMin()
    {
        return $this->updatedMin;
    }

    /**
     * @param string $var
     * @param string $value
     */
    protected function __set($var, $value)
    {
        switch ($var) {
            case 'query':
                $var = 'q';
                break;
            case 'maxResults':
                $var = 'max-results';
                $value = intval($value);
                break;
            case 'startIndex':
                $var = 'start-index';
                $value = intval($value);
                break;
            case 'updatedMin':
                $var = 'updated-min';
                $value = $this->formatTimestamp($value);
                break;
            case 'updatedMax':
                $var = 'updated-max';
                $value = $this->formatTimestamp($value);
                break;
            default:
                // other params may be set by subclasses
                break;
        }
        $this->_params[$var] = $value;
    }

    /**
     *  Convert timestamp into RFC 3339 date string.
     *  2005-04-19T15:30:00
     *
     * @param int $timestamp
     */
    public function formatTimestamp($timestamp)
    {
        if (ctype_digit($timestamp)) {
            return date('Y-m-d\TH:i:s', $timestamp);
        } else {
            $ts = strtotime($timestamp);
            if ($ts === false) {
                throw new Zend_Gdata_InvalidArgumentException("Invalid timestamp: $timestamp.");
            }
            return date('Y-m-d\TH:i:s', $ts);
        }
    }

    /**
     * @param string $var
     * @return mixed property value
     */
    protected function __get($var)
    {
        switch ($var) {
            case 'query':
                $var = 'q';
                break;
            case 'maxResults':
                $var = 'max-results';
                break;
            case 'startIndex':
                $var = 'start-index';
                break;
            case 'updatedMin':
                $var = 'updated-min';
                break;
            case 'updatedMax':
                $var = 'updated-max';
                break;
            default:
                // other params may be set by subclasses
                break;
        }
        return isset($this->_params[$var]) ? $this->_params[$var] : null;
    }

    /**
     * @param string $var
     * @return bool
     */
    protected function __isset($var)
    {
        switch ($var) {
            case 'query':
                $var = 'q';
                break;
            case 'maxResults':
                $var = 'max-results';
                break;
            case 'startIndex':
                $var = 'start-index';
                break;
            case 'updatedMin':
                $var = 'updated-min';
                break;
            case 'updatedMax':
                $var = 'updated-max';
                break;
            default:
                // other params may be set by subclasses
                break;
        }
        return isset($this->_params[$var]);
    }

    /**
     * @param string $var
     */
    protected function __unset($var)
    {
        switch ($var) {
            case 'query':
                $var = 'q';
                break;
            case 'maxResults':
                $var = 'max-results';
                break;
            case 'startIndex':
                $var = 'start-index';
                break;
            case 'updatedMin':
                $var = 'updated-min';
                break;
            case 'updatedMax':
                $var = 'updated-max';
                break;
            default:
                // other params may be set by subclasses
                break;
        }
        unset($this->_params[$var]);
    }

}

