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

class AppearanceController extends Omeka_Controller_AbstractActionController
{

    const DEFAULT_FULLSIZE_CONSTRAINT = 800;
    const DEFAULT_THUMBNAIL_CONSTRAINT = 200;
    const DEFAULT_SQUARE_THUMBNAIL_CONSTRAINT = 200;
    const DEFAULT_PER_PAGE_ADMIN = 10;
    const DEFAULT_PER_PAGE_PUBLIC = 10;

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
        
        if (isset($_POST['appearance_submit'])) {
            if ($form->isValid($_POST)) {
                $this->_setOptions($form);
                $this->_helper->flashMessenger(__('The appearance settings have been updated.'), 'success');
            } else {
                $this->_helper->flashMessenger(__('There were errors found in your form. Please edit and resubmit.'), 'error');
            }
        }

    }

    private function _getForm()
    {
        require_once APP_DIR . '/forms/AppearanceSettings.php';
        $form = new Omeka_Form_AppearanceSettings;
        $form->setDefaults($this->getInvokeArg('bootstrap')->getResource('Options'));
        fire_plugin_hook('appearance_settings_form', array('form' => $form));
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

?>