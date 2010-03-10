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
     * Simple recursive function that scrapes the theme info for either a single 
     * theme or all of them, given a directory
     * @todo make it switch between public and admin
     *
     * @return void
     **/
    protected function getAvailable($dir=null) 
    {
        /**
         * Create an array of themes, with the directory paths
         * theme.ini files and images paths if they are present
         */
        $themes = array();
        if (!$dir) {
            
            // Iterate over the directory to get the file structure
            $themesDir = new DirectoryIterator(PUBLIC_THEME_DIR);
            foreach($themesDir as $dir) {
                $fname = $dir->getFilename();
                if (!$dir->isDot() 
                    && $fname[0] != '.' 
                    && $dir->isReadable() 
                    && $dir->isDir()) {
                    $theme = $this->getAvailable($fname);
                    
                    // Finally set the array to the global array
                    $themes[$fname] = $theme;
                }
            }
            
        } else {
            // Find that theme and return its info
            
            $theme = new Theme();
            
            // Define a hard theme path for the theme
            $theme->path = PUBLIC_THEME_DIR.DIRECTORY_SEPARATOR.$dir;
            $theme->directory = $dir;
            
            // Test to see if an image is available to present the user
            // when switching themes
            $imageFile = $theme->path.DIRECTORY_SEPARATOR.'theme.jpg';
            if (file_exists($imageFile) && is_readable($imageFile)) {
                $img = WEB_PUBLIC_THEME.'/'.$dir.'/theme.jpg';
                $theme->image = $img;
            }
            
            // Finally get the theme's config file
            $themeIni = $theme->path.DIRECTORY_SEPARATOR.'theme.ini';
            if (file_exists($themeIni) && is_readable($themeIni)) {
                $ini = new Zend_Config_Ini($themeIni, 'theme');
                foreach ($ini as $key => $value) {
                    $theme->$key = $value;
                }
            } else {
                // Display some sort of warning that the theme doesn't have an ini file
            }
            
            // Get the theme's config file
            $themeConfig = $theme->path.DIRECTORY_SEPARATOR.'config.ini';
            
            // If the theme has a config file, set hasConfig to true.
            $theme->hasConfig = (file_exists($themeConfig) && is_readable($themeConfig));
            
            return $theme;
        }
        return $themes;
    }
    
    public function browseAction()
    {
        $themes = $this->getAvailable();
        
        if (!empty($_POST) && $this->isAllowed('switch')) {
            set_option('public_theme', strip_tags($_POST['public_theme']));
            
            if(!$this->_getThemeOptions($_POST['public_theme'])) {
                $configForm = $this->_getForm($_POST['public_theme']);
                if($configForm) {
                    $formValues = $configForm->getValues();
                    unset($formValues['submit']);
                    $this->_setThemeOptions($_POST['public_theme'], $formValues);
                }
            }
            
            $this->flashSuccess("The theme has been successfully changed.");
        }
        
        $public = get_option('public_theme');
        
        $current = $this->getAvailable($public);
        
        $this->view->assign(compact('current', 'themes'));
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