<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Omeka_CaptchaTest extends PHPUnit_Framework_TestCase
{   
    public function setUp()
    {
        $this->context = Omeka_Context::getInstance();
        $this->context->setOptions = array();
    }

    public function tearDown()
    {
        Omeka_Context::resetInstance();
    }

    public function testIsConfigured()
    {
        $this->assertFalse(Omeka_Captcha::isConfigured());

        $options = array(
            Omeka_Captcha::PUBLIC_KEY_OPTION => 'public_key',
            Omeka_Captcha::PRIVATE_KEY_OPTION => 'private_key'
            );
        $this->context->setOptions($options);

        $this->assertTrue(Omeka_Captcha::isConfigured());
    }

    public function testGetCaptcha()
    {
        $this->assertNull(Omeka_Captcha::getCaptcha());

        $options = array(
            Omeka_Captcha::PUBLIC_KEY_OPTION => 'public_key',
            Omeka_Captcha::PRIVATE_KEY_OPTION => 'private_key'
            );
        $this->context->setOptions($options);

        $captcha = Omeka_Captcha::getCaptcha();
        $this->assertNotNull($captcha);
        $this->assertInstanceOf('Zend_Captcha_Adapter', $captcha);
    }
}
