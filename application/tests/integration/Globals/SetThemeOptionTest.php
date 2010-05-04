<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka_Test
 **/

class Globals_SetThemeOptionTest extends Omeka_Test_AppTestCase
{   
    public function testSetOptionWithoutThemeName()
    {    
        $themeName = get_option('public_theme');
        
        $optionName1 = 'someoptionname';
        $optionValue1 = '';
        set_theme_option($optionName1, $optionValue1);
        $this->assertEquals($optionValue1, get_theme_option($optionName1));
        $this->assertEquals($optionValue1, get_theme_option($optionName1, $themeName));

        $optionName2 = 'someoptionname';
        $optionValue2 = 'someoptionvalue';
        set_theme_option($optionName2, $optionValue2);
        $this->assertEquals($optionValue2, get_theme_option($optionName2));
        $this->assertEquals($optionValue2, get_theme_option($optionName2, $themeName));
        
        $optionName3 = 'someoptionname3';
        $optionValue3 = 'someoptionvalue3';
        set_theme_option($optionName3, $optionValue3);
        $this->assertEquals($optionValue3, get_theme_option($optionName3));
        $this->assertEquals($optionValue2, get_theme_option($optionName2));
        $this->assertEquals($optionValue3, get_theme_option($optionName3, $themeName));
        $this->assertEquals($optionValue2, get_theme_option($optionName2, $themeName));
    }
    
    public function testSetOptionWithThemeName()
    {    
        $themeName = 'seasons';
        
        $optionName1 = 'someoptionname';
        $optionValue1 = '';
        set_theme_option($optionName1, $optionValue1, $themeName);
        $this->assertEquals($optionValue1, get_theme_option($optionName1, $themeName));
    
        $optionName2 = 'someoptionname';
        $optionValue2 = 'someoptionvalue';
        set_theme_option($optionName2, $optionValue2, $themeName);
        $this->assertEquals($optionValue2, get_theme_option($optionName2, $themeName));
        
        $optionName3 = 'someoptionname3';
        $optionValue3 = 'someoptionvalue3';
        set_theme_option($optionName3, $optionValue3, $themeName);
        $this->assertEquals($optionValue3, get_theme_option($optionName3, $themeName));
        $this->assertEquals($optionValue2, get_theme_option($optionName2, $themeName));
    }
}
