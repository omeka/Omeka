<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

require_once HELPERS;

/**
 * Test is_admin_theme().
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2009
 */
class Omeka_Helpers_IsAdminThemeTest extends PHPUnit_Framework_TestCase
{        
    private $_frontController;
      
    public function setUp()
    {
        $this->_frontController = Zend_Controller_Front::getInstance();
        $this->_frontController->resetInstance();
    }
    
    public function assertPreConditions()
    {
        $this->assertNull($this->_frontController->getParam('admin'));
    }
     
    /**
     * Starting from Omeka 1.3, is_admin_theme() should respond to a front
     * controller parameter, NOT a constant, as using a constant reduces 
     * testability to zero.
     * 
     * Since this test is flagged as an admin test, is_admin_theme() should be
     * true by default.  Then it should be false when we change the front 
     * controller param.
     */    
    public function testIsAdminThemeDependsOnFrontController()
    {
        $this->_frontController->setParam('admin', false);
        $this->assertFalse(is_admin_theme());
        
        $this->_frontController->setParam('admin', true);
        $this->assertTrue(is_admin_theme());
    }
    
    public function tearDown()
    {
        $this->_frontController->resetInstance();
    }
}
