<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Action helper for handling theme configuration.
 * 
 * @package Omeka\Controller\ActionHelper
 */
class Omeka_Controller_Action_Helper_ThemeConfiguration extends Zend_Controller_Action_Helper_Abstract
{
    const THEME_UPLOAD_TYPE = 'theme_uploads';

    private $_themeOptions = array();
    private $_formValues = array();
    private $_form;

    /**
     * Process the theme configuration form.
     *
     * For file elements, this will save them using the storage system
     * or remove them as is necessary.
     *
     * @param Zend_Form $form The form to save.
     * @param array $data The data to fill the form with.
     * @param array $originalOptions The previous options for the form.
     * @return array|bool Array of options if the form was validly
     *  submitted, false otherwise.
     */
    public function processForm(Zend_Form $form, $data, $originalOptions = array()) { 
        $this->_form = $form;
        
        $elements = $this->_form->getElements();
        foreach($elements as $element) {
            if ($element instanceof Zend_Form_Element_File) {
                $this->_configFileElement($element);
            }
        }

        // validate the form (note: this will populate the form with the post values)
        if ($this->_form->isValid($data)) {
            $this->_formValues = $this->_form->getValues();

            // CSRF token should not be saved as an setting
            unset($this->_formValues['theme_config_csrf']);

            $this->_themeOptions = $originalOptions;
            
            foreach($elements as $element) {
                if ($element instanceof Zend_Form_Element_File) {
                    $this->_processFileElement($element);
                }
            }
            
            return $this->_formValues;
        }
        return false;
    }

    /**
     * Ignore a file element that has an associated hidden element,
     * since this means that the user did not change the uploaded file.
     *
     * @param Zend_Form_Element_File $element
     */
    private function _configFileElement(Zend_Form_Element_File $element)
    {
        $elementName = $element->getName();
        
        // If file input's related  hidden input has a non-empty value, 
        // then the user has NOT changed the file, so do NOT upload the file.
        if (($hiddenElement = $this->_form->getElement(Omeka_Form_ThemeConfiguration::THEME_FILE_HIDDEN_FIELD_NAME_PREFIX . $elementName))) {
            $hiddenName = $hiddenElement->getName();
            if (!empty($_POST[$hiddenName])) {
                // Ignore the file input element
                $element->setIgnore(true);
            }
        }
    }

    /**
     * Store and/or delete a file for a file element.
     *
     * @uses _deleteOldFile()
     * @param Zend_Form_Element_File $element
     */
    private function _processFileElement(Zend_Form_Element_File $element)
    {
        $elementName = $element->getName();
                
        // set the theme option for the uploaded file to the file name
        if ($element->getIgnore()) {
            // set the form value to the old theme option
            $this->_formValues[$elementName] = $this->_themeOptions[$elementName];
        } else {
            $path = $element->getFileName();
            if (empty($path)) {
                // Make sure null-like values are actually null when saved.
                $newFile = null;
            } else {
                $storage = Zend_Registry::get('storage');             
                $newFile = basename($path);
                $storagePath = $storage->getPathByType($newFile, self::THEME_UPLOAD_TYPE);
                $storage->store($path, $storagePath);
            }

            $this->_formValues[$elementName] = $newFile;
            $this->_deleteOldFile($element);
        }
    }

    /**
     * Delete a previously-stored theme file.
     *
     * @param Zend_Form_Element_File $element
     */
    private function _deleteOldFile(Zend_Form_Element_File $element)
    {
        // delete old file if it is not the same as the new file name
        if (!isset($this->_themeOptions[$element->getName()])) {
            return;
        }
        $oldFileName = $this->_themeOptions[$element->getName()];
        if ($oldFileName != $newFileName) {
            $storage = Zend_Registry::get('storage');
            $storagePath = $storage->getPathByType($oldFileName, self::THEME_UPLOAD_TYPE);
            $storage->delete($storagePath);
        }
    }
}
