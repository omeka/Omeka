<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 **/

/**
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private 
 * @package Omeka
 * @subpackage Controllers
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2010
 **/
class ThemesController extends Omeka_Controller_Action
{   
    
    private $_themeOptions = array();
    private $_formValues = array();
    private $_themeName;
    private $_form;     
         
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
            $this->flashError("Invalid form submission.");
            $this->_helper->redirector->goto('browse');
            return;
        }
        
        $themeName = $this->_getParam(Theme::PUBLIC_THEME_OPTION);
        // Theme names should be alphanumeric(-ish) (prevent security flaws).
        if (preg_match('/[^a-z0-9\-_]/i', $themeName)) {
            $this->flashError('You have chosen an illegal theme name. Please select another theme.');
            return;
        }
        
        // Set the public theme option according to the form post.
        set_option(Theme::PUBLIC_THEME_OPTION, $themeName);
        
        if (!Theme::getOptions($themeName) 
            && ($configForm = new Omeka_Form_ThemeConfiguration(array('themeName' => $themeName)))
        ) {
            Theme::setOptions($themeName, $configForm->getValues());
        }
        
        $this->flashSuccess("The theme has been successfully changed.");
        $this->_helper->redirector->goto('browse');
    }
    
    /**
     * Load the configuration form for a specific theme.  
     * That configuration form will be POSTed back to this URL.
     *
     * @return void
     **/
    public function configAction()
    {                      
        // get the theme name and theme object
        $this->_themeName = $this->_getParam('name');
        $theme = Theme::getAvailable($this->_themeName);
        
        // get the configuration form        
        $this->_form = new Omeka_Form_ThemeConfiguration(array('themeName' => $this->_themeName));
                
        // process the form if posted
        if ($this->getRequest()->isPost()) {
            $elements = $this->_form->getElements();
            foreach($elements as $element) {
                if ($element instanceof Zend_Form_Element_File) {
                    $this->_configFileElement($element);
                }
            }

            // validate the form (note: this will populate the form with the post values)
            if ($this->_form->isValid($_POST)) {
                $this->_formValues = $this->_form->getValues();
                $this->_themeOptions = Theme::getOptions($this->_themeName);
                
                foreach($elements as $element) {
                    if ($element instanceof Zend_Form_Element_File) {                                                
                        $this->_processFileElement($element);
                    }
                }
                                
                // set the theme options
                Theme::setOptions($this->_themeName, $this->_formValues);
                
                $this->flashSuccess('The theme settings were successfully saved!');
                $this->redirect->goto('browse');
            }
        }
        
        $this->view->configForm = $this->_form;
        $this->view->theme = $theme;
        
    }
    
    private function _configFileElement(Zend_Form_Element_File $element)
    {
        $elementName = $element->getName();
        
        // If file input's related  hidden input has a non-empty value, 
        // then the user has NOT changed the file, so do NOT upload the file.
        if (($hiddenFileElement = $this->_form->getElement(Omeka_Form_ThemeConfiguration::THEME_FILE_HIDDEN_FIELD_NAME_PREFIX . $elementName))) {
            $hiddenFileElementValue = trim($_POST[$hiddenFileElement->getName()]); 
            if ($hiddenFileElementValue != "") {                              
                // Ignore the file input element
                $element->setIgnore(true);
            }
        }
    }
    
    private function _processFileElement(Zend_Form_Element_File $element)
    {
        $elementName = $element->getName();
                
        // set the theme option for the uploaded file to the file name
        if ($element->getIgnore()) {
            // set the form value to the old theme option
            $this->_formValues[$elementName] = $this->_themeOptions[$elementName];
        } else {
            $newFile = $element->getFileName(null, false);
            if (empty($newFile)) {
                // Make sure null-like values are actually null when saved.
                $newFile = null;
            }
            $this->_formValues[$elementName] = $newFile;
            $this->_unlinkOldFile($element);
        }
    }

    private function _unlinkOldFile(Zend_Form_Element_File $element)
    {
        // delete old file if it is not the same as the new file name
        if (!isset($this->_themeOptions[$element->getName()])) {
            return;
        }
        $oldFileName = $this->_themeOptions[$element->getName()];
        if ($oldFileName != $newFileName) {
            $oldFilePath = THEME_UPLOADS_DIR . DIRECTORY_SEPARATOR . $oldFileName;
            if (is_writable($oldFilePath) && is_file($oldFilePath)) {
                unlink($oldFilePath);
            }
        }
    }
}