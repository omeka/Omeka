<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

require_once HELPERS;

/**
 * 
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2009
 **/
class Omeka_Helpers_IsAdminThemeTest extends Omeka_Test_AppTestCase
{       
    protected $_isAdminTest = true;
    
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
        $this->assertTrue(is_admin_theme());
        $this->frontController->setParam('admin', false);
        $this->assertFalse(is_admin_theme());
    }
}