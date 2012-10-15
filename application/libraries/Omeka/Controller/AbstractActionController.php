<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Base class for Omeka controllers.
 * 
 * Provides basic create, read, update, and delete (CRUD) operations.
 * 
 * @package Omeka\Controller
 */
abstract class Omeka_Controller_AbstractActionController extends Zend_Controller_Action
{
    /**
     * The number of records to browse per page.
     * 
     * If this is left null, then results will not paginate. This is partially 
     * because not every controller will want to paginate records and also to 
     * avoid BC breaks for plugins.
     *
     * @var string
     */
    protected $_browseRecordsPerPage;

    /**
     * Base controller constructor.
     *
     * Does the following things:
     *
     * <ul>
     *   <li>Aliases the redirector helper to clean up the syntax</li>
     *   <li>Sets the table object automatically if given the class of the model 
     * to use for CRUD.</li>
     *   <li>Sets all the built-in action contexts for the CRUD actions.</li>
     * </ul>
     *
     * Instead of overriding this constructor, controller subclasses should
     * implement the init() method for initial setup.
     *
     * @see Zend_Controller_Action::init()
     * @param Zend_Controller_Request_Abstract $request Current request object.
     * @param Zend_Controller_Response_Abstract $response Response object.
     * @param array $invokeArgs Arguments passed to Zend_Controller_Action.
     */
    public function __construct(Zend_Controller_Request_Abstract $request,
                                Zend_Controller_Response_Abstract $response,
                                array $invokeArgs = array()
    ) {
        parent::__construct($request, $response, $invokeArgs);
        $response->setHeader('Content-Type', 'text/html; charset=utf-8', true);
        $this->_setActionContexts();
    }
    
    /**
     * Forward to the 'browse' action
     *
     * @see self::browseAction()
     */
    public function indexAction()
    {
        $this->_forward('browse');
    }
    
    /**
     * Retrieve and render a set of records for the controller's model.
     *
     * Using this action requires some setup:
     * 
     * <ul>
     *   <li>In your controller's <code>init()</code>, set the default model name: 
     *     <code>$this->_helper->db->setDefaultModelName('YourRecord');</code></li>
     *   <li>In your controller, set the records per page and return them using: 
     *     <code>protected function _getBrowseRecordsPerPage()</code></li>
     *   <li>In your table record, filter the select object using the provided 
     *     parameters using: 
     *     <code>public function applySearchFilters($select, $params)</code></li>
     * </ul>
     *
     * @uses Omeka_Controller_Action_Helper_Db::getDefaultModelName()
     * @uses Omeka_Db_Table::findBy()
     */
    public function browseAction()
    {
        // Respect only GET parameters when browsing.
        $this->getRequest()->setParamSources(array('_GET'));
        
        // Inflect the record type from the model name.
        $pluralName = $this->view->pluralize($this->_helper->db->getDefaultModelName());
        
        $params = $this->getAllParams();
        $recordsPerPage = $this->_getBrowseRecordsPerPage();
        $currentPage = $this->getParam('page', 1);
        
        // Get the records filtered to Omeka_Db_Table::applySearchFilters().
        $records = $this->_helper->db->findBy($params, $recordsPerPage, $currentPage);
        $totalRecords = $this->_helper->db->count($params);
        
        // Fire the specific browse records hook.
        fire_plugin_hook("browse_$pluralName", array('records' => $records));
        
        // Add pagination data to the registry. Used by pagination_links().
        if ($recordsPerPage) {
            Zend_Registry::set('pagination', array(
                'page' => $currentPage, 
                'per_page' => $recordsPerPage, 
                'total_results' => $totalRecords, 
            ));
        }
        
        $this->view->assign(array($pluralName => $records, 'total_records' => $totalRecords));
    }
    
