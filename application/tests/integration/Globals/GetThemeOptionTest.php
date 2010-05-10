<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka_Test
 **/

class Globals_GetThemeOptionTest extends Omeka_Test_AppTestCase
{   
    public function testGetOptionWithoutThemeName()
    {
        $themeName = get_option('public_theme');
        
        $optionName = 'someoptionname';
        $optionValue = '';
        $this->assertEquals($optionValue, get_theme_option($optionName));
        $this->assertEquals($optionValue, get_theme_option($optionName, $themeName));
    }
        
    public function testGetOptionWithThemeName()
    {
        $themeName = 'seasons';
        $optionName = 'someoptionname';
        $optionValue = '';
        $this->assertEquals($optionValue, get_theme_option($optionName, $themeName));
    }
}
