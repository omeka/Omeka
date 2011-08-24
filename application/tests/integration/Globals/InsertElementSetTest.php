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
class Globals_InsertElementSetTest extends Omeka_Test_AppTestCase
{
    public function testCanInsertElementSet()
    {
        $elementSet = insert_element_set(
            array('name'=>'Foobar Element Set', 'description'=>'foobar'),
            array(
                array('name'=>'Element Name', 'description'=>'Element Description')
            )
        );
        $this->assertThat($elementSet, $this->isInstanceOf('ElementSet'));
        $this->assertTrue($elementSet->exists());
        $elements = $elementSet->getElements();
        $this->assertEquals(1, count($elements));
    }
}
