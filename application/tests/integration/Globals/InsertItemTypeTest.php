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
class Globals_InsertItemTypeTest extends Omeka_Test_AppTestCase
{
    public function testCanInsertItemType()
    {
        $itemType = insert_item_type(
            array('name' => 'foobar', 'description' => 'also foobar'),
            array(array('name' => 'new element'))
        );
        $this->assertThat($itemType, $this->isInstanceOf('ItemType'));
        $this->assertTrue($itemType->exists());
    }
}