    /**
     * Retrieve a single record and render it.
     * 
     * Every request to this action must pass a record ID in the 'id' parameter.
     *
     * @uses Omeka_Controller_Action_Helper_Db::getDefaultModelName()
     * @uses Omeka_Controller_Action_Helper_Db::findById()
     */
    public function showAction()
    {
        $singularName = $this->view->singularize($this->_helper->db->getDefaultModelName());
        $record = $this->_helper->db->findById();
        
        // Fire the specific show record hook.
        fire_plugin_hook("show_$singularName", array('record' => $record));
        
        $this->view->assign(array($singularName => $record));
    }
    
    /**
     * Add an instance of a record to the database.
     *
     * This behaves differently based on the contents of the $_POST superglobal.
     * If the $_POST is empty or invalid, it will render the form used for data 
     * entry. Otherwise, if the $_POST exists and is valid, it will save the new
     * record and redirect to the 'browse' action.
     *
     * @uses Omeka_Controller_Action_Helper_Db::getDefaultModelName()
     * @uses self::_getAddSuccessMessage()
     * @uses self::_redirectAfterAdd()
     */
    public function addAction()
    {
        $class = $this->_helper->db->getDefaultModelName();
        $varName = $this->view->singularize($class);
        
        $record = new $class();
        if ($this->getRequest()->isPost()) {
            $record->setPostData($_POST);
            if ($record->save(false)) {
                $successMessage = $this->_getAddSuccessMessage($record);
                if ($successMessage != '') {
                    $this->_helper->flashMessenger($successMessage, 'success');
                }
                $this->_redirectAfterAdd($record);
            } else {
                $this->_helper->flashMessenger($record->getErrors());
            }
        }
        $this->view->$varName = $record;
    }
    
    /**
     * Similar to 'add' action, except this requires a pre-existing record.
     *
     * Every request to this action must pass a record ID in the 'id' parameter.
     *
     * @uses Omeka_Controller_Action_Helper_Db::getDefaultModelName()
     * @uses Omeka_Controller_Action_Helper_Db::findById()
     * @uses self::_getEditSuccessMessage()
     * @uses self::_redirectAfterEdit()
     */
    public function editAction()
    {
        $varName = $this->view->singularize($this->_helper->db->getDefaultModelName());
        
        $record = $this->_helper->db->findById();
        
        if ($this->getRequest()->isPost()) {
            $record->setPostData($_POST);
            if ($record->save(false)) {
                $successMessage = $this->_getEditSuccessMessage($record);
                if ($successMessage != '') {
                    $this->_helper->flashMessenger($successMessage, 'success');
                }
                $this->_redirectAfterEdit($record);
            } else {
                $this->_helper->flashMessenger($record->getErrors());
            }
        }
        
        $this->view->$varName = $record;
    }
    
    /**
     * Ask for user confirmation before deleting a record.
     * 
     * @uses Omeka_Controller_Action_Helper_Db::findById()
     * @uses self::_getDeleteConfirmMessage()
     */
    public function deleteConfirmAction()
    {
        $isPartial = $this->getRequest()->isXmlHttpRequest();
        $record = $this->_helper->db->findById();
        $form = $this->_getDeleteForm();
        $confirmMessage = $this->_getDeleteConfirmMessage($record);

        $this->view->assign(compact('confirmMessage','record', 'isPartial', 'form'));
        $this->render('common/delete-confirm', null, true);
    }
    
    /**
     * Delete a record from the database.
     *
     * Every request to this action must pass a record ID in the 'id' parameter.
     *
     * @uses Omeka_Controller_Action_Helper_Db::findById()
     * @uses self::_getDeleteSuccessMessage()
     * @uses self::_redirectAfterDelete()
     */
    public function deleteAction()
    {
        if (!$this->getRequest()->isPost()) {
            $this->_forward('method-not-allowed', 'error', 'default');
            return;
        }
        
        $record = $this->_helper->db->findById();
        
        $form = $this->_getDeleteForm();
        if ($form->isValid($_POST)) {
            $record->delete();
        } else {
            throw new Omeka_Controller_Exception_404;
        }
        
        $successMessage = $this->_getDeleteSuccessMessage($record);
        if ($successMessage != '') {
            $this->_helper->flashMessenger($successMessage, 'success');
        }
        $this->_redirectAfterDelete($record);
    }
    
