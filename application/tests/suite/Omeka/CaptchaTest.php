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
        $this->bootstrap = new Omeka_Test_Bootstrap;
        $this->bootstrap->getContainer()->options = array();
        Zend_Registry::set('bootstrap', $this->bootstrap);
    }

    public function tearDown()
    {
        Zend_Registry::_unsetInstance();
    }

    public function testIsConfigured()
    {
        $this->assertFalse(Omeka_Captcha::isConfigured());

        $options = array(
            Omeka_Captcha::PUBLIC_KEY_OPTION => 'public_key',
            Omeka_Captcha::PRIVATE_KEY_OPTION => 'private_key'
            );
        $this->bootstrap->getContainer()->options = $options;

        $this->assertTrue(Omeka_Captcha::isConfigured());
    }

    public function testGetCaptchaVersion2()
    {
        $options = array(
            Omeka_Captcha::PUBLIC_KEY_OPTION => 'public_key',
            Omeka_Captcha::PRIVATE_KEY_OPTION => 'private_key',
            );
        $this->bootstrap->getContainer()->options = $options;

        $captcha = Omeka_Captcha::getCaptcha();
        $this->assertNotNull($captcha);
        $this->assertInstanceOf('Ghost_Captcha_ReCaptcha2', $captcha);
    }
}
