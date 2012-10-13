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
class NavigationController extends Omeka_Controller_AbstractActionController
{    
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
        set_theme_base_url('public');
        $form = $this->_getForm();
        $this->view->form = $form;
        if (isset($_POST['submit'])) {            
            if ($form->isValid($_POST)) {
                $this->_setOptions($form);
                $this->_helper->flashMessenger(__('The navigation settings have been updated.'), 'success');
            } else {
                $this->_helper->flashMessenger(__('There were errors found in your form. Please edit and resubmit.'), 'error');
            }
        }
        // Reset to "current" base uri. "revert" won't work here because
        // something may have used public_uri or admin_uri in between.
        set_theme_base_url();
    }
    
    /**
     * Gets navigation form
     * 
     * @param string
     * @return boolean
     */    
    private function _getForm()
    {
        require_once APP_DIR . '/forms/Navigation.php';
                
        $form = new Omeka_Form_Navigation();
        
        fire_plugin_hook('navigation_form', array('form' => $form));
        return $form;
    }
    
    /**
     * Sets navigation form
     * 
     * @param string
     * @return boolean
     */
    private function _setOptions(Zend_Form $form)
    {
        $form->saveFromPost();
    }
}
