<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2011
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

require_once HELPERS;

/**
 * 
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2011
 */
class Omeka_Helper_OutputFormatListTest extends Omeka_Test_AppTestCase
{
	protected $_isAdminTest = false;
    
    public function tearDown()
    {
        parent::tearDown();
        self::dbChanged(false);
    }

    public function testOutputFormatListDefault()
    {
        $this->dispatch('/items/browse');
        $html = '<ul id="output-format-list">';
		$html .= '<li><a href="/items/browse?output=atom">atom</a></li>';
		$html .= '<li><a href="/items/browse?output=dcmes-xml">dcmes-xml</a></li>';
		$html .= '<li><a href="/items/browse?output=json">json</a></li>';
		$html .= '<li><a href="/items/browse?output=omeka-json">omeka-json</a></li>';
		$html .= '<li><a href="/items/browse?output=omeka-xml">omeka-xml</a></li>';
		$html .= '<li><a href="/items/browse?output=rss2">rss2</a></li>';
		$html .= '</ul>';
        $this->assertEquals($html, output_format_list());
    }

	public function testOutputFormatListWithDefaultDelimiter()
    {
		$delimiter = ' | ';
        $this->dispatch('/items/browse');
        $html = '<p id="output-format-list">';
		$html .= '<a href="/items/browse?output=atom">atom</a>';
		$html .= $delimiter;
		$html .= '<a href="/items/browse?output=dcmes-xml">dcmes-xml</a>';
		$html .= $delimiter;
		$html .= '<a href="/items/browse?output=json">json</a>';
		$html .= $delimiter;
		$html .= '<a href="/items/browse?output=omeka-json">omeka-json</a>';
		$html .= $delimiter;
		$html .= '<a href="/items/browse?output=omeka-xml">omeka-xml</a>';
		$html .= $delimiter;
		$html .= '<a href="/items/browse?output=rss2">rss2</a>';
		$html .= '</p>';
        $this->assertEquals($html, output_format_list(false));
    }

	public function testOutputFormatListWithNewDelimiter()
    {
		$delimiter = ', ';
        $this->dispatch('/items/browse');
        $html = '<p id="output-format-list">';
		$html .= '<a href="/items/browse?output=atom">atom</a>';
		$html .= $delimiter;
		$html .= '<a href="/items/browse?output=dcmes-xml">dcmes-xml</a>';
		$html .= $delimiter;
		$html .= '<a href="/items/browse?output=json">json</a>';
		$html .= $delimiter;
		$html .= '<a href="/items/browse?output=omeka-json">omeka-json</a>';
		$html .= $delimiter;
		$html .= '<a href="/items/browse?output=omeka-xml">omeka-xml</a>';
		$html .= $delimiter;
		$html .= '<a href="/items/browse?output=rss2">rss2</a>';
		$html .= '</p>';
        $this->assertEquals($html, output_format_list(false, $delimiter));
    }
}
