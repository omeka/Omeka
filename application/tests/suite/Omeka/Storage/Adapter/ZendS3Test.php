<?php

class Omeka_Storage_Adapter_ZendS3Test extends Omeka_Test_TestCase
{
    private $_options = array(
        'accessKeyId' => 'accessKey',
        'secretAccessKey' => 'secretKey',
        'bucket' => 'my-bucket'
        );

    public function testAllOptions()
    {
        $adapter = new Omeka_Storage_Adapter_ZendS3($this->_options);
        $s3 = $adapter->getS3Service();
        $this->assertInstanceOf('Zend_Service_Amazon_S3', $s3);
    }

    public function testNoOptions()
    {
        $this->setExpectedException('Omeka_Storage_Exception');
        new Omeka_Storage_Adapter_ZendS3;
    }

    public function testNoAccessKeyId()
    {
        $this->setExpectedException('Omeka_Storage_Exception');
        $options = $this->_options;
        unset($options['accessKeyId']);
        new Omeka_Storage_Adapter_ZendS3($options);
    }

    public function testNoSecretKey()
    {
        $this->setExpectedException('Omeka_Storage_Exception');
        $options = $this->_options;
        unset($options['secretAccessKey']);
        new Omeka_Storage_Adapter_ZendS3($options);
    }

    public function testNoBucket()
    {
        $this->setExpectedException('Omeka_Storage_Exception');
        $options = $this->_options;
        unset($options['bucket']);
        new Omeka_Storage_Adapter_ZendS3($options);
    }

    public function testNoEndpoint()
    {
        $options = $this->_options;
        $adapter = new Omeka_Storage_Adapter_ZendS3($options);
        $this->assertEquals('http://s3.amazonaws.com/my-bucket/test', $adapter->getUri('test'));
    }

    public function testEndpoint()
    {
        $endpoint = 'http://s3.example.com';
        $options = $this->_options;
        $options['endpoint'] = $endpoint;
        $adapter = new Omeka_Storage_Adapter_ZendS3($options);
        $this->assertEquals($endpoint . '/my-bucket/test', $adapter->getUri('test'));
    }

    /**
     * @dataProvider getUriOptionsProvider
     */
    public function testGetUri($options, $expectSignedUrl)
    {
        $adapter = new Omeka_Storage_Adapter_ZendS3($options);
        $query = parse_url($adapter->getUri('path'), PHP_URL_QUERY);

        if ($expectSignedUrl) {
            $this->assertNotNull($query);

            parse_str($query, $queryParams);
            $this->assertArrayHasKey('AWSAccessKeyId', $queryParams);
            $this->assertArrayHasKey('Expires', $queryParams);
            $this->assertArrayHasKey('Signature', $queryParams);
        } else {
            $this->assertNull($query);
        }
    }

    public function getUriOptionsProvider()
    {
        $options = $this->_options;
        return array(
            array($options, false),
            array($options + array('expiration' => '0'), false),
            array($options + array('expiration' => 'notANumber'), false),
            array($options + array('expiration' => '-10'), false),
            array($options + array('expiration' => '10'), true)
        );
    }
}
