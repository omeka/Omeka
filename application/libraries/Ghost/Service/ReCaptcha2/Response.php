<?php

class Ghost_Service_ReCaptcha2_Response
{
    /**
     * Status
     *
     * true if the response is valid or false otherwise
     *
     * @var boolean
     */
    protected $_status = null;

    /**
     * Error codes
     *
     * The error codes if the status is false. The different error codes can be found in the
     * recaptcha API docs.
     *
     * @var array
     */
    protected $_errorCodes = array();

    /**
     * Class constructor used to construct a response
     *
     * @param string $status
     * @param array $errorCodes
     * @param Zend_Http_Response $httpResponse If this is set the content will override $status and $errorCode
     */
    public function __construct($status = null, array $errorCodes = null, Zend_Http_Response $httpResponse = null)
    {
        if ($status !== null) {
            $this->setStatus($status);
        }

        if ($errorCodes !== null) {
            $this->setErrorCodes($errorCodes);
        }

        if ($httpResponse !== null) {
            $this->setFromHttpResponse($httpResponse);
        }
    }

    /**
     * Set the status
     *
     * @param string $status
     * @return Ghost_Service_ReCaptcha2_Response
     */
    public function setStatus($status)
    {
        $this->_status = (bool) $status;

        return $this;
    }

    /**
     * Get the status
     *
     * @return boolean
     */
    public function getStatus()
    {
        return $this->_status;
    }

    /**
     * Alias for getStatus()
     *
     * @return boolean
     */
    public function isValid()
    {
        return $this->getStatus();
    }

    /**
     * Set the error codes
     *
     * @param array $errorCodes
     * @return Ghost_Service_ReCaptcha2_Response
     */
    public function setErrorCodes($errorCodes)
    {
        $this->_errorCodes = $errorCodes;

        return $this;
    }

    /**
     * Get the error codes
     *
     * @return string
     */
    public function getErrorCodes()
    {
        return $this->_errorCodes;
    }

    /**
     * Populate this instance based on a Zend_Http_Response object
     *
     * @param Zend_Http_Response $response
     * @return Zend_Service_ReCaptcha_Response
     */
    public function setFromHttpResponse(Zend_Http_Response $response)
    {
        $body = Zend_Json::decode($response->getBody());

        // Default status and error code
        $status = false;
        $errorCodes = array();

        if (!empty($body['success']) && is_bool($body['success'])) {
            $status = $body['success'];
        }

        if (!empty($body['error-codes']) && is_array($body['error-codes'])) {
            $errorCodes = $body['error-codes'];
        }

        $this->setStatus($status);
        $this->setErrorCodes($errorCodes);

        return $this;
    }
}
