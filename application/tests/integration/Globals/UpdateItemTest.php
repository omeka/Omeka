<?php
/**
 * @version $Id$
 * @copyright Roy Rosenzweig Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

class Globals_UpdateItemTest extends Omeka_Test_AppTestCase
{   
    public function setUp()
    {
       parent::setUp();
       $this->item = insert_item(array('public' => false));       
    }
    
    public function assertPreConditions()
    {
        $this->assertTrue($this->item->exists());
    }
    
    public function testCanUpdateItem()
    {
        $this->item = update_item($this->item, array('public' => true));
        
        $isPublic = $this->db->fetchOne("SELECT public FROM {$this->db->Item} WHERE id = {$this->item->id}");
        $this->assertEquals(1, $isPublic,
            "Item should have been changed to public via update_item().");
    }
}
