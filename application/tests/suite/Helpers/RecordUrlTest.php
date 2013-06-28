<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2013
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Tests for the RecordUrl view helper
 */
class Omeka_View_Helper_RecordUrlTest extends Omeka_Test_AppTestCase
{
    public function setUp()
    {
        parent::setUp();
        
        $view = new Omeka_View;
        $this->helper = $view->getHelper('RecordUrl');

        $this->mockString = $this->getMock('Item', array('getRecordUrl'), array(), '', false);
        $this->mockString->expects($this->any())->method('getRecordUrl')
            ->will($this->returnValue('/boring-url'));

        $this->mockQuery = $this->getMock('Item', array('getRecordUrl'), array(), '', false);
        $this->mockQuery->expects($this->any())->method('getRecordUrl')
            ->will($this->returnValue('/boring-url?existing=baz'));

        $this->mockRoute = $this->getMock('Item', array('getRecordUrl'), array(), '', false);
        $this->mockRoute->expects($this->any())->method('getRecordUrl')
            ->will($this->returnValue(array(
                'controller' => 'items', 
                'action' => 'browse')));
    }

    public function testStringUrl()
    {
        $url = $this->helper->recordUrl($this->mockString);
        $this->assertEquals('/boring-url', $url);
    }

    public function testStringUrlWithQuery()
    {
        $url = $this->helper->recordUrl($this->mockString, null, false, array('param1' => 'foo', 'param2' => 'bar'));
        $this->assertEquals('/boring-url?param1=foo&param2=bar', $url);
    }

    public function testQueryParameterMerging()
    {
        $url = $this->helper->recordUrl($this->mockQuery, null, false, array('param1' => 'foo', 'param2' => 'bar'));
        $this->assertEquals('/boring-url?existing=baz&param1=foo&param2=bar', $url);
    }

    public function testRouteUrl()
    {
        $url = $this->helper->recordUrl($this->mockRoute);
        $this->assertEquals('/items/browse', $url);
    }

    public function testRouteUrlWithQuery()
    {
        $url = $this->helper->recordUrl($this->mockRoute, null, false, array('param1' => 'foo', 'param2' => 'bar'));
        $this->assertEquals('/items/browse?param1=foo&param2=bar', $url);
    }
}
