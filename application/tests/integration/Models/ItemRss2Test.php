<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Test ItemRss2 model class.
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2007-2010
 */
class Omeka_Model_ItemRss2Test extends Omeka_Test_AppTestCase
{
    protected $_useAdminViews = false;
    
    public function setUp()
    {
        parent::setUp();
        
        set_option(File::DISABLE_DEFAULT_VALIDATION_OPTION, 1);
        
        $item = insert_item(
            array('public'=>true), 
            array('Dublin Core'=>array('Title'=>array(array('text'=>'Item Title', 'html'=>true)))));
        
        $fileUrl = TEST_DIR . '/_files/test.txt';
        $files = insert_files_for_item($item, 'Filesystem', array($fileUrl));
        
        $_SERVER['HTTP_HOST'] = 'localhost';
    }
    
    public function assertPreConditions()
    {
        $this->assertEquals(1, $this->db->getTable('Item')->count(),
            "There should be one item in the database.");
        $this->assertEquals(1, $this->db->getTable('File')->count(),
            "There should be one file in the database.");
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
    
    public function tearDown()
    {
        // Delete the physical files that were ingested in setUp().
        $testFile = $this->db->getTable('File')->find(1);
        if ($testFile instanceof File) {
            $testFile->delete();
        }
        parent::tearDown();
    }
}
