<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * @package Omeka_Controller_Action
 **/
require_once 'Omeka/Controller/Action.php';

/**
 * @see Theme.php
 */
require_once 'Theme.php';

/**
 * @package Omeka
 * @subpackage Controllers
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class ThemesController extends Omeka_Controller_Action
{        
    public function browseAction()
    {
        $themes = apply_filters('browse_themes', Theme::getAvailable());
        $public = get_option('public_theme');
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
        
        $themeName = $this->_getParam('public_theme');
        // Theme names should be alphanumeric (prevent security flaws).
        $filter = new Zend_Filter_Alnum();
        $themeName = $filter->filter($themeName);
        
        // Set the public theme option according to the form post.
        set_option('public_theme', $themeName);
        
        if (!Theme::getOptions($themeName) 
            && ($configForm = $this->_getThemeConfigurationForm($themeName))
        ) {
            $formValues = $configForm->getValues();
            unset($formValues['submit']);
            Theme::setOptions($themeName, $formValues);
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
        $themeName = $this->_getParam('name');
        $theme = Theme::getAvailable($themeName);
        
        // get the configuration form        
        $configForm = new Omeka_Form_ThemeConfiguration(array('themeName' => $themeName));
                
        // process the form if posted
        if ($this->getRequest()->isPost()) {            
            $uploadedFileNames = array();
            $elements = $configForm->getElements();
            foreach($elements as $element) {
                if ($element instanceof Zend_Form_Element_File) {
                    $elementName = $element->getName();
                    
                    // add filters to rename all of the uploaded theme files                                               
                    
                    // Make sure the file was uploaded before adding the Rename filter to the element
                    if ($element->isUploaded()) {
                        if (get_option('disable_default_file_validation') == '0') {
                            $element->addValidator(new Omeka_Validate_File_Extension());
                            $element->addValidator(new Omeka_Validate_File_MimeType());
                        }
                        
                        $fileName = basename($element->getFileName());
                        $uploadedFileName = Theme::getUploadedFileName($themeName, $elementName, $fileName);                      
                        $uploadedFileNames[$elementName] = $uploadedFileName;
                        $uploadedFilePath = $element->getDestination() . DIRECTORY_SEPARATOR . $uploadedFileName;
                        $element->addFilter('Rename', array('target'=>$uploadedFilePath, 'overwrite'=>true));
                    }

                    // If file input's related  hidden input has a non-empty value, 
                    // then the user has NOT changed the file, so do NOT upload the file.
                    if ($hiddenFileElement = $configForm->getElement(Omeka_Form_ThemeConfiguration::THEME_FILE_HIDDEN_FIELD_NAME_PREFIX . $elementName)) { 
                        $hiddenFileElementValue = trim($_POST[$hiddenFileElement->getName()]); 
                        if ($hiddenFileElementValue != "") {                              
                            // Ignore the file input element
                            $element->setIgnore(true);
                        }
                    }
                }
            }

            // validate the form (note: this will populate the form with the post values)
            if ($configForm->isValid($_POST)) {                                
                $formValues = $configForm->getValues();
                $currentThemeOptions = Theme::getOptions($themeName);
                
                foreach($elements as $element) {
                    if ($element instanceof Zend_Form_Element_File) {                                                
                        $elementName = $element->getName();
                        // set the theme option for the uploaded file to the file name
                        if ($element->getIgnore()) {
                            // set the form value to the old theme option
                            $formValues[$elementName] = $currentThemeOptions[$elementName];
                        } else {                          
                            // set the new file
                            $newFileName = $uploadedFileNames[$elementName];
                            $formValues[$elementName] = $newFileName;
                            
                            // delete old file if it is not the same as the new file name
                            $oldFileName = $currentThemeOptions[$elementName];
                            if ($oldFileName != $newFileName) {
                                $oldFilePath = THEME_UPLOADS_DIR . DIRECTORY_SEPARATOR . $oldFileName;
                                if (is_writable($oldFilePath) && is_file($oldFilePath)) {
                                    unlink($oldFilePath);
                                }
                            }
                        }       
                    } else if ($element instanceof Zend_Form_Element_Hidden) {
                        $elementName = $element->getName();
                        // unset the values for the hidden fields associated with the file inputs
                        if (strpos($elementName, Omeka_Form_ThemeConfiguration::THEME_FILE_HIDDEN_FIELD_NAME_PREFIX) == 0) { 
                            unset($formValues[$elementName]);
                        }
                   }
                }
                
                // unset the submit input
                unset($formValues['submit']);
                                
                // set the theme options
                Theme::setOptions($themeName, $formValues);
                
                $this->flashSuccess('The theme settings were successfully saved!');
                $this->redirect->goto('browse');
            }
        }
        
        $this->view->assign(compact('theme','configForm'));
    }
}