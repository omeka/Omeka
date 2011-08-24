<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * 
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Globals_InsertItemTypeTest extends Omeka_Test_AppTestCase
{
    public function testCanInsertItemType()
    {
        $urlElement = get_db()->getTable('Element')->findByElementSetNameAndElementName('Item Type Metadata', 'URL');
        $itemType = insert_item_type(
            array('name' => 'foobar', 'description' => 'also foobar'),
            array(
                array('name' => 'new element'),
                $urlElement
                )
        );
        $this->assertThat($itemType, $this->isInstanceOf('ItemType'));
        $this->assertTrue($itemType->exists());
        
        $newFirstElement = $itemType->Elements[0];
        $this->assertEquals($newFirstElement->name, 'new element');
        
        $newSecondElement = $itemType->Elements[1];
        $this->assertEquals($newSecondElement->name, 'URL'); 
    }
}
