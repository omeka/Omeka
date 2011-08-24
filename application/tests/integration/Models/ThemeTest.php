<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka_Test
 */

class Models_ThemeTest extends Omeka_Test_AppTestCase
{       
    public function testGetOption()
    {
        $themeName = 'seasons';
        $optionName = 'someoptionname';
        $optionValue = '';
        $this->assertEquals($optionValue, Theme::getOption($themeName, $optionName));
    }
    
    public function testSetOption()
    {    
        $themeName = 'seasons';
        
        $optionName1 = 'someoptionname';
        $optionValue1 = '';
        Theme::setOption($themeName, $optionName1, $optionValue1);
        $this->assertEquals($optionValue1, Theme::getOption($themeName, $optionName1));
    
        $optionName2 = 'someoptionname';
        $optionValue2 = 'someoptionvalue';
        Theme::setOption($themeName, $optionName2, $optionValue2);
        $this->assertEquals($optionValue2, Theme::getOption($themeName, $optionName2));
        
        $optionName3 = 'someoptionname3';
        $optionValue3 = 'someoptionvalue3';
        Theme::setOption($themeName, $optionName3, $optionValue3);
        $this->assertEquals($optionValue3, Theme::getOption($themeName, $optionName3));
        $this->assertEquals($optionValue2, Theme::getOption($themeName, $optionName2));        
    }
    
    public function testGetOptionName()
    {
        $themeName = 'seasons';
        $themeOptionName = 'theme_seasons_options';
        $this->assertEquals($themeOptionName, Theme::getOptionName($themeName));
    
        $themeName = 'SEASONS';
        $themeOptionName = 'theme_seasons_options';
        $this->assertEquals($themeOptionName, Theme::getOptionName($themeName));
    }
    
    public function testGetUploadedFileName()
    {
        self::dbChanged(false);
        $themeName = 'seasons';
        $optionName = 'logo';
        $fileName = 'bob.jpg';
        $this->assertRegExp('/^[a-f0-9]{32}\.jpg$/', Theme::getUploadedFileName($themeName, $optionName, $fileName));
    }
    
    public function testGetOptions()
    {
        self::dbChanged(false);
        $themeName = 'seasons';
        $options = array();
        $this->assertEquals($options, Theme::getOptions($themeName));
    }
    
    public function testSetOptions()
    {
        $themeName = 'seasons';

        $options = array();
        Theme::setOptions($themeName, $options);
        $this->assertEquals($options, Theme::getOptions($themeName));
        
        $options = array('a'=>'1', 'b'=>'2', 'c'=>'3');
        Theme::setOptions($themeName, $options);
        $this->assertEquals($options, Theme::getOptions($themeName));
    }
    
    public function testGetAvailable()
    {
        self::dbChanged(false);
        $themeName = 'seasons';
        
        $themes = Theme::getAvailable();
        $this->assertTrue(is_array($themes));
        
        $theme = Theme::getAvailable($themeName);
        $this->assertTrue($theme instanceof Theme);
        $this->assertEquals($themeName, $theme->directory);
    }
}
