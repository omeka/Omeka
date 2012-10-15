<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Wrapper for Zend_Http_Client.
 *
 * Adds the following functionality: retries on timeouts.
 * 
 * @package Omeka\Http
 */
class Omeka_Http_Client extends Zend_Http_Client
{
    /**
     * @var integer
     */
    private $_maxRetries = 0;

    /**
     * @var integer
     */
    private $_retryCount = 0;

    /**
     * Wraps Zend_Http_Client to automatically retry timed out requests.
     * 
     * @see Zend_Http_Client::request()
     */
    public function request($method = null)
    {
        try {
            $resp = parent::request($method);
            $this->_retryCount = 0;
            return $resp;
        } catch (Zend_Http_Client_Adapter_Exception $e) {
            if ($e->getCode() == Zend_Http_Client_Adapter_Exception::READ_TIMEOUT) {
                if ($this->_retryCount < $this->_maxRetries) {
                    $this->_retryCount++;
                    return $this->request($method);
                } else {
                    $this->_retryCount = 0;
                    throw $e;
                }
            } else {
                throw $e;
            }
        }
    }

    /**
     * Set the maximum number of retries to make when a request times out.
     *
     * @param integer $count
     */
    public function setMaxRetries($count)
    {
        $this->_maxRetries = $count;
    }
}
