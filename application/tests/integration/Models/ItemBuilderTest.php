<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * 
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2007-2010
 */
class Models_ItemBuilderTest extends Omeka_Test_AppTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->builder = new ItemBuilder($this->db);
    }
    
    public function testCanSetItemTypeByName()
    {
        $this->builder->setRecordMetadata(array(
            ItemBuilder::ITEM_TYPE_NAME => 'Still Image'
        ));
        $item = $this->builder->build();
        $this->assertNotNull($item->item_type_id);
        $itemType = $item->getItemType();
        $this->assertThat($itemType, $this->isInstanceOf('ItemType'));
        $this->assertEquals('Still Image', $itemType->name);
    }
}