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
class SecurityController extends Omeka_Controller_AbstractActionController
{    
    public function indexAction() {
        $this->_forward('edit');
    }
    
    public function browseAction() {
        $this->_forward('edit');
    }
    
    public function editAction() {
        $this->view->form = new Omeka_Form_SecuritySettings;
        
        
        //Any changes to this list should be reflected in the install script (and possibly the view functions)        
        $options = array(Omeka_Validate_File_Extension::WHITELIST_OPTION,
                         Omeka_Validate_File_MimeType::WHITELIST_OPTION,
                         File::DISABLE_DEFAULT_VALIDATION_OPTION,
                         'html_purifier_is_enabled',
                         'html_purifier_allowed_html_elements',
                         'html_purifier_allowed_html_attributes',
                         Omeka_Captcha::PUBLIC_KEY_OPTION,
                         Omeka_Captcha::PRIVATE_KEY_OPTION
        );
        
        //process the form
        if ($this->getRequest()->isPost() && $this->view->form->isValid($_POST)) {
            foreach ($this->view->form->getValues() as $key => $value) {
                if (in_array($key, $options)) {
                    set_option($key, $value);
                }
            }          
            $this->_helper->flashMessenger(__('The security settings have been updated.'), 'success');
        }        
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
