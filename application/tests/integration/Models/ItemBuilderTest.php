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
    
    public function testThrowsExceptionIfNoEntityGivenWhenTagging()
    {
        $this->builder->setRecordMetadata(array(
            ItemBuilder::TAGS => 'foo, bar'
        ));
        try {
            $item = $this->builder->build();
            $this->fail("Should have thrown an exception when no entity was given for tagging.");
        } catch (Omeka_Record_Builder_Exception $e) {
            $this->assertContains("no Entity is available", $e->getMessage());
        }
    }
    
    public function testCanAddTagsToItem()
    {
        $entity = new Entity;
        $entity->first_name = 'Foobar';
        $entity->forceSave();
        $this->builder->setRecordMetadata(array(
            ItemBuilder::TAGS => 'foo, bar',
            ItemBuilder::TAG_ENTITY => $entity
        ));
        $item = $this->builder->build();
        $tags = $item->getTags();
        $this->assertEquals(2, count($tags));
    }
}
