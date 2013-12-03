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
        // TODO Integrate storage paths and constraints with other base options.
        $derivative_types = get_option('derivative_types');
        if (empty($derivative_types)) {
            $derivative_types = array(
                'original',
                'fullsize',
                'thumbnail',
                'square_thumbnail',
            );
            set_option('derivative_types', serialize($derivative_types));
            set_option('original_path', 'original');
            set_option('fullsize_path', 'fullsize');
            set_option('thumbnail_path', 'thumbnails');
            set_option('square_thumbnail_path', 'square_thumbnails');
            set_option('square_thumbnail_constraint_square', true);
            delete_option('storage_paths');
        }
        else {
            $derivative_types = unserialize($derivative_types);
        }

        require_once APP_DIR . '/forms/AppearanceSettings.php';
        $form = new Omeka_Form_AppearanceSettings;
        $form->setDefaults($this->getInvokeArg('bootstrap')->getResource('Options'));

        // TODO Integrate storage paths and constraints with other base options.
        foreach ($derivative_types as $type) {
            $form->setDefault($type . '_path', get_option($type . '_path'));
            $form->setDefault($type . '_constraint', get_option($type . '_constraint'));
            $form->setDefault($type . '_constraint_square', get_option($type . '_constraint_square'));
        }
        $form->setDefault('derivative_types', implode('; ', $derivative_types));

        $form->removeDecorator('Form');
        fire_plugin_hook('appearance_settings_form', array('form' => $form));
        $this->view->form = $form;

        if (isset($_POST['appearance_submit'])) {
            if ($form->isValid($_POST)) {
                $options = $form->getValues();
                // Everything except the submit button should correspond to a
                // valid option in the database.
                unset($options['settings_submit']);
                foreach ($options as $key => $value) {
                    // Serialize derivative types before saving.
                    if ($key == 'derivative_types') {
                        $values = explode(';', $value);
                        $values = array_map('trim', $values);
                        $value = serialize($values);
                    }
                    set_option($key, $value);
                }
                $this->_addNewStoragePaths();
                $this->_helper->flashMessenger(__('The appearance settings have been updated.'), 'success');
                $this->_helper->redirector('edit-settings');
            } else {
                $this->_helper->flashMessenger(__('There were errors found in your form. Please edit and resubmit.'), 'error');
            }
        }
    }

    public function editNavigationAction()
    {
        require_once APP_DIR . '/forms/Navigation.php';
        $form = new Omeka_Form_Navigation();
        fire_plugin_hook('navigation_form', array('form' => $form));
        $this->view->form = $form;

        if (isset($_POST['submit'])) {
            if ($form->isValid($_POST)) {
                $form->saveFromPost();
                $this->_helper->flashMessenger(__('The navigation settings have been updated.'), 'success');
                $this->_helper->redirector('edit-navigation');
            } else {
                $this->_helper->flashMessenger(__('The navigation settings were not saved because of missing or invalid values.  All changed values have been restored.'), 'error');
                foreach($form->getMessages() as $msg) {
                    $this->_helper->flashMessenger($msg, 'error');
                }
            }
        }

    }

    /**
     * Add new storage paths if needed.
     */
    private function _addNewStoragePaths()
    {
        $derivative_types = unserialize(get_option('derivative_types'));
        foreach ($derivative_types as $type) {
            $dirpath = FILES_DIR . DIRECTORY_SEPARATOR . get_option($type . '_path');
            if (!realpath($dirpath)) {
                $this->_createFolder($dirpath);
                // For security reason, an index.html is added.
                copy(
                    FILES_DIR . DIRECTORY_SEPARATOR . 'original' . DIRECTORY_SEPARATOR . 'index.html',
                    $dirpath . DIRECTORY_SEPARATOR . 'index.html'
                );
            }
        }
    }

    /**
     * Checks and creates a folder.
     *
     * @note Currently, Omeka core doesn't provide a function to create a folder.
     *
     * @param string $path Full path of the folder to create.
     *
     * @return boolean True if the path is created, Exception if an error occurs.
     */
    private function _createFolder($path)
    {
        if ($path != '') {
            if (file_exists($path)) {
                if (is_dir($path)) {
                    chmod($path, 0755);
                    if (is_writable($path)) {
                        return TRUE;
                    }
                    throw new Omeka_Storage_Exception(__('Error directory non writable: "%s".', $path));
                }
                throw new Omeka_Storage_Exception(__('Failed to create folder "%s": a file with the same name exists...', $path));
            }

            if (!@mkdir($path, 0755, TRUE)) {
                throw new Omeka_Storage_Exception(__('Error making directory: "%s".', $path));
            }
            chmod($path, 0755);
        }
        return TRUE;
    }
}
