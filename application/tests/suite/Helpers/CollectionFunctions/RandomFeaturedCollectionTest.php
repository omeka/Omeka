<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2011
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Tests random_featured_collection()
 *
 * @package Omeka
 */
class Omeka_Helper_RandomFeaturedCollectionTest extends Omeka_Test_AppTestCase
{

    /**
     * Checks that the correct message is displayed if there are no featured 
     * collections to display.
     */
    public function testNoRandomFeaturedCollection()
    {
        $this->dispatch('/');
        $html = random_featured_collection();
        $this->assertContains('No featured collections are available.', $html);
    }

}
