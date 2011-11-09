<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @subpackage Controllers
 * @author CHNM
 * @see Omeka_Controller_Action
 * @access private
 */

/**
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class ItemsController extends Omeka_Controller_Action
{
    public $contexts = array(
            'browse' => array('json', 'dcmes-xml', 'rss2', 'omeka-xml', 'omeka-json', 'atom'),
            'show'   => array('json', 'dcmes-xml', 'omeka-xml', 'omeka-json', 'atom')
    );

    private $_ajaxRequiredActions = array(
        'element-form',
        'tag-form',
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
        $this->_modelClass = 'Item';
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
        $this->view->isPartial = $this->getRequest()->isXmlHttpRequest();
        
        // If this is set to null, use the default items/browse action.
        $this->view->formActionUri = null;
        
        $this->view->formAttributes = array('id'=>'advanced-search-form');
    }
    
    protected function _getItemElementSets()
    {
        return $this->getTable('ElementSet')->findForItems();
    }
    
    /**
     * Adds an additional permissions check to the built-in edit action.
     * 
     */
    public function editAction()
    {
        // Get all the element sets that apply to the item.
        $this->view->elementSets = $this->_getItemElementSets();
        
        if ($user = $this->getCurrentUser()) {
            
            $item = $this->findById();
            
            // If the user cannot edit any given item. Check if they can edit 
            // this specific item
            if ($this->isAllowed('edit', $item)) {
                return parent::editAction();    
            }
        }
        
        $this->forbiddenAction();
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
     * Delete an item.
     *
     * Wraps the standard deleteAction in permission checks.
     */
    public function deleteAction()
    {
        if (($user = $this->getCurrentUser())) {
            $item = $this->findById();
            
            // Permission check
            if ($this->isAllowed('delete', $item)) {
                return parent::deleteAction();
            }
        }
        
        $this->_forward('forbidden');
    }
    
    /**
     * Finds all tags associated with items (used for tag cloud)
     * 
     * @return void
     */
    public function tagsAction()
    {
        $params = array_merge($this->_getAllParams(), array('type'=>'Item'));
        $tags = $this->getTable('Tag')->findBy($params);
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
        $results = $this->_helper->searchItems();
        
        /** 
         * Now process the pagination
         * 
         */
        $paginationUrl = $this->getRequest()->getBaseUrl().'/items/browse/';

        //Serve up the pagination
        $pagination = array('menu'          => null, // This hasn't done anything since $menu was never instantiated in ItemsController::browseAction()
                            'page'          => $results['page'], 
                            'per_page'      => $results['per_page'], 
                            'total_results' => $results['total_results'], 
                            'link'          => $paginationUrl);
        
        Zend_Registry::set('pagination', $pagination);
        
        fire_plugin_hook('browse_items', $results['items']);
        
        $this->view->assign(array('items'=>$results['items'], 'total_items'=>$results['total_items']));
    }
    
    public function elementFormAction()
    {
        $elementId = (int)$_POST['element_id'];
        $itemId  = (int)$_POST['item_id'];
        
        // Re-index the element form posts so that they are displayed in the correct order
        // when one is removed.
        $_POST['Elements'][$elementId] = array_merge($_POST['Elements'][$elementId]);

        $element = $this->getTable('Element')->find($elementId);
        try {
            $item = $this->findById($itemId);
        } catch (Exception $e) {
            $item = new Item;
        }
        
        $this->view->assign(compact('element', 'item'));
    }
    
    ///// AJAX ACTIONS /////
    
    /**
     * Find or create an item for this mini-form
     *
     */
    public function changeTypeAction()
    {
        if ($id = $_POST['item_id']) {
            $item = $this->findById($id);
        } else {
            $item = new Item;
        }
        
        $item->item_type_id = (int) $_POST['type_id'];
        $this->view->assign(compact('item'));
    }
    
    /**
     * Display the form for tags for a given item.
     * 
     * @return void
     */
    public function tagFormAction()
    {
        $item = $this->findById();
        $this->view->assign(compact('item'));
    }
    
    /**
     * Modify the tags for an item (add or remove).  If this is an AJAX request, it will
     * render the 'tag-list' partial, otherwise it will redirect to the
     * 'show' action.
     * 
     * @return void
     */
    public function modifyTagsAction()
    {
        $item = $this->findById();

        //Add the tags
         
        if (array_key_exists('modify_tags', $_POST) || !empty($_POST['tags'])) {
            if ($this->isAllowed('tag')) {
                $currentUser = $this->getInvokeArg('bootstrap')->getResource('Currentuser');
                $tagsAdded = $item->applyTagString($_POST['tags'], $currentUser->Entity);
                // Refresh the item.
                $item = $this->findById();
            } else {
                $this->flashError(__('User does not have permission to add tags.'));
            }
        }
        
        if (!$this->getRequest()->isXmlHttpRequest()) {
            $itemId = $this->_getParam('id');
            return $this->redirect->gotoRoute(array('controller' => 'items', 
                                                    'action'     => 'show', 
                                                    'id'         => $itemId), 'id');
        }
        
        $this->view->assign(compact('item'));
        $this->render('tag-list');
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
            $this->flashError(__('You must choose some items to batch edit.'));
            return $this->_helper->redirector->goto('browse', 'items');
        }

        $this->view->assign(compact('itemIds'));
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
                        
            if ($metadata && array_key_exists('public', $metadata) && !$this->isAllowed('makePublic')) {
                $errorMessage = 
                    __('User is not allowed to modify visibility of items.');
            }

            if ($metadata && array_key_exists('featured', $metadata) && !$this->isAllowed('makeFeatured')) {
                $errorMessage = 
                    __('User is not allowed to modify featured status of items.');
            }

            if (!$errorMessage) {
                foreach ($itemIds as $id) {
                    if ($item = $this->getTable('Item')->find($id)) {
                        if ($delete && !$this->isAllowed('delete', $item)) {
                            $errorMessage = __('User is not allowed to delete selected items.');
                            break;
                        }

                        // Check to see if anything but 'tag'
                        if ($metadata && array_diff_key($metadata, array('tags' => '')) && !$this->isAllowed('edit', $item)) {
                            $errorMessage = __('User is not allowed to edit selected items.');
                            break;
                        }

                        if ($metadata && array_key_exists('tags', $metadata) && !$this->isAllowed('tag', $item)) {
                            $errorMessage = __('User is not allowed to tag selected items.');
                            break;
                        }
                        release_object($item);
                    }
                }
            }

            $errorMessage = apply_filters('items_batch_edit_error', $errorMessage, $metadata, $custom, $itemIds);

            if ($errorMessage) {
                $this->flashError($errorMessage);
            } else {
                $dispatcher = Zend_Registry::get('job_dispatcher');
                $dispatcher->send(
                    'Item_BatchEditJob', 
                    array(
                        'itemIds' => $itemIds, 
                        'delete' => $delete, 
                        'metadata'  => $metadata, 
                        'custom' => $custom
                    )
                );
                $this->flashSuccess(__('The items were successfully changed!'));
            }
         }

         $this->_helper->redirector->goto('browse', 'items');
    }
}
