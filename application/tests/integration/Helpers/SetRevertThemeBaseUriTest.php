<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

require_once HELPERS;

/**
 * 
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2010
 */
class Omeka_Helpers_SetRevertThemeBaseUriTest extends Omeka_Test_AppTestCase
{
    protected $_isAdminTest = true;
    
    public function tearDown()
    {
        parent::tearDown();
        self::dbChanged(false);
    }

    public function assertPreConditions()
    {
        $this->assertTrue(function_exists('revert_theme_base_uri'));
        $this->assertTrue(function_exists('set_theme_base_uri'));
    }
    
    public function testSetAndRevertBaseUri()
    {
        $baseUrl = $this->frontController->getBaseUrl();
        $this->assertTrue(defined('PUBLIC_BASE_URL'));
        set_theme_base_uri('public');
        $newBaseUrl = $this->frontController->getBaseUrl();
        $this->assertTrue($baseUrl != $newBaseUrl);
        revert_theme_base_uri();
        $this->assertEquals($baseUrl, $this->frontController->getBaseUrl());
    }
}
