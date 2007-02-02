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
 * @package    Zend_Controller
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * Zend_Controller_Response_Abstract
 *
 * Base class for Zend_Controller responses
 *
 * @package Zend_Controller
 * @subpackage Response
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Controller_Response_Abstract
{
    /**
     * Body content
     * @var array
     */
    protected $_body = array();

    /**
     * Exception stack
     * @var Exception
     */
    protected $_exceptions = array();

    /**
     * Array of headers. Each header is an array with keys 'name' and 'value'
     * @var array
     */
    protected $_headers = array();

    /**
     * Array of raw headers. Each header is a single string, the entire header to emit
     * @var array
     */
    protected $_headersRaw = array();

    /**
     * HTTP response code to use in headers
     * 
     * @var int
     */
    protected $_httpResponseCode = 200;

    /**
     * Whether or not to render exceptions; off by default
     * @var boolean 
     */
    protected $_renderExceptions = false;

    /**
     * Set a header
     *
     * If $replace is true, replaces any headers already defined with that
     * $name.
     *
     * @param string $name
     * @param string $value
     * @param boolean $replace
     * @return Zend_Controller_Response_Abstract
     */
    public function setHeader($name, $value, $replace = false)
    {
        $name  = (string) $name;
        $value = (string) $value;

        if ($replace) {
            foreach ($this->_headers as $key => $header) {
                if ($name == $header['name']) {
                    unset($this->_headers[$key]);
                }
            }
        }

        $this->_headers[] = array(
            'name'  => $name,
            'value' => $value
        );

        return $this;
    }

    /**
     * Set redirect URL
     *
     * Sets Location header and response code. Forces replacement of any prior 
     * redirects.
     * 
     * @param string $url 
     * @param int $code 
     * @return Zend_Controller_Response_Abstract
     */
    public function setRedirect($url, $code = 302)
    {
        $this->setHeader('Location', $url, true)
             ->setHttpResponseCode($code);

        return $this;
    }

    /**
     * Return array of headers; see {@link $_headers} for format
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->_headers;
    }

    /**
     * Clear headers
     *
     * @return Zend_Controller_Response_Abstract
     */
    public function clearHeaders()
    {
        $this->_headers = array();

        return $this;
    }

    /**
     * Set raw HTTP header
     *
     * Allows setting non key => value headers, such as status codes
     * 
     * @param string $value 
     * @return Zend_Controller_Response_Abstract
     */
    public function setRawHeader($value)
    {
        $this->_headersRaw[] = (string) $value;
        return $this;
    }

    /**
     * Retrieve all {@link setRawHeader() raw HTTP headers}
     * 
     * @return array
     */
    public function getRawHeaders()
    {
        return $this->_headersRaw;
    }

    /**
     * Clear all {@link setRawHeader() raw HTTP headers}
     * 
     * @return Zend_Controller_Response_Abstract
     */
    public function clearRawHeaders()
    {
        $this->_headersRaw = array();
        return $this;
    }

    /**
     * Clear all headers, normal and raw
     * 
     * @return Zend_Controller_Response_Abstract
     */
    public function clearAllHeaders()
    {
        return $this->clearHeaders()
                    ->clearRawHeaders();
    }

    /**
     * Set HTTP response code to use with headers
     * 
     * @param int $code 
     * @return Zend_Controller_Response_Abstract
     */
    public function setHttpResponseCode($code)
    {
        if (!is_int($code) || (100 > $code) || (599 < $code)) {
            require_once 'Zend/Controller/Response/Exception.php';
            throw new Zend_Controller_Response_Exception('Invalid HTTP response code');
        }

        $this->_httpResponseCode = $code;
        return $this;
    }

    /**
     * Retrieve HTTP response code
     * 
     * @return int
     */
    public function getHttpResponseCode()
    {
        return $this->_httpResponseCode;
    }

    /**
     * Send all headers
     *
     * Sends any headers specified. If an {@link setHttpResponseCode() HTTP response code} 
     * has been specified, it is sent with the first header.
     * 
     * @return Zend_Controller_Response_Abstract
     */
    public function sendHeaders()
    {
        if (!headers_sent()) {
            $httpCodeSent = false;
            foreach ($this->_headersRaw as $header) {
                if (!$httpCodeSent && $this->_httpResponseCode) {
                    header($header, true, $this->_httpResponseCode);
                    $httpCodeSent = true;
                } else {
                    header($header);
                }
            }
            foreach ($this->_headers as $header) {
                if (!$httpCodeSent && $this->_httpResponseCode) {
                    header($header['name'] . ': ' . $header['value'], false, $this->_httpResponseCode);
                    $httpCodeSent = true;
                } else {
                    header($header['name'] . ': ' . $header['value'], false);
                }
            }
        }

        return $this;
    }

    /**
     * Set body content
     *
     * If body content already defined, this will replace it.
     *
     * @param string $content
     * @return Zend_Controller_Response_Abstract
     */
    public function setBody($content)
    {
        $this->_body = array((string) $content);
        return $this;
    }

    /**
     * Append content to the body content
     *
     * @param string $content
     * @return Zend_Controller_Response_Abstract
     */
    public function appendBody($content)
    {
        $this->_body[] = (string) $content;
        return $this;
    }

    /**
     * Return the body content
     *
     * @param boolean $asArray Whether or not to return the body content as an 
     * array of strings or as a single string; defaults to false
     * @return string|array
     */
    public function getBody($asArray = false)
    {
        if ($asArray) {
            return $this->_body;
        }

        ob_start();
        $this->outputBody();
        return ob_get_clean();
    }

    /**
     * Echo the body segments
     * 
     * @return void
     */
    public function outputBody()
    {
        foreach ($this->_body as $content) {
            echo $content;
        }
    }

    /**
     * Register an exception with the response
     * 
     * @param Exception $e 
     * @return Zend_Controller_Response_Abstract
     */
    public function setException(Exception $e)
    {
        $this->_exceptions[] = $e;
        return $this;
    }

    /**
     * Retrieve the exception stack
     * 
     * @return array
     */
    public function getException()
    {
        return $this->_exceptions;
    }

    /**
     * Has an exception been registered with the response?
     * 
     * @return boolean
     */
    public function isException()
    {
        return !empty($this->_exceptions);
    }

    /**
     * Whether or not to render exceptions (off by default)
     *
     * If called with no arguments or a null argument, returns the value of the 
     * flag; otherwise, sets it and returns the current value.
     * 
     * @param boolean $flag Optional
     * @return boolean
     */
    public function renderExceptions($flag = null)
    {
        if (null !== $flag) {
            $this->_renderExceptions = $flag ? true : false;
        }

        return $this->_renderExceptions;
    }

    /**
     * Send the response, including all headers, rendering exceptions if so 
     * requested.
     * 
     * @return void
     */
    public function sendResponse()
    {
        $this->sendHeaders();

        if ($this->isException() && $this->renderExceptions()) {
            $exceptions = '';
            foreach ($this->getException() as $e) {
                $exceptions .= $e->__toString() . "\n";
            }
            echo $exceptions;
            return;
        }

        $this->outputBody();
    }

    /**
     * Magic __toString functionality
     *
     * Proxies to {@link sendResponse()} and returns response value as string 
     * using output buffering.
     *
     * @return string
     */
    public function __toString()
    {
        ob_start();
        $this->sendResponse();
        return ob_get_clean();
    }
}
