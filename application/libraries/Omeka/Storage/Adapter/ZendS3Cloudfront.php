<?php
/**
 * Cloud storage adapter for Amazon S3, serving through CloudFront.
 *
 * @package Omeka\Storage\Adapter
 */
class Omeka_Storage_Adapter_ZendS3Cloudfront extends Omeka_Storage_Adapter_ZendS3
{
    const CLOUDFRONT_DOMAIN = 'cloudfrontDomain';
    const CLOUDFRONT_KEY_ID = 'cloudfrontKeyId';
    const CLOUDFRONT_KEY_PATH = 'cloudfrontKeyPath';
    const CLOUDFRONT_KEY_PASSPHRASE = 'cloudfrontKeyPassphrase';

    /**
     * @var string
     */
    private $_cloudfrontDomain;

    /**
     * @var string
     */
    private $_cloudfrontKeyId;

    /**
     * @var string
     */
    private $_cloudfrontKeyPath;

    /**
     * @var string
     */
    private $_cloudfrontKeyPassphrase = '';

    /**
     * Set options for the storage adapter.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        parent::__construct($options);

        if (isset($options[self::CLOUDFRONT_DOMAIN])) {
            $this->_cloudfrontDomain = $options[self::CLOUDFRONT_DOMAIN];
        } else {
            throw new Omeka_Storage_Exception('The cloudfrontDomain storage option is required');
        }

        if (isset($options[self::CLOUDFRONT_KEY_ID])) {
            $this->_cloudfrontKeyId = $options[self::CLOUDFRONT_KEY_ID];
        }

        if (isset($options[self::CLOUDFRONT_KEY_PATH])) {
            $this->_cloudfrontKeyPath = $options[self::CLOUDFRONT_KEY_PATH];
        }

        if (isset($options[self::CLOUDFRONT_KEY_PASSPHRASE])) {
            $this->_cloudfrontKeyPassphrase = $options[self::CLOUDFRONT_KEY_PASSPHRASE];
        }

        if ($this->_getExpiration() && !($this->_cloudfrontKeyId && $this->_cloudfrontKeyPath)) {
            throw new Omeka_Storage_Exception('The cloudfrontKeyId and cloudfrontKeyPath storage options are required when enabling expiration');
        }
    }

    /**
     * Get a URI to a stored file from CloudFront
     */
    public function getUri($path)
    {
        $object = str_replace('%2F', '/', rawurlencode($path));
        $uri = "https://{$this->_cloudfrontDomain}/{$object}";

        if ($expiration = $this->_getExpiration()) {
            $timestamp = time();
            $expirationSeconds = $expiration * 60;
            $expires = $timestamp + $expirationSeconds;
            // "Chunk" expirations to allow browser caching
            $expires = $expires + $expirationSeconds - ($expires % $expirationSeconds);

            $statement = json_encode([
                'Statement' => [
                    [
                        'Resource' => $uri,
                        'Condition' => [
                            'DateLessThan' => [
                                'AWS:EpochTime' => $expires,
                            ],
                        ],
                    ],
                ],
            ], JSON_UNESCAPED_SLASHES);

            $key = openssl_pkey_get_private('file://' . $this->_cloudfrontKeyPath, $this->_cloudfrontKeyPassphrase);
            if (!$key) {
                throw new Omeka_Storage_Exception("Unable to load key for Cloudfront\n\n" . $this->_getOpensslErrors());
            }

            $result = openssl_sign($statement, $signature, $key);
            if (!$result) {
                throw new Omeka_Storage_Exception("Failed to create Cloudfront URI signature\n\n" . $this->_getOpensslErrors());
            }

            unset($key);

            $signatureParam = strtr(base64_encode($signature), '+=/', '-_~');

            $query['Expires'] = $expires;
            $query['Signature'] = $signatureParam;
            $query['Key-Pair-Id'] = $this->_cloudfrontKeyId;

            $queryString = http_build_query($query);

            $uri .= "?$queryString";
        }

        return $uri;
    }

    private function _getOpensslErrors()
    {
        $errors = [];
        while (($error = openssl_error_string()) !== false) {
            $errors[] = $error;
        }
        return implode("\n", $errors);
    }
}
