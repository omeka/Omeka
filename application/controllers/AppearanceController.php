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
class AppearanceController extends Omeka_Controller_AbstractActionController
{
    const DEFAULT_FULLSIZE_CONSTRAINT = 800;
    const DEFAULT_THUMBNAIL_CONSTRAINT = 200;
    const DEFAULT_SQUARE_THUMBNAIL_CONSTRAINT = 200;
    const DEFAULT_PER_PAGE_ADMIN = 10;
    const DEFAULT_PER_PAGE_PUBLIC = 10;

    public function indexAction()
    {
        $this->_helper->redirector('browse', 'themes');
    }

    public function browseAction()
    {
        $this->_helper->redirector('browse', 'themes');
    }

    public function editSettingsAction()
    {
        $form = new Omeka_Form_AppearanceSettings;
        $form->setDefaults($this->getInvokeArg('bootstrap')->getResource('Options'));
        $form->removeDecorator('Form');
        fire_plugin_hook('appearance_settings_form', ['form' => $form]);
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($_POST)) {
                $options = $form->getValues();
                // Everything except the CSRF hash should correspond to a
                // valid option in the database.
                unset($options['appearance_csrf']);
                foreach ($options as $key => $value) {
                    if (substr($key, -13) === '_sort_options') {
                        $value = json_encode(sanitize_sort_tiles($value));
                    }
                    set_option($key, $value);
                }
                // Sort direction and default field selects are plain fields
                // inside the hand-built sort tiles widget, not Zend_Form
                // elements.
                foreach (['items_sort_default_dir', 'collections_sort_default_dir'] as $dirKey) {
                    $dir = ($_POST[$dirKey] ?? null) === 'a' ? 'a' : 'd';
                    set_option($dirKey, $dir);
                }
                foreach (['items_sort_default_field', 'collections_sort_default_field'] as $fieldKey) {
                    $field = trim((string) ($_POST[$fieldKey] ?? ''));
                    if ($field !== '') {
                        set_option($fieldKey, $field);
                    }
                }
                $this->_helper->flashMessenger(__('The appearance settings have been updated.'), 'success');
                $this->_helper->redirector('edit-settings');
            } else {
                $this->_helper->flashMessenger(__('There were errors found in your form. Please edit and resubmit.'), 'error');
            }
        }
    }

    public function editNavigationAction()
    {
        $form = new Omeka_Form_Navigation();
        fire_plugin_hook('navigation_form', ['form' => $form]);
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($_POST)) {
                $form->saveFromPost();
                $this->_helper->flashMessenger(__('The navigation settings have been updated.'), 'success');
                $this->_helper->redirector('edit-navigation');
            } else {
                $this->_helper->flashMessenger(__('The navigation settings were not saved because of missing or invalid values.  All changed values have been restored.'), 'error');
                foreach ($form->getMessages() as $msg) {
                    $this->_helper->flashMessenger($msg, 'error');
                }
            }
        }
    }

    public function resetNavigationConfirmAction()
    {
        $isPartial = $this->getRequest()->isXmlHttpRequest();
        $form = $this->_getResetForm();

        $this->view->assign(compact('isPartial', 'form'));
        $this->render('appearance/reset-navigation-confirm', null, true);
    }

    public function resetNavigationAction()
    {
        if (!$this->getRequest()->isPost()) {
            $this->_forward('method-not-allowed', 'error', 'default');
            return;
        }
        $form = $this->_getResetForm();
        if ($form->isValid($_POST)) {
            $nav = array_reverse(Omeka_Navigation::getNavigationOptionValueForInstall(), true);
            set_option(Omeka_Navigation::PUBLIC_NAVIGATION_MAIN_OPTION_NAME,
                       $nav);
            $this->_helper->flashMessenger(__('The navigation settings have been reset.'), 'success');
            $this->_helper->redirector('edit-navigation');
        } else {
            throw new Omeka_Controller_Exception_404;
        }
    }

    protected function _getResetForm()
    {
        $form = new Zend_Form();
        $form->setElementDecorators(['ViewHelper']);
        $form->removeDecorator('HtmlTag');
        $form->addElement('hash', 'confirm_reset_hash');
        $form->addElement('submit', 'Reset', ['class' => 'delete red button']);
        $form->setAction($this->view->url(['action' => 'reset-navigation']));
        return $form;
    }
}