    /**
     * Return the record for the current user.
     *
     * @return User|bool User object if a user is logged in, false otherwise.
     */
    public function getCurrentUser()
    {
        return $this->getInvokeArg('bootstrap')->getResource('Currentuser');
    }
    
    /**
     * Return the number of records to display per page.
     *
     * By default this will return null, disabling pagination. This can be 
     * overridden in subclasses by redefining this method.
     *
     * @return integer|null
     */
    protected function _getBrowseRecordsPerPage()
    {
        return $this->_browseRecordsPerPage;
    }

    /**
     * Return the success message for adding a record.
     * 
     * Default is empty string. Subclasses should override it.
     *
     * @param Omeka_Record_AbstractRecord $record
     * @return string
     */
    protected function _getAddSuccessMessage($record)
    {
        return '';
    }
    
    /**
     * Return the success message for editing a record.
     * 
     * Default is empty string. Subclasses should override it.
     *
     * @param Omeka_Record_AbstractRecord $record
     * @return string
     */
    protected function _getEditSuccessMessage($record)
    {
        return '';
    }
    
    /**
     * Return the success message for deleting a record.
     * 
     * Default is empty string. Subclasses should override it.
     *
     * @param Omeka_Record_AbstractRecord $record
     * @return string
     */
    protected function _getDeleteSuccessMessage($record)
    {
        return '';
    }
    
    /**
     * Return the delete confirm message for deleting a record.
     *
     * @param Omeka_Record_AbstractRecord $record
     * @return string
     */
    protected function _getDeleteConfirmMessage($record)
    {
        return '';
    }
    
    /**
     * Redirect to another page after a record is successfully added.
     *
     * The default is to reidrect to this controller's browse page.
     *
     * @param Omeka_Record_AbstractRecord $record
     */
    protected function _redirectAfterAdd($record)
    {
        $this->_helper->redirector('browse');
    }

    /**
     * Redirect to another page after a record is successfully edited.
     *
     * The default is to redirect to this record's show page.
     *
     * @param Omeka_Record_AbstractRecord $record
     */
    protected function _redirectAfterEdit($record)
    {
        $this->_helper->redirector('show', null, null, array('id'=>$record->id));
    }

    /**
     * Redirect to another page after a record is successfully deleted.
     *
     * The default is to redirect to this controller's browse page.
     *
     * @param Omeka_Record_AbstractRecord $record
     */
    protected function _redirectAfterDelete($record)
    {
        $this->_helper->redirector('browse');
    }

    /**
     * Augment Zend's default action contexts.
     *
     * Passes Omeka's default additional contexts through the
     * 'define_action_contexts' filter to allow plugins to add contexts.
     */
    protected function _setActionContexts()
    {
        $contextSwitcher = $this->_helper->getHelper('contextSwitch');
        $contextArray = !empty($this->contexts) ? $this->contexts : array();
        
        // Plugins can hook in to add contexts to actions
        if ($broker = $this->getInvokeArg('bootstrap')->getResource('Pluginbroker')) {
            // The 'define_action_contexts' filter receives the controller
            // object as the 2st argument and the context switcher object as the
            // 3nd (in case custom modification is required).
            $contextArray = $broker->applyFilters(
                'define_action_contexts', 
                $contextArray,
                array('controller' => $this, 'context_switcher' => $contextSwitcher)
            );
        }
        
        // Replace the existing contexts with the filtered plugin list.
        $contextSwitcher->setActionContexts($contextArray);
        $contextSwitcher->initContext();
    }

    /**
     * Get the form used for confirming deletions.
     *
     * @see deleteConfirmAction()
     * @see deleteAction()
     * @return Zend_Form
     */
    protected function _getDeleteForm()
    {
        $form = new Zend_Form();
        $form->setElementDecorators(array('ViewHelper'));
        $form->removeDecorator('HtmlTag');
        $form->addElement('hash', 'confirm_delete_hash');
        $form->addElement('submit', 'Delete', array('class' => 'delete red button'));
        $form->setAction($this->view->url(array('action' => 'delete')));
        return $form;
    }
}
