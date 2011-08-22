<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Test get_theme_option().
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Globals_GetThemeOptionTest extends PHPUnit_Framework_TestCase
{   
    const THEME_NAME = 'foobar';
    
    private $_themeOptions = array(
        'logo' => 'foobar.jpg',
        'show_title' => true,
        'show_description' => false
    );
    
    public function setUp()
    {
        // Configure the options so that get_theme_option() can automatically retrieve 
        // options for the given theme.
        Omeka_Context::getInstance()->setOptions(array(
            Theme::PUBLIC_THEME_OPTION => self::THEME_NAME,
            'theme_' . self::THEME_NAME . '_options' => serialize($this->_themeOptions)
        ));
    }
    
    public function testWithoutThemeName()
    {
        $this->assertEquals(true, get_theme_option('show_title'));
        $this->assertEquals('foobar.jpg', get_theme_option('logo'));
    }
        
    public function testWithThemeName()
    {
        $this->assertEquals('foobar.jpg', get_theme_option('logo', self::THEME_NAME));
        $this->assertEquals(true, get_theme_option('show_title', self::THEME_NAME));
    }
}
