<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2011
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Test Omeka_Http_Client.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2011
 */
class Omeka_Http_ClientTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->uri = 'http://local.test';
        $this->client = new Omeka_Http_Client($this->uri);
        $this->exception = new Zend_Http_Client_Adapter_Exception(
            "Read timeout", 
            Zend_Http_Client_Adapter_Exception::READ_TIMEOUT
        );
        $this->exceptionAdapter = $this
            ->getMockBuilder('Zend_Http_Client_Adapter_Test')
            ->getMock();
    }

    /**
     * @expectedException Zend_Http_Client_Adapter_Exception
     */
    public function testNoRetryByDefault()
    {
        $this->exceptionAdapter->expects($this->any())
            ->method('connect')
            ->will($this->throwException($this->exception));
        $this->client->setAdapter($this->exceptionAdapter);
        $response = $this->client->request('GET');
    }

    /**
     * @expectedException Zend_Http_Client_Adapter_Exception
     */
    public function testExceedMaxRetries()
    {
        $this->exceptionAdapter->expects($this->exactly(6))
            ->method('connect')
            ->will($this->throwException($this->exception));
        $this->client->setMaxRetries(5);
        $this->client->setAdapter($this->exceptionAdapter);
        $this->client->request('GET');
    }

    public function testRetryThenSucceed()
    {
        $expectedResponse = new Zend_Http_Response(
            200,
            array('header' => 'a header'),
            'body'
        );
        $this->exceptionAdapter->expects($this->at(0))
            ->method('connect')
            ->will($this->throwException($this->exception));
        $this->exceptionAdapter->expects($this->at(1))
            ->method('connect')
            ->will($this->throwException($this->exception));
        $this->exceptionAdapter->expects($this->any())
            ->method('read')
            ->will($this->returnValue($expectedResponse));
        $this->client->setMaxRetries(5);
        $this->client->setAdapter($this->exceptionAdapter);
        $response = $this->client->request('GET');
        $this->assertEquals($response, $expectedResponse);
    }
}
