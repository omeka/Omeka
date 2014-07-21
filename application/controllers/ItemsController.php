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
class ItemsController extends Omeka_Controller_AbstractActionController
{
    protected $_autoCsrfProtection = true;

    public $contexts = array(
            'browse' => array('json', 'dcmes-xml', 'rss2', 'omeka-xml', 'omeka-json', 'atom'),
            'show'   => array('json', 'dcmes-xml', 'omeka-xml', 'omeka-json', 'atom')
    );

    private $_ajaxRequiredActions = array(
        'change-type',
    );

    private $_methodRequired = array(
        'modify-tags' => array('POST'),
        'power-edit' => array('POST'),
        'change-type' => array('POST'),
        'batch-edit-save'   => array('POST'),
    );

    public function init() 
    {
        $this->_helper->db->setDefaultModelName('Item');
    }

    public function preDispatch()
    {
        $action = $this->getRequest()->getActionName();
        if (in_array($action, $this->_ajaxRequiredActions)) {
            if (!$this->getRequest()->isXmlHttpRequest()) {
                return $this->_forward('not-found', 'error');
            }
        }
        if (array_key_exists($action, $this->_methodRequired)) {
            if (!in_array($this->getRequest()->getMethod(),
                          $this->_methodRequired[$action])) {
                return $this->_forward('method-not-allowed', 'error');
            }
        }
    }
    
    /**
     * This shows the search form for items by going to the correct URI.
     * 
     * This form can be loaded as a partial by calling items_search_form().
     * 
     * @return void
     */
    public function searchAction()
    {
        // Only show this form as a partial if it's being pulled via XmlHttpRequest
        if($this->getRequest()->isXmlHttpRequest()) {
            $this->render('search-form');
        }
    }
    
    /**
     * Gets the element sets for the 'Item' record type.
     * 
     * @return array The element sets for the 'Item' record type
     */
    protected function _getItemElementSets()
    {
        return $this->_helper->db->getTable('ElementSet')->findByRecordType('Item');
    }
    
    /**
     * Adds an additional permissions check to the built-in edit action.
     * 
     */
    public function editAction()
    {
        // Get all the element sets that apply to the item.
        $this->view->elementSets = $this->_getItemElementSets();
        if (!Zend_Registry::isRegistered('file_derivative_creator') && is_allowed('Settings', 'edit')) {
            $this->_helper->flashMessenger(__('The ImageMagick directory path has not been set. No derivative images will be created. If you would like Omeka to create derivative images, please set the path in Settings.'));
        }
        parent::editAction();
    }
    
    protected function _getAddSuccessMessage($item)
    {
        $itemTitle = $this->_getElementMetadata($item, 'Dublin Core', 'Title');
        if ($itemTitle != '') {
            return __('The item "%s" was successfully added!', $itemTitle);
        } else {
            return __('The item #%s was successfully added!', strval($item->id));
        }        
    }
    
    protected function _getEditSuccessMessage($item)
    {
        $itemTitle = $this->_getElementMetadata($item, 'Dublin Core', 'Title');
        if ($itemTitle != '') {
            return __('The item "%s" was successfully changed!', $itemTitle);
        } else {
            return __('The item #%s was successfully changed!', strval($item->id));
        }
    }

    protected function  _getDeleteSuccessMessage($item)
    {
        $itemTitle = $this->_getElementMetadata($item, 'Dublin Core', 'Title');
        if ($itemTitle != '') {
            return __('The item "%s" was successfully deleted!', $itemTitle);
        } else {
            return __('The item #%s was successfully deleted!', strval($item->id));
        }
    }
    
    protected function _getDeleteConfirmMessage($item)
    {
        $itemTitle = $this->_getElementMetadata($item, 'Dublin Core', 'Title');
        if ($itemTitle != '') {        
            return __('This will delete the item "%s" and its associated metadata. It will '
                 . 'also delete all files and file metadata associated with this '
                 . 'item.', $itemTitle);
        } else {
            return __('This will delete the item #%s and its associated metadata. It will '
                 . 'also delete all files and file metadata associated with this '
                 . 'item.', strval($item->id));
        }
    }
    
    protected function _getElementMetadata($item, $elementSetName, $elementName) 
    {
        $m = new Omeka_View_Helper_Metadata;
        return strip_formatting($m->metadata($item, array($elementSetName, $elementName)));
    }
    
    public function addAction()
    {
        // Get all the element sets that apply to the item.
        $this->view->elementSets = $this->_getItemElementSets();
        if (!Zend_Registry::isRegistered('file_derivative_creator') && is_allowed('Settings', 'edit')) {
            $this->_helper->flashMessenger(__('The ImageMagick directory path has not been set. No derivative images will be created. If you would like Omeka to create derivative images, please set the path in Settings.'));
        }
        return parent::addAction();
    }

    /**
     * Finds all tags associated with items (used for tag cloud)
     * 
     * @return void
     */
    public function tagsAction()
    {
        $params = array_merge($this->_getAllParams(), array('type'=>'Item'));
        $tags = $this->_helper->db->getTable('Tag')->findBy($params);
        $this->view->assign(compact('tags'));
    }
    
    /**
     * Browse the items.  Encompasses search, pagination, and filtering of
     * request parameters.  Should perhaps be split into a separate
     * mechanism.
     * 
     * @return void
     */
    public function browseAction()
    {
        if (!$this->_getParam('sort_field')) {
            $this->_setParam('sort_field', 'added');
            $this->_setParam('sort_dir', 'd');
        }

        //Must be logged in to view items specific to certain users
        if ($this->_getParam('user') && !$this->_helper->acl->isAllowed('browse', 'Users')) {
            $this->_setParam('user', null);
            // Zend re-reads from GET/POST on every getParams() so we need to
            // also remove these.
            unset($_GET['user'], $_POST['user']);
        }
        
        parent::browseAction();
    }

