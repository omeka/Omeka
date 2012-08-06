<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private 
 * @package Omeka
 * @subpackage Controllers
 * @author CHNM
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class ThemesController extends Omeka_Controller_AbstractActionController
{
    public function browseAction()
    {
        $themes = apply_filters('browse_themes', Theme::getAvailable());
        $public = get_option(Theme::PUBLIC_THEME_OPTION);
        $this->view->themes = $themes;
        $this->view->current = $themes[$public];
    }
    
    public function switchAction()
    {
        if (!$this->getRequest()->isPost()) {
            $this->_helper->flashMessenger(__('Invalid form submission.'), 'error');
            $this->_helper->redirector('browse');
            return;
        }
        
        $themeName = $this->_getParam(Theme::PUBLIC_THEME_OPTION);
        // Theme names should be alphanumeric(-ish) (prevent security flaws).
        if (preg_match('/[^a-z0-9\-_]/i', $themeName)) {
            $this->_helper->flashMessenger(__('You have chosen an illegal theme name. Please select another theme.'), 'error');
            return;
        }

        $theme = Theme::getAvailable($themeName);
        $minVer = $theme->omeka_minimum_version;
        if (!empty($minVer) && version_compare(OMEKA_VERSION, $theme->omeka_minimum_version, '<')) {
            $this->_helper->flashMessenger(__('This theme requires a newer version of Omeka (%s).', $minVer), 'error');
            $this->_helper->redirector('browse');
            return;
        }
        
        // Set the public theme option according to the form post.
        set_option(Theme::PUBLIC_THEME_OPTION, $themeName);
        
        if (!Theme::getOptions($themeName) 
            && ($configForm = new Omeka_Form_ThemeConfiguration(array('themeName' => $themeName)))
        ) {
            Theme::setOptions($themeName, $configForm->getValues());
        }
        
        $this->_helper->flashMessenger(__('The theme has been successfully changed.'), 'success');
        $this->_helper->redirector('browse');
    }
    
    /**
     * Load the configuration form for a specific theme.  
     * That configuration form will be POSTed back to this URL.
     *
     * @return void
     */
    public function configAction()
    {                      
        // get the theme name and theme object
        $themeName = $this->_getParam('name');
        $theme = Theme::getAvailable($themeName);
        $themeOptions = Theme::getOptions($themeName);
        
        // get the configuration form        
        $form = new Omeka_Form_ThemeConfiguration(array('themeName' => $themeName));
                
        // process the form if posted
        if ($this->getRequest()->isPost()) {
            $configHelper = new Omeka_Controller_Action_Helper_ThemeConfiguration;

            if (($newOptions = $configHelper->processForm($form, $_POST, $themeOptions))) {
                Theme::setOptions($themeName, $newOptions);
                $this->_helper->flashMessenger(__('The theme settings were successfully saved!'), 'success');
                $this->_helper->redirector('browse');
            }
        }
        
        $this->view->configForm = $form;
        $this->view->theme = $theme;
    }
}
