<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Base class for Omeka controllers.
 * Provides basic create, read, update, and delete (CRUD) operations.
 *
 * @package Omeka
 */
abstract class Omeka_Controller_AbstractActionController extends Zend_Controller_Action
{
    /**
     * The number of records to browse per page.
     * If this is left null, then results will not paginate.
     *
     * This is partially because not every controller will want to paginate
     * records and also to avoid BC breaks for plugins.
     *
     * @var string
     */
    protected $_browseRecordsPerPage;

    /**
     * Base controller constructor.
     *
     * Does the following things:
     *
     *  - Aliases the redirector helper to clean up the syntax (is this bad?)
     *  - Sets the table object automatically if given the class of the model to
     *    use for CRUD.
     *  - Sets all the built-in action contexts for the CRUD actions.
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
                                array $invokeArgs = array())
    {
        // Zend_Controller_Action __construct finishes by running init()
        $init = parent::__construct($request, $response, $invokeArgs);

        $response->setHeader('Content-Type', 'text/html; charset=utf-8', true);

        $this->_setActionContexts();

        return $init;
    }

    //==================//
    // Built-in Actions //
    //==================//

    /**
     * Forward to the 'browse' action
     *
     * @see Omeka_Controller_AbstractActionController::browseAction()
     * @return void
     */
    public function indexAction()
    {
        $this->_forward('browse');
    }

    /**
     * Retrieve and renders a set of records for the controller's model.
     *
     * Uses inflection based on the model class in order to determine
     * which records to retrieve.  Registers the set of records with the
     * pluralized name.  Retrieves all records by default.
     *
     * The model class must be set to use this action.
     *
     * @return void
     */
    public function browseAction()
    {
        // Only respect GET params when browsing.
        $this->getRequest()->setParamSources(array('_GET'));

        $pluralName = $this->view->pluralize($this->_helper->db->getDefaultModelName());

        $params = $this->_getAllParams();

        $recordsPerPage = $this->_getBrowseRecordsPerPage();
        $currentPage = $this->_getParam('page', 1);

        $records = $this->_helper->db->findBy($params, $recordsPerPage, $currentPage);

        $totalRecords = $this->_helper->db->count($params);

        Zend_Registry::set($pluralName, $records);

        // Fire the plugin hook
        fire_plugin_hook('browse_' . strtolower(ucwords($pluralName)), 
                         array('records' => $records));

        // If we are using the pagination, we'll need to set some info in the
        // registry.
        if ($recordsPerPage) {
            $pagination = array('page'          => $currentPage,
                                'per_page'      => $recordsPerPage,
                                'total_results' => $totalRecords);
            Zend_Registry::set('pagination', $pagination);
        }

        $this->view->assign(array($pluralName     => $records,
                                  'total_records' => $totalRecords));
    }

    /**
     * Retrieve a single record and render it.
     *
     * Every request to this action must pass a record ID in the 'id' parameter.
     *
     * @uses Omeka_Controller_Action_Helper_Db::findById()
     * @return void
     */
    public function showAction()
    {
        $dbHelper = $this->_helper->db;
        $varName = $this->view->singularize($dbHelper->getDefaultModelName());

        $record = $dbHelper->findById();

        Zend_Registry::set($varName, $record);

        fire_plugin_hook('show_' . strtolower(get_class($record)), array('record' => $record));

        $this->view->assign(array($varName => $record));
    }

    /**
     * Add an instance of a record to the database.
     *
     * This behaves differently based on the contents of the $_POST superglobal.
     * If the $_POST is empty or invalid, it will render the form used for data entry.
     * Otherwise, if the $_POST exists and is valid, it will save the new
     * record and redirect to the 'browse' action.
     *
     * @return void
     */
    public function addAction()
    {
        $class = $this->_helper->db->getDefaultModelName();

        $record = new $class();
        if ($this->getRequest()->isPost()) {
            try {
                $record->setPostData($_POST);
                if ($record->save()) {
                    $successMessage = $this->_getAddSuccessMessage($record);
                    if ($successMessage != '') {
                        $this->_helper->flashMessenger($successMessage, 'success');
                    }
                    $this->_redirectAfterAdd($record);
                }
            } catch (Omeka_Validator_Exception $e) {
                $this->_helper->flashMessenger($e);
            }
        }
        $this->view->assign(array(strtolower($class)=>$record));
    }

    /**
     * Similar to 'add' action, except this requires a pre-existing record.
     *
     * Every request to this action must pass a record ID in the 'id' parameter.
     *
     * @uses Omeka_Controller_Action_Helper_Db::findById()
     * @return void
     */
    public function editAction()
    {
        $varName = strtolower($this->_helper->db->getDefaultModelName());

        $record = $this->_helper->db->findById();
        
        if ($this->getRequest()->isPost()) {
            try {
                $record->setPostData($_POST);
                if ($record->save()) {
                    $successMessage = $this->_getEditSuccessMessage($record);
                    if ($successMessage != '') {
                        $this->_helper->flashMessenger($successMessage, 'success');
                    }
                    $this->_redirectAfterEdit($record);
                }
            } catch (Omeka_Validator_Exception $e) {
                $this->_helper->flashMessenger($e);
            }
        }
        
        $this->view->assign(array($varName=>$record));
    }

    /**
     * Ask for user confirmation before deleting a record.
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
     * Find a record based on ID, delete it and redirect to 'browse' action.
     *
     * @uses Omeka_Controller_Action_Helper_Db::findById()
     * @return void
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

    //=====================//
    // Convenience Methods //
    //=====================//

    /**
     * Retrieve the record for the current user.
     *
     * @return User|bool User object if a user is logged in, false otherwise.
     */
    public function getCurrentUser()
    {
        return $this->getInvokeArg('bootstrap')->getResource('Currentuser');
    }

    //==================//
    // Template Methods //
    //==================//

    /**
     * Retrieve the number of records to display per page.
     *
     * By default, this will return null, disabling pagination.
     *
     * @return integer|null
     */
    protected function _getBrowseRecordsPerPage()
    {
        // Don't care about the 'per_page' query parameter by default.
        // This can be overridden in subclasses by redefining this method.
        return $this->_browseRecordsPerPage;
    }

    /**
     * Returns the success message for adding a record.
     * Default is empty string. Subclasses should override it.
     *
     * @param Omeka_Record_AbstractRecord $record
     * @return string
     */
    protected function _getAddSuccessMessage($record) {return '';}

    /**
     * Returns the success message for editing a record.
     * Default is empty string. Subclasses should override it.
     *
     * @param Omeka_Record_AbstractRecord $record
     * @return string
     */
    protected function _getEditSuccessMessage($record) {return '';}

    /**
     * Returns the success message for deleting a record.
     * Default is empty string. Subclasses should override it.
     *
     * @param Omeka_Record_AbstractRecord $record
     * @return string
     */
    protected function _getDeleteSuccessMessage($record) {return '';}

    /**
     * Returns the delete confirm message for deleting a record.
     *
     * @param Omeka_Record_AbstractRecord $record
     * @return string
     */
    protected function _getDeleteConfirmMessage($record) {return '';}

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
     *
     * @link http://framework.zend.com/manual/en/zend.controller.actionhelpers.html#zend.controller.actionhelpers.contextswitch
     * @return void
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