    /**
     * Retrieve the number of items to display on any given browse page.
     * This can be modified as a query parameter provided that a user is
     * actually logged in.
     *
     * @return integer
     */
    public function _getBrowseRecordsPerPage()
    {
        //Retrieve the number from the options table
        $options = $this->getFrontController()->getParam('bootstrap')
                          ->getResource('Options');

        if (is_admin_theme()) {
            $perPage = (int) $options['per_page_admin'];
        } else {
            $perPage = (int) $options['per_page_public'];
        }
        
        // If users are allowed to modify the # of items displayed per page,
        // then they can pass the 'per_page' query parameter to change that.
        if ($this->_helper->acl->isAllowed('modifyPerPage', 'Items') && ($queryPerPage = $this->getRequest()->get('per_page'))) {
            $perPage = $queryPerPage;
        }

        if ($perPage < 1) {
            $perPage = null;
        }

        return $perPage;
    }
    
    ///// AJAX ACTIONS /////
    
    /**
     * Find or create an item for this mini-form
     *
     */
    public function changeTypeAction()
    {
        if (isset($_POST['item_id'])) {
            $item = $this->_helper->db->findById($_POST['item_id']);
        } else {
            $item = new Item;
        }
        
        $item->item_type_id = (int) $_POST['type_id'];
        $this->view->assign(compact('item'));
    }
    
    ///// END AJAX ACTIONS /////
    
    /**
     * Batch editing of Items. If this is an AJAX request, it will
     * render the 'batch-edit' as a partial.
     * 
     * @return void
     */
    public function batchEditAction()
    {
        /**
         * Only show this view as a partial if it's being pulled via
         * XmlHttpRequest
         */
        $this->view->isPartial = $this->getRequest()->isXmlHttpRequest();
        
        $itemIds = $this->_getParam('items');
        if (empty($itemIds)) {
            $this->_helper->flashMessenger(__('You must choose some items to batch edit.'), 'error');
            $this->_helper->redirector('browse', 'items');
            return;
        }

        $this->view->assign(compact('itemIds'));
        if ($this->_getParam('submit-batch-delete')) {
            $this->render('batch-delete');
        }
    }
    
    /**
     * Processes batch edit information. Only accessible via POST.
     * 
     * @return void
     */
    public function batchEditSaveAction()
    {
        $hashParam = $this->_getParam('batch_edit_hash');
        $hash = new Zend_Form_Element_Hash('batch_edit_hash');
        if (!$hash->isValid($hashParam)) {
            throw new Omeka_Controller_Exception_403;
        }

        if ($itemIds = $this->_getParam('items')) {
            $metadata = $this->_getParam('metadata');
            $removeMetadata = $this->_getParam('removeMetadata');
            $delete = $this->_getParam('delete');
            $custom = $this->_getParam('custom');

            // Set metadata values to null for "removed" metadata keys.
            if ($removeMetadata && is_array($removeMetadata)) {
                foreach ($removeMetadata as $key => $value) {
                    if($value) {
                        $metadata[$key] = null;
                    }
                }
            }

            $errorMessage = null;
            $aclHelper = $this->_helper->acl;
            
            if ($metadata && array_key_exists('public', $metadata) && !$aclHelper->isAllowed('makePublic')) {
                $errorMessage = 
                    __('User is not allowed to modify visibility of items.');
            }

            if ($metadata && array_key_exists('featured', $metadata) && !$aclHelper->isAllowed('makeFeatured')) {
                $errorMessage = 
                    __('User is not allowed to modify featured status of items.');
            }

            if (!$errorMessage) {
                foreach ($itemIds as $id) {
                    if ($item = $this->_helper->db->getTable('Item')->find($id)) {
                        if ($delete && !$aclHelper->isAllowed('delete', $item)) {
                            $errorMessage = __('User is not allowed to delete selected items.');
                            break;
                        }

                        // Check to see if anything but 'tag'
                        if ($metadata && array_diff_key($metadata, array('tags' => '')) && !$aclHelper->isAllowed('edit', $item)) {
                            $errorMessage = __('User is not allowed to edit selected items.');
                            break;
                        }

                        if ($metadata && array_key_exists('tags', $metadata) && !$aclHelper->isAllowed('tag', $item)) {
                            $errorMessage = __('User is not allowed to tag selected items.');
                            break;
                        }
                        
                        
                        release_object($item);
                    }
                }
            }

            $errorMessage = apply_filters(
                'items_batch_edit_error', 
                $errorMessage, 
                array(
                    'metadata' => $metadata, 
                    'custom' => $custom, 
                    'item_ids' => $itemIds, 
                )
            );

            if ($errorMessage) {
                $this->_helper->flashMessenger($errorMessage, 'error');
            } else {
                $dispatcher = Zend_Registry::get('job_dispatcher');
                $dispatcher->send(
                    'Job_ItemBatchEdit', 
                    array(
                        'itemIds' => $itemIds, 
                        'delete' => $delete, 
                        'metadata'  => $metadata, 
                        'custom' => $custom
                    )
                );
                if ($delete) {
                  $message = __('The items were successfully deleted!');
                } else {
                  $message = __('The items were successfully changed!');
                }
                $this->_helper->flashMessenger($message, 'success');            }
         }

         $this->_helper->redirector('browse', 'items');
    }
}
