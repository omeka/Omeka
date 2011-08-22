<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

require_once HELPERS;

/**
 * 
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2009
 */
class Omeka_Helper_PaginationLinksTest extends Omeka_Test_AppTestCase
{
    public function tearDown()
    {
        parent::tearDown();
        self::dbChanged(false);
    }

    public function testPaginationLinksAvoidXssAttack()
    {
        $attackUrl = '/items/browse/%22%3e%3cscript%3ealert(11639)%3c/script%3e';
        $escapedUrl = '/items/browse/%22%3E%3Cscript%3Ealert%2811639%29%3C/script%3E/page/5';
        
        // Have to dispatch a request in order for view script directories to 
        // be added to the View instance.
        
        $this->dispatch($attackUrl);
        Zend_Registry::set('pagination', array(
          "menu"            => NULL,
          "page"            => "2",
          "per_page"        => 2,
          "total_results"   => 10,
          "link"            => ""));
      
        $html = pagination_links();
        $this->assertContains("<a href=\"$escapedUrl\">", 
                              $html, 
                              'Should have escaped the pagination URLs to avoid XSS attack.');                
    }    
}
