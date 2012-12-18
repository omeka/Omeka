<?php

class Omeka_Storage_Adapter_ZendS3Test extends PHPUnit_Framework_TestCase
{
    private $_options = array(
        'accessKeyId' => 'accessKey',
        'secretAccessKey' => 'secretKey',
        'bucket' => 'my-bucket'
        );

    public function testAllOptions()
    {
        new Omeka_Storage_Adapter_ZendS3($this->_options);
    }
    
    /**
     * @expectedException Omeka_Storage_Exception
     */
    public function testNoOptions()
    {
        new Omeka_Storage_Adapter_ZendS3;
    }

    /**
     * @expectedException Omeka_Storage_Exception
     */
    public function testNoAccessKeyId()
    {
        $options = $this->_options;
        unset($options['accessKeyId']);
        new Omeka_Storage_Adapter_ZendS3($options);
    }

    /**
     * @expectedException Omeka_Storage_Exception
     */
    public function testNoSecretKey()
    {
        $options = $this->_options;
        unset($options['secretAccessKey']);
        new Omeka_Storage_Adapter_ZendS3($options);
    }

    /**
     * @expectedException Omeka_Storage_Exception
     */
    public function testNoBucket()
    {
        $options = $this->_options;
        unset($options['bucket']);
        new Omeka_Storage_Adapter_ZendS3($options);
    }

    public function testNoEndpoint()
    {
        $options = $this->_options;
        $adapter = new Omeka_Storage_Adapter_ZendS3($options);
        $this->assertEquals('http://s3.amazonaws.com/my-bucket%2Ftest', $adapter->getUri('test'));
    }

    public function testEndpoint()
    {
        $endpoint = 'http://s3.example.com';
        $options = $this->_options;
        $options['endpoint'] = $endpoint;
        $adapter = new Omeka_Storage_Adapter_ZendS3($options);
        $this->assertEquals($endpoint . '/my-bucket%2Ftest', $adapter->getUri('test'));
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
