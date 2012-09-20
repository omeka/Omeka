<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @subpackage Controllers
 * @author CHNM
 * @see Omeka_Controller_AbstractActionController
 * @access private
 */

/**
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class ItemsController extends Omeka_Controller_AbstractActionController
{
    public $contexts = array(
            'browse' => array('json', 'dcmes-xml', 'rss2', 'omeka-xml', 'omeka-json', 'atom'),
            'show'   => array('json', 'dcmes-xml', 'omeka-xml', 'omeka-json', 'atom')
    );

    private $_ajaxRequiredActions = array(
        'element-form',
        'change-type',
    );

    private $_methodRequired = array(
        'element-form' => array('POST'),
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
     * This shows the advanced search form for items by going to the correct URI.
     * 
     * This form can be loaded as a partial by calling items_search_form().
     * 
     * @return void
     */
    public function advancedSearchAction()
    {
        // Only show this form as a partial if it's being pulled via XmlHttpRequest
        if($this->getRequest()->isXmlHttpRequest()) {
            $this->render('advanced-search-form');
        }
    }
    
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
        parent::editAction();
    }
    
    protected function _getAddSuccessMessage($record)
    {
        return __('The item was successfully added!');        
    }
    
    protected function _getEditSuccessMessage($record)
    {
        return __('The item was successfully changed!');
    }

    protected function  _getDeleteSuccessMessage($record)
    {
        return __('The item was successfully deleted!');
    }
    
    protected function _getDeleteConfirmMessage($record)
    {
        return __('This will delete the item and its associated metadata. It will '
             . 'also delete all files and file metadata associated with this '
             . 'item.');
    }
    
    public function addAction()
    {
        // Get all the element sets that apply to the item.
        $this->view->elementSets = $this->_getItemElementSets();
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
            $this->_helper->flashMessenger('May not browse by specific users.');
            $this->_setParam('user', null);
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
        if ($id = $_POST['item_id']) {
            $item = $this->_helper->db->findById($id);
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
    
    /**
     * Goes to results page based off value in text input.
     */
     
    public function paginationAction()
    {
        $pageNumber = (int)$_POST['page'];
        $baseUrl = $this->getRequest()->getBaseUrl().'/items/browse/';
    	$request = Zend_Controller_Front::getInstance()->getRequest(); 
    	$requestArray = $request->getParams();        
        if($currentPage = $this->current) {
            $paginationUrl = $baseUrl.$currentPage;
        } else {
            $paginationUrl = $baseUrl;
        }

    }

}
