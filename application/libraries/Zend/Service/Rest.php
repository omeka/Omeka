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
 * @package    Zend_Service
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * Zend_Service_Abstract
 */
require_once 'Zend/Service/Abstract.php';


/**
 * @category   Zend
 * @package    Zend_Service
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Rest extends Zend_Service_Abstract
{
    /**
     * Zend_Uri of this web service
     *
     * @var Zend_Uri_Http
     */
    protected $_uri = null;


	/**
	 * Call a remote REST web service URI and return the Zend_Http_Response object
	 *
	 * @param  string $path            The path to append to the URI
	 * @throws Zend_Service_Exception
	 * @return void
	 */
	final private function _prepareRest($path, $query)
	{
		// Get the URI object and configure it
		if (!$this->_uri instanceof Zend_Uri) {
		    throw new Zend_Service_Exception('URI object must be set before performing call');
		}

		// @todo this needs to be revisited
		if ($path[0] != '/' && $this->_uri[strlen($this->_uri)-1] != '/') {
			$path = '/' . $path;
		}

		$this->_uri->setPath($path);

		if (!is_null($query)) {
			$this->_uri->setQuery($query);
		}

		/**
		 * Get the HTTP client and configure it for the endpoint URI.  Do this each time
		 * because the Zend_Http_Client instance is shared among all Zend_Service_Abstract subclasses.
		 */
		self::getHttpClient()->setUri($this->_uri);
	}


	/**
	 * Sets the URI for the web service.
	 *
	 * @param string|Zend_Uri $uri
	 * @return Zend_Uri
	 */
	final public function setUri($uri)
	{
	    // Set the URI object from a string or ZUri
		self::getHttpClient()->setUri($uri);

		// Save the ZUri for this URI and return it.
	    return $this->_uri = self::getHttpClient()->getUri();
	}


	/**
	 * Performs an HTTP GET request to the $path.
	 *
	 * @param string $path
	 * @return Zend_Http_Response
	 */
	final public function restGet($path, $query = null)
	{
	   $this->_prepareRest($path, $query);
	   return self::getHttpClient()->request('GET');
	}


	/**
	 * Performs an HTTP POST request to $path.
	 *
	 * @param string $path
	 * @param array $data
	 * @return Zend_Http_Response
	 */
	final public function restPost($path, $data)
	{
	   $this->_prepareRest($path);
	   $this->_uri->setQuery($data);
	   self::getHttpClient()->setRawData($data);
	   return self::getHttpClient()->request('POST');
	}


	/**
	 * Performs an HTTP PUT request to $path.
	 *
	 * @param string $path
	 * @param array $data
	 * @return Zend_Http_Response
	 */
	final public function restPut($path, $data)
	{
	   $this->_prepareRest($path);
	   $this->_uri->setQuery($data);
	   self::getHttpClient()->setRawData($data);
	   return self::getHttpClient()->request('PUT');
	}


	/**
	 * Performs an HTTP DELETE request to $path.
	 *
	 * @param string $path
	 * @return Zend_Http_Response
	 */
	final public function restDelete($path)
	{
	   $this->_prepareRest($path);
	   return self::getHttpClient()->request('DELETE');
	}

}

