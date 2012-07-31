<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

class Globals_ElementExistsTest extends Omeka_Test_AppTestCase
{   
    public function tearDown()
    {
        parent::tearDown();
        self::dbChanged(false);
    }

    public function testExistingElementOnExistingElementSet()
    {
        $this->assertTrue(element_exists('Dublin Core', 'Title'));
    }
    
    public function testNonExistingElementOnExistingElementSet()
    {
        $this->assertFalse(element_exists('Dublin Core', 'This Element Should Not Exist'));
    }
    
    public function testNonExistingElementSet()
    {
        $this->assertFalse(element_exists('Non Existing Element Set', 'Title'));
    }
    
    public function testLowerCaseElementName()
    {
        $this->assertFalse(element_exists('Dublin Core', 'title'));
    }
}
