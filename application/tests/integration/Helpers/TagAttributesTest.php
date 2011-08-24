<?php 
require_once HELPERS;

class TagAttributesTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->reporting = error_reporting();
        error_reporting(E_ALL);
    }
    
    /**
     * Test when a string is passed as the first argument to _tag_attributes. This
     * should return the id and name attributes, and use the argument for each of
     * their values.
     */
    public function testAttributeString()
    {
        $attributeString = 'elementvalue';
        $html = 'name="elementvalue" id="elementvalue"';
        $this->assertEquals($html, _tag_attributes($attributeString));
    }
    
    /**
     * Test attributes that all have values.
     */
    public function testAttributesWithValues()
    {
        $attributesWithValues = array('id' => 'elementid', 'class' => 'element class', 'title' => 'Element title');
        $html = 'id="elementid" class="element class" title="Element title"';
        $this->assertEquals($html, _tag_attributes($attributesWithValues));
    }
    
    /**
     * Test that attributes with null values are not output.
     */
    public function testAttributesWithNullValues()
    {
        $attributesWithNullValues = array('id' => null, 'class' => 'element class', 'title' => null);
        $html = 'class="element class"';
        $this->assertEquals($html, _tag_attributes($attributesWithNullValues));
    }
    
    /** 
     * Test that attributes with empty string values are output.
     */
    public function testAttributesWithEmptyStringValues()
    {
        $attributesWithEmptyStringValues = array('id' => '', 'class' => '', 'title' => '');
        $html = 'id="" class="" title=""';
        $this->assertEquals($html, _tag_attributes($attributesWithEmptyStringValues));
    }
    
    /** 
     * Test attributes with a mix of values.
     */
    public function testAttributesWithMixedValues()
    {
        $attributesWithMixedValues = array('id' => null, 'class' => 'element class', 'title' => '');
        $html = 'class="element class" title=""';
        $this->assertEquals($html, _tag_attributes($attributesWithMixedValues));
    }
    
    public function tearDown()
    {
        error_reporting($this->reporting);
    }
}
