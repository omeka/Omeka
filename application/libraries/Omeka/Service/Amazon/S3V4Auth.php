<?php

/**
 * Subclass of Zend's S3 class, making minimal modifications to use SigV4 auth
 */
class Omeka_Service_Amazon_S3V4Auth extends Zend_Service_Amazon_S3
{
    protected $_region;
    protected $_signingKey;
    protected $_signingKeyDate;

    /**
     * Constructor
     *
     * @param string $accessKey
     * @param string $secretKey
     * @param string $region
     */
    public function __construct($accessKey=null, $secretKey=null, $region=null)
    {
        if ($region) {
            $this->_region = $region;
        } else {
            $this->_region = 'us-east-1';
        }

        parent::__construct($accessKey, $secretKey, $region);
    }
    /**
     * Put file to S3 as object, using streaming
     *
     * @param string $path   File name
     * @param string $object Object name
     * @param array  $meta   Metadata
     * @return boolean
     */
    public function putFileStream($path, $object, $meta=null)
    {
        $data = @fopen($path, "rb");
        if ($data === false) {
            /**
             * @see Zend_Service_Amazon_S3_Exception
             */
            require_once 'Zend/Service/Amazon/S3/Exception.php';
            throw new Zend_Service_Amazon_S3_Exception("Cannot open file $path");
        }

        if (!is_array($meta)) {
            $meta = array();
        }

        if (!isset($meta[self::S3_CONTENT_TYPE_HEADER])) {
            $meta[self::S3_CONTENT_TYPE_HEADER] = self::getMimeType($path);
        }

        if (!isset($meta['Content-MD5'])) {
            $meta['Content-MD5'] = base64_encode(md5_file($path, true));
        }

        if (!isset($meta['x-amz-content-sha256'])) {
            $meta['x-amz-content-sha256'] = hash_file('sha256', $path);
        }

        return $this->putObject($object, $data, $meta);
    }
    /**
     * Upload an object by a PHP string
     *
     * @param  string $object Object name
     * @param  string|resource $data   Object data (can be string or stream)
     * @param  array  $meta   Metadata
     * @return boolean
     */
    public function putObject($object, $data, $meta=null)
    {
        $object = $this->_fixupObjectName($object);
        $headers = (is_array($meta)) ? $meta : array();

        if(!is_resource($data)) {
            $headers['Content-MD5'] = base64_encode(md5($data, true));
        }
        $headers['Expect'] = '100-continue';

        if (!isset($headers[self::S3_CONTENT_TYPE_HEADER])) {
            $headers[self::S3_CONTENT_TYPE_HEADER] = self::getMimeType($object);
        }

        $response = $this->_makeRequest('PUT', $object, null, $headers, $data);

        // Etags aren't always the MD5, so rely on S3 checking against our headers
        if ($response->getStatus() == 200) {
            return true;
        }

        return false;
    }

    /**
     * Make a request to Amazon S3
     *
     * @param  string $method    Request method
     * @param  string $path        Path to requested object
     * @param  array  $params    Request parameters
     * @param  array  $headers    HTTP headers
     * @param  string|resource $data        Request data
     * @return Zend_Http_Response
     */
    public function _makeRequest($method, $path='', $params=null, $headers=array(), $data=null)
    {
        $retry_count = 0;

        if (!is_array($headers)) {
            $headers = array($headers);
        }

        if (!isset($headers['x-amz-content-sha256'])) {
            if (is_string($data)) {
                $headers['x-amz-content-sha256'] = hash('sha256', $data);
            } else if (is_resource($data)) {
                throw new Exception('sha256 is required but was not passed for a stream');
            } else {
                // body is empty, use sha256 of the empty string
                $headers['x-amz-content-sha256'] = 'e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855';
            }
        }

        if(is_resource($data) && $method != 'PUT') {
            /**
             * @see Zend_Service_Amazon_S3_Exception
             */
            require_once 'Zend/Service/Amazon/S3/Exception.php';
            throw new Zend_Service_Amazon_S3_Exception("Only PUT request supports stream data");
        }

        // Move this higher so the content-type is set when we sign the request
        if (($method == 'PUT') && ($data !== null)) {
            if (!isset($headers['Content-type'])) {
                $headers['Content-type'] = self::getMimeType($path);
            }
        }

        // build the end point out
        $parts = explode('/', $path, 2);
        $endpoint = clone($this->_endpoint);
        if ($parts[0]) {
            // prepend bucket name to the hostname
            $endpoint->setHost($parts[0].'.'.$endpoint->getHost());
        }
        if (!empty($parts[1])) {
            // ZF-10218, ZF-10122
            $pathparts = explode('?',$parts[1]);
            $endpath = $pathparts[0];
            $endpoint->setPath('/'.$endpath);
            
        }
        else {
            $endpoint->setPath('/');
            if ($parts[0]) {
                $path = $parts[0].'/';
            }
        }
        // the client will add the Host header for us, but we need to sign it
        $headers['host'] = $endpoint->getHost();
        self::addSignature($method, $endpoint->getPath(), $headers);
        unset($headers['host']);

        $client = self::getHttpClient();

        $client->resetParameters(true);
        $client->setUri($endpoint);
        $client->setAuth(false);
        $client->setHeaders($headers);

        if (is_array($params)) {
            foreach ($params as $name=>$value) {
                $client->setParameterGet($name, $value);
            }
        }

        if (($method == 'PUT') && ($data !== null)) {
            $client->setRawData($data, $headers['Content-type']);
        }
        do {
            $retry = false;

            $response = $client->request($method);
            $response_code = $response->getStatus();

            // Some 5xx errors are expected, so retry automatically
            if ($response_code >= 500 && $response_code < 600 && $retry_count <= 5) {
                $retry = true;
                $retry_count++;
                sleep($retry_count / 4 * $retry_count);
            }
            else if ($response_code == 307) {
                // Need to redirect, new S3 endpoint given
                // This should never happen as Zend_Http_Client will redirect automatically
            }
            else if ($response_code == 100) {
                // echo 'OK to Continue';
            }
        } while ($retry);

        return $response;
    }

