<?php 
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Test ItemRss2 model class.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Omeka_Model_ItemRss2Test extends Omeka_Test_AppTestCase
{
    protected $_isAdminTest = false;
    
    public function setUp()
    {
        parent::setUp();
        self::dbChanged(false);
    }
    
    public function assertPreConditions()
    {
        $this->assertEquals(1, $this->db->getTable('Item')->count(),
            "There should be one item in the database.");
    }
    
    public function testCanGetValidItemRss2Output()
    {   
        $this->dispatch('items/browse?output=rss2');
        $string = $this->response->getBody();
         
        try {
            $feed = Zend_Feed::importString($string);
        } catch (Zend_Feed_Exception $e) {
            $this->fail("Feed does not load properly.");        
        }
    }
    
    public function testCanGetItemRss2OutputItem()
    {   
        $this->dispatch('items/browse?output=rss2');
        $string = $this->response->getBody();
         
        try {
            $feed = Zend_Feed::importString($string);
        } catch (Zend_Feed_Exception $e) {
            $this->fail("Feed does not load properly.");        
        }
        
        $item = $feed->current();
        if(!$item) {
            $this->fail("Feed does not have an item");
        }
    }
}
