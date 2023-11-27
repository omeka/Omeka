<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Cloud storage adapter for Amazon S3, using Zend's built-in service.
 *
 * Caveat: Zend's storage adapter currently does not function correctly
 * with buckets that are validly-named, but use characters that cannot
 * appear in domain names.
 * 
 * @package Omeka\Storage\Adapter
 */
class Omeka_Storage_Adapter_ZendS3 implements Omeka_Storage_Adapter_AdapterInterface
{
    const AWS_KEY_OPTION = 'accessKeyId';
    const AWS_SECRET_KEY_OPTION = 'secretAccessKey';
    const ENDPOINT_OPTION = 'endpoint';
    const BUCKET_OPTION = 'bucket';
    const EXPIRATION_OPTION = 'expiration';
    const FORCE_SSL = 'forceSSL';

    /**
     * @var Zend_Service_Amazon_S3
     */
    private $_s3;

    /**
     * @var array
     */
    private $_options;

    /**
     * @var string
     */
    private $_force_ssl_display_endpoint;    

    /**
     * Set options for the storage adapter.
     *
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $this->_options = $options;

        if (array_key_exists(self::AWS_KEY_OPTION, $options)
        && array_key_exists(self::AWS_SECRET_KEY_OPTION, $options)) {
            $awsKey = $options[self::AWS_KEY_OPTION];
            $awsSecretKey = $options[self::AWS_SECRET_KEY_OPTION];
        } else {
            throw new Omeka_Storage_Exception('You must specify your AWS access key and secret key to use the ZendS3 storage adapter.');
        }

        if (!array_key_exists(self::BUCKET_OPTION, $options)) {
            throw new Omeka_Storage_Exception('You must specify an S3 bucket name to use the ZendS3 storage adapter.');
        }

        // Use Omeka_Http_Client to retry up to 3 times on timeouts
        $client = new Omeka_Http_Client;
        $client->setMaxRetries(3);
        Zend_Service_Amazon_S3::setHttpClient($client);

        $this->_s3 = new Zend_Service_Amazon_S3($awsKey, $awsSecretKey);
        if (!empty($options[self::ENDPOINT_OPTION])) {
            $this->_s3->setEndpoint($options[self::ENDPOINT_OPTION]);
        }
        
        if (isset($this->_options[self::FORCE_SSL]) && (bool) $this->_options[self::FORCE_SSL] && parse_url($this->_s3->getEndpoint(), PHP_URL_SCHEME)=='http'){
            $this->_force_ssl_display_endpoint = 'https:' . substr($this->_s3->getEndpoint(), 5);
        }        
    }

    public function setUp()
    {
        // Required by interface but does nothing, for the time being.
    }

    public function canStore()
    {
        $bucket = $this->_getBucketName();
        return $this->_s3->isBucketAvailable($bucket);
    }

    /**
     * Move a local file to S3 storage.
     *
     * @param string $source Local filesystem path to file.
     * @param string $dest Destination path.
     */
    public function store($source, $dest)
    {
        $objectName = $this->_getObjectName($dest);

        // If an expiration time is set, we're uploading private files
        // and using signed URLs. If not, we're uploading public files.
        if ($this->_getExpiration()) {
            $meta[Zend_Service_Amazon_S3::S3_ACL_HEADER] = Zend_Service_Amazon_S3::S3_ACL_PRIVATE;
        } else {
            $meta[Zend_Service_Amazon_S3::S3_ACL_HEADER] = Zend_Service_Amazon_S3::S3_ACL_PUBLIC_READ;
        }

        $status = $this->_s3->putFileStream($source, $objectName, $meta);

        if (!$status) {
            throw new Omeka_Storage_Exception('Unable to store file.');
        }

        _log("Omeka_Storage_Adapter_ZendS3: Stored '$source' as '$objectName'.");
        unlink($source);
    }

    /**
     * Move a file between two "storage" locations.
     *
     * @param string $source Original stored path.
     * @param string $dest Destination stored path.
     */
    public function move($source, $dest)
    {
        $sourceObject = $this->_getObjectName($source);
        $destObject = $this->_getObjectName($dest);

        $status = $this->_s3->moveObject($sourceObject, $destObject);

        if (!$status) {
            throw new Omeka_Storage_Exception('Unable to move file.');
        }

        _log("Omeka_Storage_Adapter_ZendS3: Moved '$sourceObject' to '$destObject'.");
    }

    /**
     * Remove a "stored" file.
     *
     * @param string $path
     */
    public function delete($path)
    {
        $objectName = $this->_getObjectName($path);

        $status = $this->_s3->removeObject($objectName);

        if (!$status) {
            if ($this->_s3->isObjectAvailable($objectName)) {
                throw new Omeka_Storage_Exception('Unable to delete file.');
            } else {
                _log("Omeka_Storage_Adapter_ZendS3: Tried to delete missing object '$objectName'.", Zend_Log::WARN);
            }
        } else {
            _log("Omeka_Storage_Adapter_ZendS3: Removed object '$objectName'.");
        }
    }

    /**
     * Get a URI for a "stored" file.
     *
     * @see http://docs.amazonwebservices.com/AmazonS3/latest/dev/index.html?RESTAuthentication.html#RESTAuthenticationQueryStringAuth
     * @param string $path
     * @return string URI
     */
    public function getUri($path)
    {
        $endpoint = isset($this->_force_ssl_display_endpoint) ? $this->_force_ssl_display_endpoint : $this->_s3->getEndpoint();
        $object = str_replace('%2F', '/', rawurlencode($this->_getObjectName($path)));

        $uri = "$endpoint/$object";

        if ($expiration = $this->_getExpiration()) {
            $timestamp = time();
            $expirationSeconds = $expiration * 60;
            $expires = $timestamp + $expirationSeconds;
            // "Chunk" expirations to allow browser caching
            $expires = $expires + $expirationSeconds - ($expires % $expirationSeconds);

            $accessKeyId = $this->_options[self::AWS_KEY_OPTION];
            $secretKey = $this->_options[self::AWS_SECRET_KEY_OPTION];

            $stringToSign = "GET\n\n\n$expires\n/$object";

            $signature = base64_encode(
                Zend_Crypt_Hmac::compute($secretKey, 'sha1', $stringToSign, Zend_Crypt_Hmac::BINARY));

            $query['AWSAccessKeyId'] = $accessKeyId;
            $query['Expires'] = $expires;
            $query['Signature'] = $signature;

            $queryString = http_build_query($query);

            $uri .= "?$queryString";
        }

        return $uri;
    }

    /**
     * Return the service object being used for S3 requests.
     *
     * @return Zend_Service_Amazon_S3
     */
    public function getS3Service()
    {
        return $this->_s3;
    }

    /**
     * Get the name of the bucket files should be stored in.
     * 
     * @return string Bucket name
     */
    private function _getBucketName()
    {
        return $this->_options[self::BUCKET_OPTION];
    }

    /**
     * Get the object name.  Zend's S3 service requires you to build the
     * object name by prepending the name of the target bucket.
     *
     * @param string $path
     * @return string Object name.
     */
    private function _getObjectName($path)
    {
        return $this->_getBucketName() . '/' . $path;
    }

    /**
     * Normalizes and returns the expiration time.
     *
     * Converts to integer and returns zero for all non-positive numbers.
     *
     * @return int
     */
    private function _getExpiration()
    {
        $expiration = (int) @$this->_options[self::EXPIRATION_OPTION];
        return $expiration > 0 ? $expiration : 0;
    }
}
