<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * Test themes controller.
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2007-2010
 **/
class Omeka_Controller_ThemesControllerTest extends Omeka_Test_AppTestCase
{   
    const SEASONS_THEME = 'seasons';
    
    public function setUp()
    {
        if (!is_dir(PUBLIC_THEME_DIR . DIRECTORY_SEPARATOR . self::SEASONS_THEME)) {
            $this->markTestSkipped("Cannot test ThemesController without the 'seasons' theme.");
        }
        parent::setUp();   
        set_option(Omeka_Validate_File_MimeType::HEADER_CHECK_OPTION, '1');
        $this->_authenticateUser($this->_getDefaultUser());
    }
    
    public function testDisplayConfigForm()
    {
        set_option(Theme::PUBLIC_THEME_OPTION, self::SEASONS_THEME);
        $this->request->setParam('name', self::SEASONS_THEME);
        $this->dispatch('themes/config');
        $this->assertController('themes');
        $this->assertAction('config');
        $this->assertQueryContentContains('h2', 'Please Configure The "Seasons" Theme');
        $this->assertQuery('select#style_sheet');
    }
        
    public function testConfigureSeasonsThemeWithNoLogoFileAndNoPreviousLogoFile()
    {
        $themeName = self::SEASONS_THEME;
        $this->assertEquals('', (string)get_theme_option('logo', $themeName));
        
        // specify the files array for the post
        $_FILES = array(
            'logo' => 
                array(
                  'name' => '',
                  'type' => '',
                  'tmp_name' => '',
                  'error' => 4,
                  'size' => 0
                )
        );
        
        // specify the theme options for the post
        $themeOptions = array(
          'style_sheet' => 'winter',
          'custom_header_navigation' => '',
          'display_featured_item' => '1',
          'display_featured_collection' => '1',
          'display_featured_exhibit' => '1',
          'homepage_recent_items' => '',
          'homepage_text' => '',
          'display_dublin_core_fields' => '',
          'footer_text' => '',
          'display_footer_copyright' => '0'
        );
        
        // specify other post data
        $otherPostData = array(
          'hidden_file_logo' => '',
          'MAX_FILE_SIZE' => '33554432',
          'submit' => 'Save Changes'
        );
        
        // set the the post data
        $post = array_merge($themeOptions, $otherPostData);
        $this->getRequest()->setParam('name', $themeName);
        $this->getRequest()->setPost($post);
        $this->getRequest()->setMethod('POST');
        
        // dispatch the controller action
        $this->dispatch('themes/config');
        
        foreach($themeOptions as $themeOptionName => $themeOptionValue) {
            $this->assertEquals($themeOptionValue, get_theme_option($themeOptionName, $themeName));
        }
        
        // verify that logo is empty
        $this->assertEquals('', get_theme_option('logo', $themeName));
    }
}
