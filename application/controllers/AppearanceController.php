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
                    set_option($key, $value);
                }
                $this->_helper->flashMessenger(__('The appearance settings have been updated.'), 'success');
                $this->_helper->redirector('edit-settings');
            } else {
                $this->_helper->flashMessenger(__('There were errors found in your form. Please edit and resubmit.'), 'error');
            }
        }
    }

    public function editSortingAction()
    {
        $form = new Zend_Form();
        $form->setElementDecorators(['ViewHelper']);
        $form->removeDecorator('HtmlTag');
        $form->addElement('hash', 'sorting_csrf', ['timeout' => 3600]);
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($_POST)) {
                // Restore defaults by deleting each type's stored options, so
                // get_sort_tiles() and the view's ?: fallbacks take over again.
                if (isset($_POST['sorting_restore_defaults'])) {
                    foreach ($_POST as $key => $value) {
                        if (str_ends_with($key, '_sort_options')) {
                            $type = substr($key, 0, -strlen('_sort_options'));
                            delete_option("{$type}_sort_options");
                            delete_option("{$type}_sort_default_dir");
                            delete_option("{$type}_sort_default_field");
                        }
                    }
                    $this->_helper->flashMessenger(__('The sorting settings have been restored to their defaults.'), 'success');
                    $this->_helper->redirector('edit-sorting');
                    return;
                }
                // The sort tiles widget (core's items/collections, and any
                // plugin-registered type via the admin_appearance_sorting_form
                // hook) is plain HTML, not Zend_Form elements, so its fields
                // are read directly by name pattern rather than a fixed list.
                foreach ($_POST as $key => $value) {
                    if (str_ends_with($key, '_sort_options')) {
                        set_option($key, json_encode(sanitize_sort_tiles($value)));
                    } elseif (str_ends_with($key, '_sort_default_dir')) {
                        set_option($key, $value === 'a' ? 'a' : 'd');
                    } elseif (str_ends_with($key, '_sort_default_field')) {
                        // Only keep a default field that's one of this type's
                        // saved tiles; otherwise delete it so the view falls
                        // back to the built-in default instead of a stale field.
                        $field = normalize_sort_tile_field($value);
                        $type = substr($key, 0, -strlen('_sort_default_field'));
                        $tileFields = array_column(sanitize_sort_tiles($_POST["{$type}_sort_options"] ?? ''), 'field');
                        if ($field !== null && in_array($field, $tileFields, true)) {
                            set_option($key, $field);
                        } else {
                            delete_option($key);
                        }
                    }
                }
                $this->_helper->flashMessenger(__('The sorting settings have been updated.'), 'success');
                $this->_helper->redirector('edit-sorting');
            } else {
                $this->_helper->flashMessenger(__('There were errors found in your form. Please edit and resubmit.'), 'error');
            }
        }
    }

    /**
     * Render a single sort tile's markup for AJAX insertion, so newly-added
     * tiles are built from the same template as the initial page render
     * instead of being duplicated in JavaScript.
     *
     * The widget's current tiles are passed as JSON in "tiles", so a field
     * that's invalid or already in the list is rejected with a 400 and a
     * message body instead of a tile.
     */
    public function addSortTileAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $type = trim((string) $this->_getParam('type'));
        $rawField = trim((string) $this->_getParam('field'));
        $index = (int) $this->_getParam('index');
        if ($type === '' || $rawField === '') {
            return;
        }
        $field = normalize_sort_tile_field($rawField);
        if ($field === null) {
            $this->getResponse()->setHttpResponseCode(400);
            $this->getResponse()->setBody(__('"%s" is not a valid sort field.', $rawField));
            return;
        }
        $existingTiles = sanitize_sort_tiles($this->_getParam('tiles'));
        if (in_array($field, array_column($existingTiles, 'field'), true)) {
            $this->getResponse()->setHttpResponseCode(400);
            $this->getResponse()->setBody(__('"%s" is already a sort option.', $field));
            return;
        }
        $label = derive_sort_tile_label($field);
        $widget = new Omeka_View_Helper_SortTilesWidget();
        $this->getResponse()->setBody($widget->tileMarkup($type, $index, $label, $field));
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
