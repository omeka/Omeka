<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2011
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @subpackage Tests
 */

/**
 * Tests the clear_filters global function.
 *
 * @see clear_filters()
 * @package Omeka
 * @subpackge Tests
 */
class Globals_ClearFiltersTest extends Omeka_Test_AppTestCase
{
    private function _getPluginBroker()
    {
        return get_plugin_broker();
    }
    
    public function testNonexistingFilter()
    {
        clear_filters('not_a_filter');
        $this->assertEmpty($this->_getPluginBroker()->getFilters('not a filter'));
    }
    
    public function testExistingFilter()
    {
        $broker = $this->_getPluginBroker();
        
        add_filter('actual_filter', 'test_callback');
        add_filter('actual_filter', 'test_callback2');
        $this->assertNotEmpty($broker->getFilters('actual_filter'));
        clear_filters('actual_filter');
        $this->assertEmpty($broker->getFilters('actual_filter'));
    }
    
    public function testAllFilters()
    {
        $broker = $this->_getPluginBroker();
        
        add_filter('filter1', 'test_callback');
        add_filter('filter2', 'test_callback');
        clear_filters();
        $this->assertEmpty($broker->getFilters('filter1'));
        $this->assertEmpty($broker->getFilters('filter2'));
    }
}
