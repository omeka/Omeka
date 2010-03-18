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
     const THEME_FILE_HIDDEN_FIELD_NAME_PREFIX = 'hidden_file_';
     
    /**
     * Retrieve information about a given theme (or all themes).
     **/
    protected function getAvailable($dir=null) 
    {
        /**
         * Create an array of themes, with the directory paths
         * theme.ini files and images paths if they are present
         */
        $themes = array();        
        if (!$dir) {
            $iterator = new VersionedDirectoryIterator(PUBLIC_THEME_DIR);
            $themeDirs = $iterator->getValid();
            foreach ($themeDirs as $themeName) {
                $themes[$themeName] = $this->getAvailable($themeName);
            }
            return $themes;
        } else {
            $theme = new Theme();            
            $theme->setDirectoryName($dir);
            $theme->setImage('theme.jpg');
            $theme->setIni('theme.ini');
            $theme->setConfig('config.ini');
            return $theme;
        }
    }
    
    public function browseAction()
    {
        $themes = apply_filters('browse_themes', $this->getAvailable());
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
        
        if (!$this->_getThemeOptions($themeName) 
            && ($configForm = $this->_getForm($themeName))
        ) {
            $formValues = $configForm->getValues();
            unset($formValues['submit']);
            $this->_setThemeOptions($themeName, $formValues);
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
        $theme = $this->getAvailable($themeName);
        
        // get the configuration form        
        $configForm = $this->_getForm($themeName);
        
        // process the form if posted
        if ($this->getRequest()->isPost()) {                
            $uploadedFileNames = array();
            $elements = $configForm->getElements();
            foreach($elements as $element) {
                if ($element instanceof Zend_Form_Element_File) {
                    
                    // add filters to rename all of the uploaded theme files                                               
                    if ($element->isUploaded()) {
                        
                        if (get_option('disable_default_file_validation') == '0') {
                            $element->addValidator(new Omeka_Validate_File_Extension());
                            $element->addValidator(new Omeka_Validate_File_MimeType());
                        }
                        
                        $fileName = $element->getFileName();
                        $uploadedFileName = trim(strtolower($themeName)) . '_' . $element->getName() . '_' . basename($fileName);                        
                        $uploadedFileNames[$element->getName()] = $uploadedFileName;
                        $uploadedFilePath = $element->getDestination() . DIRECTORY_SEPARATOR . $uploadedFileName;
                        $element->addFilter('Rename', array('target'=>$uploadedFilePath, 'overwrite'=>true));
                    }

                    // If file input's related  hidden input has a non-empty value, 
                    // then the user has NOT changed the file, so do NOT upload the file.
                    if ($hiddenFileElement = $configForm->getElement(self::THEME_FILE_HIDDEN_FIELD_NAME_PREFIX . $element->getName())) { 
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
                $currentThemeOptions = $this->_getThemeOptions($themeName);
                
                foreach($elements as $element) {
                    if ($element instanceof Zend_Form_Element_File) {                                                
                        
                        // set the theme option for the uploaded file to the file name
                        $elementName = $element->getName();
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
                                if (is_writable($oldFilePath)) {
                                    unlink($oldFilePath);
                                }
                            }

                        }
                                    
                    } else if ($element instanceof Zend_Form_Element_Hidden) {
                        
                        // unset the values for the hidden fields associated with the file inputs
                        if (strpos($element->getName(), self::THEME_FILE_HIDDEN_FIELD_NAME_PREFIX) == 0) { 
                            unset($formValues[$element->getName()]);
                        }
                   }
                }
                
                // unset the submit input
                unset($formValues['submit']);
                                
                // set the theme options
                $this->_setThemeOptions($themeName, $formValues);
                
                $this->flashSuccess('The theme settings were successfully saved!');
                $this->redirect->goto('browse');
            }
        }
        
        $this->view->assign(compact('theme','configForm'));
    }
    
    private function _getForm($themeName)
    {
        $theme = $this->getAvailable($themeName);
        $themeConfigIni = $theme->path.DIRECTORY_SEPARATOR.'config.ini';
        $themeOptionName = 'theme_'.trim(strtolower($themeName)).'_options';
        
        if (file_exists($themeConfigIni) && is_readable($themeConfigIni)) {
        
            // get the theme configuration form specification
            $formElementsIni = new Zend_Config_Ini($themeConfigIni, 'config');
            $configIni = new Zend_Config(array('elements' => $formElementsIni));
        
            // create an omeka form from the configuration file
            $configForm = new Omeka_Form($configIni);
            $configForm->setAction("");
            $configForm->setAttrib('enctype', 'multipart/form-data');
            
            // add the 'Save Changes' submit button                      
            $configForm->addElement(
                'submit', 
                'submit', 
                array(
                    'label' => 'Save Changes'
                )
            );
            
            // configure all of the form elements
            $elements = $configForm->getElements();
            $newElements = array();
            foreach($elements as $element) {
                if ($element instanceof Zend_Form_Element_File) {
                    
                    // set all the file elements destination directories
                    $element->setDestination(THEME_UPLOADS_DIR);
                    $fileName = get_theme_option($element->getName());                    
                    
                    // add a hidden field to store whether already exists
                    $hiddenElement = new Zend_Form_Element_Hidden(self::THEME_FILE_HIDDEN_FIELD_NAME_PREFIX . $element->getName());
                    $hiddenElement->setValue($fileName);
                    $hiddenElement->setDecorators(array('ViewHelper', 'Errors'));
                    $newElements[] = $hiddenElement;
                        
                }
                $newElements[] = $element;
            }
            $configForm->setElements($newElements);            

            // set all of the form element values
            $themeConfigValues = $this->_getThemeOptions($themeName);
            foreach($themeConfigValues as $key => $value) {
                if ($configForm->getElement($key)) {
                    $configForm->$key->setValue($value);
                }
            }
            
            return $configForm;
        }
        return null;
    }
    
    private function _setThemeOptions($themeName, $formValues)
    {
        $themeOptionName = 'theme_'.trim(strtolower($themeName)).'_options';
        set_option($themeOptionName, serialize($formValues));
    }
    
    private function _getThemeOptions($themeName)
    {
        $themeOptionName = 'theme_'.trim(strtolower($themeName)).'_options';
        $themeConfigValues = get_option($themeOptionName);
        if ($themeConfigValues) {
            $themeConfigValues = unserialize($themeConfigValues);
        } else {
            $themeConfigValues = array();
        }
        
        return $themeConfigValues;
    }
}