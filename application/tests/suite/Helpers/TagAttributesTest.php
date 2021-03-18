<?php 
class TagAttributesTest extends Omeka_Test_TestCase
{
    public function setUpLegacy()
    {
        $this->reporting = error_reporting();
        error_reporting(E_ALL);
    }

    /**
     * Test when a string is passed as the first argument to tag_attributes. This
     * should return the id and name attributes, and use the argument for each of
     * their values.
     */
    public function testAttributeString()
    {
        $attributeString = 'elementvalue';
        $html = 'name="elementvalue" id="elementvalue"';
        $this->assertEquals($html, tag_attributes($attributeString));
    }

    /**
     * Test attributes that all have values.
     */
    public function testAttributesWithValues()
    {
        $attributesWithValues = array('id' => 'elementid', 'class' => 'element class', 'title' => 'Element title');
        $html = 'id="elementid" class="element class" title="Element title"';
        $this->assertEquals($html, tag_attributes($attributesWithValues));
    }

    /**
     * Test that attributes with null values are not output.
     */
    public function testAttributesWithNullValues()
    {
        $attributesWithNullValues = array('id' => null, 'class' => 'element class', 'title' => null);
        $html = 'class="element class"';
        $this->assertEquals($html, tag_attributes($attributesWithNullValues));
    }

    /** 
     * Test that attributes with empty string values are output.
     */
    public function testAttributesWithEmptyStringValues()
    {
        $attributesWithEmptyStringValues = array('id' => '', 'class' => '', 'title' => '');
        $html = 'id="" class="" title=""';
        $this->assertEquals($html, tag_attributes($attributesWithEmptyStringValues));
    }

    /** 
     * Test attributes with a mix of values.
     */
    public function testAttributesWithMixedValues()
    {
        $attributesWithMixedValues = array('id' => null, 'class' => 'element class', 'title' => '', 'boolean' => true, 'boolean-no' => false);
        $html = 'class="element class" title="" boolean';
        $this->assertEquals($html, tag_attributes($attributesWithMixedValues));
    }

    public function testNumericAttributes()
    {
        $attrs = array('int1' => 0, 'int2' => 100, 'float' => 1.5);
        $html = 'int1="0" int2="100" float="1.5"';
        $this->assertEquals($html, tag_attributes($attrs));
    }

    public function tearDownLegacy()
    {
        error_reporting($this->reporting);
    }
}
