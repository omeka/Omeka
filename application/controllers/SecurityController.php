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
class SecurityController extends Omeka_Controller_Action
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
            $this->flashSuccess(__("The security settings have been updated."));
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
}
