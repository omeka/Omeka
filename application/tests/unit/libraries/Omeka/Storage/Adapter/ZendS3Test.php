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
}
