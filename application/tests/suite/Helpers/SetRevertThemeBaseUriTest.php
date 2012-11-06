<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * 
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2010
 */
class Omeka_Helpers_SetRevertThemeBaseUriTest extends Omeka_Test_AppTestCase
{
    protected $_isAdminTest = true;

    public function assertPreConditions()
    {
        $this->assertTrue(function_exists('revert_theme_base_url'));
        $this->assertTrue(function_exists('set_theme_base_url'));
    }
    
    public function testSetAndRevertBaseUri()
    {
        $this->assertTrue(defined('PUBLIC_BASE_URL'));
        $this->assertTrue(defined('ADMIN_BASE_URL'));
        $this->assertTrue(defined('CURRENT_BASE_URL'));

        $this->assertEquals(PUBLIC_BASE_URL, CURRENT_BASE_URL);

        $baseUrl = $this->frontController->getBaseUrl();
        $this->assertEquals($baseUrl, PUBLIC_BASE_URL);
        
        set_theme_base_url('admin');
        $this->assertEquals($this->frontController->getBaseUrl(), ADMIN_BASE_URL);
        
        revert_theme_base_url();
        $this->assertEquals($this->frontController->getBaseUrl(), PUBLIC_BASE_URL);
    }
}