    /**
     * Add the S3 Authorization signature to the request headers
     *
     * @param  string $method
     * @param  string $path
     * @param  array &$headers
     * @return string
     */
    protected function addSignature($method, $path, &$headers)
    {
        $sha256 = $headers['x-amz-content-sha256'];

        $timestamp = gmdate('Ymd\THis\Z');
        $date = substr($timestamp, 0, 8);
        $region = $this->_region;

        // set date here so the request matches the signature
        $headers['x-amz-date'] = $timestamp;

        $canonicalURI = parse_url($path, PHP_URL_PATH);

        $query = parse_url($path, PHP_URL_QUERY);
        $canonicalQueryString = '';
        if ($query) {
            parse_str($query, $queryArr);
            ksort($queryArr);
            foreach ($queryArr as $key => $value) {
                $canonicalQueryString .= rawurlencode($key) . '=' . rawurlencode($value) . '&';
            }
            $canonicalQueryString = substr($canonicalQueryString, 0, -1);
        }

        $canonicalHeadersArr = array();
        foreach ($headers as $header => $value) {
            $lowerHeader = strtolower($header);
            $canonicalHeadersArr[$lowerHeader] = $lowerHeader . ':' . trim($value);
        }
        ksort($canonicalHeadersArr);
        $canonicalHeaders = implode("\n", $canonicalHeadersArr) . "\n";
        $signedHeaders = implode(';', array_keys($canonicalHeadersArr));

        $signature = $this->_getSignature($method, $canonicalURI, $canonicalQueryString, $canonicalHeaders, $signedHeaders, $sha256, $timestamp, $date, $region);

        $headers['Authorization'] = 'AWS4-HMAC-SHA256 Credential=' . $this->_getAccessKey() . "/$date/$region/s3/aws4_request,SignedHeaders=$signedHeaders,Signature=$signature";
    }

    /**
     * Get the query string for a presigned SigV4 URL for the given path
     *
     * @param string $path Path portion of the URL (including the leading slash)
     * @param int $expires Time, in seconds, the URL should be valid for (max is 7 days)
     * @return string
     */
    public function getPresignedURLQuery($path, $expires)
    {
        $timestamp = gmdate('Ymd\THis\Z');
        $date = substr($timestamp, 0, 8);
        $region = $this->_region;

        $accessKey = rawurlencode($this->_getAccessKey());
        $region = rawurlencode($this->_region);
        $expires = (int) $expires;

        $query = "X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=$accessKey%2F$date%2F$region%2Fs3%2Faws4_request&X-Amz-Date=$timestamp&X-Amz-Expires=$expires&X-Amz-SignedHeaders=host";
        $headers = 'host:' . $this->_endpoint->getHost() . "\n";
        $signature = $this->_getSignature('GET', $path, $query, $headers, 'host', 'UNSIGNED-PAYLOAD', $timestamp, $date, $region);
        $query .= "&X-Amz-Signature=$signature";

        return $query;
    }

    /**
     * Get signature for a given request
     *
     * @param string $method
     * @param string $canonicalURI
     * @param string $canonicalQueryString
     * @param string $canonicalHeaders
     * @param string $signedHeaders
     * @param string $sha256
     * @param string $timestamp
     * @param string $date
     * @param string $region
     * @return string
     */
    protected function _getSignature($method, $canonicalURI, $canonicalQueryString, $canonicalHeaders, $signedHeaders, $sha256, $timestamp, $date, $region)
    {
        $canonicalRequestHash = hash('sha256', "$method\n$canonicalURI\n$canonicalQueryString\n$canonicalHeaders\n$signedHeaders\n$sha256");

        $scope = "$date/$region/s3/aws4_request";
        $stringToSign = "AWS4-HMAC-SHA256\n$timestamp\n$scope\n$canonicalRequestHash";

        // Signing key is the same request-to-request as long as it's the same date
        if (!($this->_signingKey && $date === $this->_signingKeyDate)) {
            $dateKey = hash_hmac('sha256', $date, 'AWS4' . $this->_getSecretKey(), true);
            $dateRegionKey = hash_hmac('sha256', $region, $dateKey, true);
            $dateRegionServiceKey = hash_hmac('sha256', 's3', $dateRegionKey, true);
            $this->_signingKey = hash_hmac('sha256', 'aws4_request', $dateRegionServiceKey, true);
            $this->_signingKeyDate = $date;
        }

        return hash_hmac('sha256', $stringToSign, $this->_signingKey);
    }
}
