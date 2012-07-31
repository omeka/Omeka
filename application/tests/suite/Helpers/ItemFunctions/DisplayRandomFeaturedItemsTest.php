<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2011
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Tests display_random_featured_items()
 * in helpers/ItemFunctions.php
 *
 * @package Omeka
 */ 
class Omeka_Helper_DisplayRandomFeaturedItemsTest extends Omeka_Test_AppTestCase
{
    public function testDisplayRandomFeaturedItems()
    {   
        $this->_createFeaturedItems();
        $html = display_random_featured_items();
        $this->assertContains('<h3><a href="/items/show/2">Title 1</a></h3>', $html);
        $this->assertContains('<p class="item-description">Description for item 1.</p>', $html);
    }
    
    public function testDisplayNoRandomFeaturedItems()
    {
        $this->_createFeaturedItems(false);
        $html = display_random_featured_items();
        $this->assertContains('<p>No featured items are available.</p>', $html);
    }

    /**
     * Creates some feature items for use in our tests.
     */
    protected function _createFeaturedItems($featured = true)
    {
        $db = $this->db;
        
        for ($i=1; $i < 6; $i++) {
            $title = "Title $i";
            $description = "Description for item $i.";

            $elementTexts = array('Dublin Core' => array(
                'Title' => array(array('text' => $title, 'html' => false)),
                'Description' => array(array('text' => $description, 'html' => false))
            ));

            $item = new Item;
            $item->public = 1;
            $item->featured = $featured ? '1' : '0';
            $item->addElementTextsByArray($elementTexts);
            $item->save();
        }
    }
}
