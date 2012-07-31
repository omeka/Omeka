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
class Models_Builder_ItemTest extends Omeka_Test_AppTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->builder = new Builder_Item($this->db);
    }
    
    public function testCanSetItemTypeByName()
    {
        $this->builder->setRecordMetadata(array(
            Builder_Item::ITEM_TYPE_NAME => 'Still Image'
        ));
        $item = $this->builder->build();
        $this->assertNotNull($item->item_type_id);
        $itemType = $item->getItemType();
        $this->assertThat($itemType, $this->isInstanceOf('ItemType'));
        $this->assertEquals('Still Image', $itemType->name);
    }
    
    public function testCanAddTagsToItem()
    {
        $this->builder->setRecordMetadata(array(
            Builder_Item::TAGS => 'foo, bar'
        ));
        $item = $this->builder->build();
        $tags = $item->getTags();
        $this->assertEquals(2, count($tags));
    }
}
