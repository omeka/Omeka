<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * @see Omeka_Controller_Action
 */
require_once 'Omeka/Controller/Action.php';

/**
 * @package Omeka
 * @subpackage Controllers
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class SecurityController extends Omeka_Controller_Action
{    
    public function indexAction() {
        $this->_forward('edit');
    }
    
    public function browseAction() {
        $this->_forward('edit');
    }
    
    public function editAction() {
        //Any changes to this list should be reflected in the install script (and possibly the view functions)        
        $settingsList = array('file_extension_whitelist',
                              'file_mime_type_whitelist',
                              'disable_default_file_validation');
        
        $options = Omeka_Context::getInstance()->getOptions();
        
        foreach ($options as $k => $v) {
            if (in_array($k, $settingsList)) {
                $settings[$k] = $v;
            }
        }
        
        $optionTable = $this->getTable('Option')->getTableName();
        $conn = $this->getDb();
        
        //process the form
        if (!empty($_POST)) {
            $sql = "UPDATE $optionTable SET value = ? WHERE name = ?";
            foreach ( $_POST as $key => $value ) {
                if (array_key_exists($key,$settings)) {
                    $conn->exec($sql, array($value, $key));
                    $settings[$key] = $value;
                    $options[$key] = $value;
                }
            }
            Omeka_Context::getInstance()->setOptions($options);
            
            $this->flash("Settings have been changed.");
        }
        
        $this->view->assign($settings);
    }
    
    public function getFileExtensionWhitelistAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        if ($this->_getParam('default')) {
            $body = Omeka_Validate_File_Extension::DEFAULT_WHITELIST;
        } else {
            $body = get_option('file_extension_whitelist');
        }
        $this->getResponse()->setBody($body);
    }
    
    public function getFileMimeTypeWhitelistAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        if ($this->_getParam('default')) {
            $body = Omeka_Validate_File_MimeType::DEFAULT_WHITELIST;
        } else {
            $body = get_option('file_mime_type_whitelist');
        }
        $this->getResponse()->setBody($body);
    }
}