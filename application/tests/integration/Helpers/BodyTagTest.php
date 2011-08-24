<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2011
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */


/**
 * Tests for body_tag() helper.
 *
 * @package Omeka
 */
class Omeka_Helper_BodyTagTest extends Omeka_Test_AppTestCase
{
    public function testBodyTagWithAttributes()
    {
        $attributes = array('id' => 'my-id', 'class' => 'my-class');
        $html = "<body id=\"my-id\" class=\"my-class\">\n";
        $this->assertEquals($html, body_tag($attributes));
    }

    public function testBodyTagWithoutAttributes()
    {
        $html = "<body>\n";
        $this->assertEquals($html, body_tag());
    }

    /**
     * Test attributes that have null values.
     */
    public function testBodyTagWithNullAttributes()
    {
        $attributes = array('id' => null , 'class' => null);
        $html = "<body>\n";
        $this->assertEquals($html, body_tag($attributes));
    }

    public function testBodyTagWithFilter()
    {
        $attributes = array('id' => 'my-id', 'class' => 'my-class');
        $defaultHtml = "<body id=\"my-id\" class=\"my-class\">\n";
        $this->assertEquals($defaultHtml, body_tag($attributes));

        add_filter('body_tag_attributes', array($this, 'bodyTagAttributesFilter'));

        $filteredHtml = "<body id=\"my-id\" class=\"my-class new-class\">\n";
        $this->assertEquals($filteredHtml, body_tag($attributes));
    }

    public function bodyTagAttributesFilter($attributes)
    {
        if (array_key_exists('class', $attributes)) {
            $attributes['class'] = $attributes['class'] . ' new-class';
        } else {
            $attributes['class'] = 'new-class';
        }

        return $attributes;
    }
}
