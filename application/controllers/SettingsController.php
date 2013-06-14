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
        $this->_helper->redirector('edit-settings');
    }
    
    public function browseAction() 
    {
        $this->_helper->redirector('edit-settings');
    }
    
    public function editSettingsAction() 
    {
        require_once APP_DIR . '/forms/GeneralSettings.php';
        $form = new Omeka_Form_GeneralSettings;
        $form->setDefaults($this->getInvokeArg('bootstrap')->getResource('Options'));
        fire_plugin_hook('general_settings_form', array('form' => $form));
        $form->removeDecorator('Form');
        $this->view->form = $form;
        
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($_POST)) {
                $options = $form->getValues();
                // Everything except the submit button should correspond to a 
                // valid option in the database.
                unset($options['settings_submit']);
                foreach ($options as $key => $value) {
                    set_option($key, $value);
                }
                $this->_helper->flashMessenger(__('The general settings have been updated.'), 'success');
                $this->_helper->redirector('edit-settings');
            } else {
                $this->_helper->flashMessenger(__('There were errors found in your form. Please edit and resubmit.'), 'error');
            }
        }
    }
    
    public function editSecurityAction() {
        $form = new Omeka_Form_SecuritySettings;
        $form->removeDecorator('Form');
        $this->view->form = $form;
        
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($_POST)) {
                // Any changes to this list should be reflected in the install 
                // script (and possibly the view functions).
                $options = array(
                    Omeka_Validate_File_Extension::WHITELIST_OPTION,
                    Omeka_Validate_File_MimeType::WHITELIST_OPTION,
                    File::DISABLE_DEFAULT_VALIDATION_OPTION,
                    'html_purifier_is_enabled',
                    'html_purifier_allowed_html_elements',
                    'html_purifier_allowed_html_attributes',
                    Omeka_Captcha::PUBLIC_KEY_OPTION,
                    Omeka_Captcha::PRIVATE_KEY_OPTION
                );
                foreach ($form->getValues() as $key => $value) {
                    if (in_array($key, $options)) {
                        set_option($key, $value);
                    }
                }
                $this->_helper->flashMessenger(__('The security settings have been updated.'), 'success');
                $this->_helper->redirector('edit-security');
            } else {
                $this->_helper->flashMessenger(__('There were errors found in your form. Please edit and resubmit.'), 'error');
            }
        }
    }
    
    public function editSearchAction()
    {
        // Customize search record types.
        if ($this->getRequest()->isPost()) {
            if (isset($_POST['submit_save_changes'])) {
                if (isset($_POST['search_record_types'])) {
                    $option = serialize($_POST['search_record_types']);
                } else {
                    $option = serialize(array());
                }
                set_option('search_record_types', $option);
                $this->_helper->flashMessenger(__('You have changed which records are searchable in Omeka. Please re-index the records using the form below.'), 'success');
            }
            
            // Index the records.
            if (isset($_POST['submit_index_records'])) {
                Zend_Registry::get('bootstrap')->getResource('jobs')
                                               ->sendLongRunning('Job_SearchTextIndex');
                $this->_helper->flashMessenger(__('Indexing records. This may take a while. You may continue administering your site.'), 'success');
            }
            $this->_helper->redirector('edit-search');
        }
        
        $this->view->assign('searchRecordTypes', get_search_record_types());
        $this->view->assign('customSearchRecordTypes', get_custom_search_record_types());
    }
    
    public function editItemTypeElementsAction()
    {
        $elementSet = $this->_helper->db->getTable('ElementSet')->findByName(ElementSet::ITEM_TYPE_NAME);
        $db = $this->_helper->db;
        
        // Handle a submitted edit form.
        if ($this->getRequest()->isPost()) {
            
            // Update the elements.
            try {
                $elements = $this->getRequest()->getPost('elements');
                foreach ($elements as $id => $element) {
                    $elementRecord = $db->getTable('Element')->find($id);
                    if ($element['delete']) {
                        $elementRecord->delete();
                        continue;
                    }
                    $elementRecord->description = $element['description'];
                    $elementRecord->save();
                }
                $this->_helper->flashMessenger(__('The item type elements were successfully changed!'), 'success');
                $this->_helper->redirector('edit-item-type-elements');
            } catch (Omeka_Validate_Exception $e) {
                $this->_helper->flashMessenger($e);
            }
        }
        
        $this->view->element_set = $elementSet;
    }
    
    public function editApiAction()
    {
        $keyTable = $this->_helper->db->getTable('Key');
        
        // Handle a form submission
        if ($this->getRequest()->isPost()) {
            set_option('api_enable', (bool) $_POST['api_enable']);
            set_option('api_per_page', (int) $_POST['api_per_page']);
            $this->_helper->flashMessenger(__('The API configuration was successfully changed!'), 'success');
        }
        
        $this->view->api_resources = Omeka_Controller_Plugin_Api::getApiResources();
        $this->view->keys = $keyTable->findAll();
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
        $this->_helper->viewRenderer->setNoRender(true);
        $imPath = $this->_getParam('path-to-convert');
        $isValid = Omeka_File_Derivative_Image_Creator::isValidImageMagickPath($imPath);
        $this->getResponse()->setBody(
            $isValid ? '<div class="success">' . __('The ImageMagick directory path works.') . '</div>' 
                     : '<div class="error">' . __('The ImageMagick directory path does not work.') . '</div>');
    }
    
    public function getFileExtensionWhitelistAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        if ($this->_getParam('default')) {
            $body = Omeka_Validate_File_Extension::DEFAULT_WHITELIST;
        } else {
            $body = get_option(Omeka_Validate_File_Extension::WHITELIST_OPTION);
        }
        $this->getResponse()->setBody($body);
    }
    
    public function getFileMimeTypeWhitelistAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        if ($this->_getParam('default')) {
            $body = Omeka_Validate_File_MimeType::DEFAULT_WHITELIST;
        } else {
            $body = get_option(Omeka_Validate_File_MimeType::WHITELIST_OPTION);
        }
        $this->getResponse()->setBody($body);
    }
    
    public function getHtmlPurifierAllowedHtmlElementsAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        if ($this->_getParam('default')) {
            $body = implode(',', Omeka_Filter_HtmlPurifier::getDefaultAllowedHtmlElements());
        } else {
            $body = get_option('html_purifier_allowed_html_elements');
        }
        $this->getResponse()->setBody($body);
    }
    
    public function getHtmlPurifierAllowedHtmlAttributesAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        if ($this->_getParam('default')) {
            $body = implode(',', Omeka_Filter_HtmlPurifier::getDefaultAllowedHtmlAttributes());
        } else {
            $body = get_option('html_purifier_allowed_html_attributes');
        }
        $this->getResponse()->setBody($body);
    }
}
