<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Test themes controller.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Omeka_Controller_ThemesControllerTest extends Omeka_Test_AppTestCase
{   
    const THEME = 'default';
    
    public function setUp()
    {
        $themeDir = PUBLIC_THEME_DIR . '/' . self::THEME;
        if (!is_dir($themeDir) 
            || !file_exists($themeDir . '/config.ini')
        ) {
            $this->markTestSkipped("Cannot test ThemesController without the '" . self::THEME . "' theme.");
        }
        parent::setUp();   
        $this->_authenticateUser($this->_getDefaultUser());
    }
    
    public function testDisplayConfigForm()
    {
        set_option(Theme::PUBLIC_THEME_OPTION, self::THEME);

        $theme = Theme::getTheme(self::THEME);
        $name = $theme->title;

        $this->request->setParam('name', self::THEME);
        $this->dispatch('themes/config');
        $this->assertController('themes');
        $this->assertAction('config');
        $this->assertQueryContentContains('h2', $name);
        $this->assertQuery('input#logo');
    }
        
    public function testConfigureThemeWithNoLogoFileAndNoPreviousLogoFile()
    {
        $themeName = self::THEME;
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
              ),
            'header_background' =>
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
          'display_featured_item' => '1',
          'display_featured_collection' => '1',
          'display_featured_exhibit' => '1',
          'homepage_recent_items' => '',
          'homepage_text' => '',
          'footer_text' => '',
          'display_footer_copyright' => '0'
        );

        $csrf = new Zend_Form_Element_Hash('theme_config_csrf');
        $csrf->initCsrfToken();
        
        // specify other post data
        $otherPostData = array(
          'hidden_file_logo' => '',
          'hidden_file_header_background' => '',  
          'MAX_FILE_SIZE' => '33554432',
          'submit' => 'Save Changes',
          'theme_config_csrf' => $csrf->getHash()
        );
        
        // set the the post data
        $post = array_merge($themeOptions, $otherPostData);
        $this->getRequest()->setParam('name', $themeName);
        $this->getRequest()->setPost($post);
        $this->getRequest()->setMethod('POST');
        
        // dispatch the controller action
        $this->dispatch('themes/config');

        $actualOptions = Theme::getOptions(self::THEME);
        $this->assertArrayNotHasKey('theme_config_csrf', $actualOptions);
        foreach($themeOptions as $name => $value) {
            $this->assertArrayHasKey($name, $actualOptions);
            $this->assertEquals($actualOptions[$name], $value, "Option '$name' was not correctly set.");
        }
        
        // verify that logo is empty
        $this->assertEmpty(get_theme_option('logo', $themeName));
    }
}
