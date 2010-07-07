<?php 
class Item_Rss2Test extends Omeka_Test_AppTestCase
{
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
        $testFile->delete();
        parent::tearDown();
    }
}
