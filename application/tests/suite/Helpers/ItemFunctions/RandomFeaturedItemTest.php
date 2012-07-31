<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2011
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Tests random_featured_item()
 * in helpers/ItemFunctions.php
 *
 * @package Omeka
 */ 
class Omeka_Helper_RandomFeaturedItemTest extends Omeka_Test_AppTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->_createFeaturedItems();
    }
    
    public function testRandomFeaturedItem()
    {   
        $randomFeaturedItem = random_featured_item();
        $this->assertEquals(1, count($randomFeaturedItem));
        
        $randomFeaturedItemWithImage = random_featured_item(true);
        $this->assertEquals(1, count($randomFeaturedItemWithImage));
        $this->assertTrue(item_has_files($randomFeaturedItemWithImage));
    }
    
    /**
     * Creates some feature items for use in our tests. Specifically, it creates
     * 10 public featured items without images, 10 public non-featured items
     * without images, and 10 public features items with images.
     */
    protected function _createFeaturedItems()
    {
        $db = $this->db;
        
        for ($i=0; $i < 10; $i++) { 
            $item = new Item;
            $item->featured = 1;
            $item->public = 1;
            $item->save();
        }
        
        for ($i=0; $i < 10; $i++) { 
            $item = new Item;
            $item->featured = 0;
            $item->public = 1;
            $item->save();
        }
        
        for ($i=0; $i < 10; $i++) {
            $item = new Item;
            $item->featured = 1;
            $item->public = 1;
            $item->save();
            
            $db->insert('File', array(
                'has_derivative_image' => '1',
                'archive_filename' => 'file'.$i,
                'original_filename' => 'file'.$i,
                'size' => 0,
                'item_id' => $item->id
            ));
        }
    }
}
