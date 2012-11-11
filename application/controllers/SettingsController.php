<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * @package Omeka\Controller
 */
class SettingsController extends Omeka_Controller_AbstractActionController
{    
    const DEFAULT_TAG_DELIMITER = ',';
        
    public function indexAction() 
    {
        $this->_forward('edit');
    }
    
    public function browseAction() 
    {
        $this->_forward('edit');
    }
    
    public function editAction() 
    {
        $form = $this->_getForm();
        $this->view->form = $form;
        
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($_POST)) {
                $this->_setOptions($form);
                $this->_helper->flashMessenger(__('The general settings have been updated.'), 'success');
            } else {
                $this->_helper->flashMessenger(__('There were errors found in your form. Please edit and resubmit.'), 'error');
            }
        }
    }
    
    /**
     * Determine whether or not ImageMagick has been correctly installed and
     * configured.  
     * 
     * In a few cases, this will indicate failure even though the ImageMagick
     * program works properly.  In those cases, users may ignore the results of
     * this test.  This is because the 'convert' command may have returned a 
     * non-zero status code for some reason.  Keep in mind that a 0 status code 
     * always indicates success.
     *
     * @return boolean True if the command line return status is 0 when
     * attempting to run ImageMagick's convert utility, false otherwise.
     */
    public function checkImagemagickAction()
    {
        $imPath = $this->_getParam('path-to-convert');
        $this->_helper->viewRenderer->setNoRender(true);
        $isValid = Omeka_File_Derivative_Image_Creator::isValidImageMagickPath($imPath);
        $this->getResponse()->setBody($isValid 
                                    ? '<div class="im-success">' . __('The ImageMagick directory path works.') . '</div>' 
                                    : '<div class="im-failure">' . __('The ImageMagick directory path does not work.') . '</div>');
    }
        
    private function _getForm()
    {
        require_once APP_DIR . '/forms/GeneralSettings.php';
        $form = new Omeka_Form_GeneralSettings;
        $form->setDefaults($this->getInvokeArg('bootstrap')->getResource('Options'));
        fire_plugin_hook('general_settings_form', array('form' => $form));

        $form->removeDecorator('Form');
        return $form;
    }
    
    private function _setOptions(Zend_Form $form)
    {        
        $options = $form->getValues();
        // Everything except the submit button should correspond to a valid 
        // option in the database.
        unset($options['settings_submit']);
        foreach ($options as $key => $value) {
            set_option($key, $value);
        }
    }
}
