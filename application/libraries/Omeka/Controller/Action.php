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
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
abstract class Omeka_Controller_Action extends Zend_Controller_Action
{            
    /**
     * Omeka_Db_Table associated with the controller.
     * 
     * Initialized optionally within the init() method.
     *
     * @var Omeka_Db_Table
     */
    protected $_table;
    
    /**
     * Allows for built-in CRUD scaffolding in the controllers.
     *
     * Must be initialized within the init() method.
     *
     * @var string
     */
    protected $_modelClass;

    /**
     * Filter functions applied before dispatching to a controller action.
     *
     * @var array
     */
    protected $_beforeFilter = array();
    
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
                
        $this->redirect = $this->_helper->redirector;
                
        // Get the table obj by automatic
        if (!$this->_table && $this->_modelClass) {
            $this->_table = $this->getTable($this->_modelClass); 
        }
        
        $this->setActionContexts();
        
        return $init;
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
    protected function setActionContexts()
    {
        $contextSwitcher = $this->_helper->getHelper('contextSwitch');
        
        $contextArray = !empty($this->contexts) ? $this->contexts : array();
        
        // Plugins can hook in to add contexts to actions
        if ($broker = $this->getInvokeArg('bootstrap')->getResource('Pluginbroker')) {
            // The 'define_action_contexts' filter receives the controller
            // object as the 2st argument and the context switcher object as the
            // 3nd (in case custom modification is required).
            $contextArray = $broker->applyFilters('define_action_contexts', $contextArray,
            array($this, $contextSwitcher));
        }                
        
        // Replace the existing contexts with the filtered plugin list.
        $contextSwitcher->setActionContexts($contextArray);
        
        $contextSwitcher->initContext();        
    }
    
    /**
     * Declare a 'before' filter that will run in preDispatch() for the controller
     * 
     * @param string $function Method name within the controller.
     * @param array $except Array of actions for which this filter will not run.
     * @return void
     */    
    protected function beforeFilter($function, $except = array())
    {
        $this->_beforeFilter[$function] = $except;
    }
    
    /**
     * Run any 'before' filters prior to dispatching the action.
     * 
     * @return void
     */
    public function preDispatch()
    {                        
        $action = $this->_request->getActionName();
        foreach ($this->_beforeFilter as $func => $exceptThese) {
            if (!in_array($action, $exceptThese)) {
                if (!method_exists($this, $func)) {
                    throw new Zend_Controller_Exception('The before filter '.$func.' was not found.');
                } else {
                    $this->$func();
                }
            }
        }
    }
    
    /// CONVENIENCE METHODS ///
    
    /**
     * Retrieve the controller's DB table.
     *
     * If either {@link $_modelClass} or {@link $_table} was set in 
     * init(), calling this function with no argument will return the configured
     * table.  Otherwise, the desired model class name must be passed to this 
     * function.
     *
     * @see Zend_Controller_Action::init()
     * @see Omeka_Controller_Action::__construct()
     * @param string $table Name of the model for the table to be retrieved.
     * @return Omeka_Db_Table
     */
    public function getTable($table = null)
    {
        if(!$table and $this->_table) {
            return $this->_table;
        } else {
            return $this->getDb()->getTable($table);
        }
    }
    
    /**
     * Retrieve the database object.
     * 
     * @uses Omeka_Context
     * @return Omeka_Db
     */
    public function getDb()
    {
        return Omeka_Context::getInstance()->getDb();
    }
    
    /**
     * Retrieve the record for the current user.
     * 
     * @return User|bool User object if a user is logged in, false otherwise.
     */
    public function getCurrentUser()
    {
        return $this->getInvokeArg('bootstrap')->getResource('Currentuser');
    }
    
    /**
     * Check if an action is allowed for the current user.
     *
     * Alias for {@link Omeka_Controller_Action_Helper_Acl::isAllowed()}.
     * 
     * @param string $rule Privilege name.
     * @param string $resource Resource name. If omitted, uses the controller's
     * default resource name.
     * @return boolean
     */
    public function isAllowed($rule, $resource = null)
    {
        return $this->_helper->acl->isAllowed($rule, $resource);
    }
    
    /// FLASH METHODS ///
    
    /**
     * Set a flash message.
     * 
     * See {@link Omeka_Controller_Flash} for status and priority levels.
     * The default status level is "ALERT".
     *
     * @param string $msg Message to set.
     * @param integer $flash_code Flash message status.
     * @param integer $priority Message priority.
     * @return void
     */
    public function flash($msg = null, $flash_code = null, $priority = null)
    {
        if (!$flash_code) {
            $flash_code = Omeka_Controller_Flash::ALERT;
        }
        
        $flash = new Omeka_Controller_Flash();
        $flash->setFlash($flash_code, $msg, $priority);
    }
    
    /**
     * Set a flash message containing validation error messages.
     *
     * The message will have status level "VALIDATION_ERROR" and
     * priority "DISPLAY_NOW".
     *
     * @param Omeka_Validator_Exception $e Validator exception.
     * @param integer $priority (optional) Override message priority.
     * @return void
     */
    public function flashValidationErrors(Omeka_Validator_Exception $e, $priority = null)
    {
        if (!$priority) {
            $priority = Omeka_Controller_Flash::DISPLAY_NOW;
        }
        
        $errors = $e->getErrors();
        
        $flash = new Omeka_Controller_Flash();
        
        $flash->setFlash(Omeka_Controller_Flash::VALIDATION_ERROR, $errors, $priority);
    }
    
    /**
     * Set a flash message indicating a successful operation.
     *
     * The message will have status level "SUCCESS" and priority "DISPLAY_NEXT".
     *
     * @param string $msg Message to set.
     * @return void
     */
    public function flashSuccess($msg)
    {
        $flash = new Omeka_Controller_Flash;
        $flash->setFlash(Omeka_Controller_Flash::SUCCESS, 
                         $msg, 
                         Omeka_Controller_Flash::DISPLAY_NEXT);
    }
    
    /**
     * Set a flash message indicating a general error.
     *
     * The message will have status level "ERROR" and priority "DISPLAY_NEXT".
     *
     * @param string $msg Message to set.
     * @return void
     */
    public function flashError($msg)
    {
        $flash = new Omeka_Controller_Flash;
        $flash->setFlash(Omeka_Controller_Flash::GENERAL_ERROR, 
                         $msg, 
                         Omeka_Controller_Flash::DISPLAY_NEXT);
    }

    /// BASIC CRUD INTERFACE ///
    
    /**
     * Forward to the 'browse' action
     * 
     * @see Omeka_Controller_Action::browseAction()
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
        if (empty($this->_modelClass)) {
            throw new Exception( 'Scaffolding class has not been specified' );
        }
        
        $pluralName = $this->getPluralized();
        
        $params = $this->_getAllParams();
        
        $recordsPerPage = $this->_getBrowseRecordsPerPage();
        $currentPage = $this->_getBrowseRecordsPage();
        
        $table = $this->getTable($this->_modelClass);
        
        $records = $table->findBy($params, $recordsPerPage, $currentPage);
        
        $totalRecords = $table->count($params);
        
        Zend_Registry::set($pluralName, $records);
        
        // Fire the plugin hook
        fire_plugin_hook('browse_' . strtolower(ucwords($pluralName)),  $records);
        
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
     * Retrieve the current page of a result set that is being displayed.
     *
     * If no 'page' parameter is passed to the controller, page 1 is assumed.
     *
     * @return integer
     */
    protected function _getBrowseRecordsPage()
    {
        return $this->_getParam('page', 1);
    }
    
    /**
     * Retrieve a single record and render it.
     *
     * Every request to this action must pass a record ID in the 'id' parameter.
     *
     * @uses Omeka_Controller_Action::findById()
     * @return void
     */
    public function showAction()
    {
        $varName = strtolower($this->_modelClass);
                
        $record = $this->findById();        
        
        Zend_Registry::set($varName, $record);
        
        fire_plugin_hook('show_' . strtolower(get_class($record)), $record);
        
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
     * @uses Omeka_Controller_Action::findById()
     * @return void
     */
    public function addAction()
    {
        //Maybe this recurring bit should be abstracted out
        $varName = strtolower($this->_modelClass);
        $class = $this->_modelClass;
        
        $record = new $class();
        
        try {
            if ($record->saveForm($_POST)) {
                $successMessage = $this->_getAddSuccessMessage($record);
                if ($successMessage != '') {
                    $this->flashSuccess($successMessage);
                }
                $this->redirect->goto('browse');
            }
        } catch (Omeka_Validator_Exception $e) {
            $this->flashValidationErrors($e);
        } 
        $this->view->assign(array($varName=>$record));            
    }
    
    /**
     * Returns the success message for adding a record. 
     * Default is empty string. Subclasses should override it.
     *
     * @param Omeka_Record $record
     * @return string
     */
    protected function _getAddSuccessMessage($record) {return '';}
    
    /**
     * Returns the success message for editing a record.  
     * Default is empty string. Subclasses should override it.
     *
     * @param Omeka_Record $record 
     * @return string
     */
    protected function _getEditSuccessMessage($record) {return '';}
    
    /**
     * Returns the success message for deleting a record.
     * Default is empty string. Subclasses should override it.
     *
     * @param Omeka_Record $record 
     * @return string
     */
    protected function _getDeleteSuccessMessage($record) {return '';}     
    
    /**
     * Returns the delete confirm message for deleting a record.
     *
     * @param Omeka_Record $record
     * @return string
     */
    protected function _getDeleteConfirmMessage($record) {return '';}
    
    /**
     * Similar to 'add' action, except this requires a pre-existing record.
     * 
     * Every request to this action must pass a record ID in the 'id' parameter.
     *
     * @uses Omeka_Controller_Action::findById()
     * @return void
     */
    public function editAction()
    {
        $varName = strtolower($this->_modelClass);
        
        $record = $this->findById();
        
        try {
            if ($record->saveForm($_POST)) {
                $successMessage = $this->_getEditSuccessMessage($record);
                if ($successMessage != '') {
                    $this->flashSuccess($successMessage);
                }
                $this->redirect->goto('show', null, null, array('id'=>$record->id));
            }
        } catch (Omeka_Validator_Exception $e) {
            $this->flashValidationErrors($e);
        } 
        $this->view->assign(array($varName=>$record));        
    }
    
    /**
     * Delete a record from the database.
     * 
     * Every request to this action must pass a record ID in the 'id' parameter.
     * Find a record based on ID, delete it and redirect to 'browse' action.
     * 
     * @uses Omeka_Controller_Action::findById()
     * @return void
     */
    public function deleteAction()
    {
        if (!$this->getRequest()->isPost()) {
            $this->_forward('method-not-allowed', 'error', 'default');
            return;
        }
        
        $record = $this->findById();
        
        $form = $this->_getDeleteForm();
        
        if ($form->isValid($_POST)) { 
            $record->delete();
        } else {
            $this->_forward('error');
            return;
        }
        
        $successMessage = $this->_getDeleteSuccessMessage($record);
        if ($successMessage != '') {
            $this->flashSuccess($successMessage);
        }
        $this->redirect->goto('browse');
    }
    
    /**
     * Throw a 403 "Forbidden" exception.
     * Causes Omeka to reroute to the ErrorController.
     * 
     * @throws Omeka_Controller_Exception_403
     * @return void
     */
    public function forbiddenAction()
    {
        throw new Omeka_Controller_Exception_403();
    }
    
    /**
     * Throw a 404 "Not Found" exception.
     * Causes Omeka to reroute to the ErrorController.
     * 
     * @throws Omeka_Controller_Exception_404
     * @return void
     */
    public function errorAction()
    {
        throw new Omeka_Controller_Exception_404();
    }
    
    /**
     * Convenience method to get the pluralized form of the CRUD data model.
     * 
     * @param boolean $lower Whether or not to return the name in lowercase.
     * @return string
     */
    protected function getPluralized($lower = true)
    {
        $plural = Inflector::pluralize($this->_modelClass);
        return $lower ? strtolower($plural) : $plural;
    }
    
    /**
     * Find a particular record given its unique ID # and (optionally) its 
     * class name.  
     * 
     * @uses Omeka_Db_Table::find()
     * @uses Omeka_Db_Table::checkExists()
     * @param int $id (optional) ID of the record to find.
     * @param string $table (optional) Model class corresponding to the table
     * that should be checked.
     * @throws Omeka_Controller_Exception_404
     * @throws Omeka_Controller_Exception_403
     * @return Omeka_Record
     */
    public function findById($id = null, $table = null)
    {
        $id = (!$id) ? $this->getRequest()->getParam('id') : $id;
        
        if (!$id) {
            throw new Omeka_Controller_Exception_404(get_class($this) . ': No ID passed to this request' );
        }

        $table = !$table ? $this->_table : $this->getTable($table);            
        
        if (!$table) {
            throw new Exception('A table must be defined in order to use findById()!');
        }
        
        $record = $table->find($id);
        
        if (!$record) {
            
            //Check to see whether to record exists at all
            if (!$table->checkExists($id)) {
                throw new Omeka_Controller_Exception_404(get_class($this) . ": No record with ID # $id exists" );
            } else {
                throw new Omeka_Controller_Exception_403('You do not have permission to access this page.');
            }
            
        }
        
        return $record;
    }
    /**
     *
     */
    public function deleteConfirmAction() {
        $isPartial = $this->getRequest()->isXmlHttpRequest();
        $record = $this->findById();
        $form = $this->_getDeleteForm();
        $confirmMessage = $this->_getDeleteConfirmMessage($record);
        $this->view->assign(compact('confirmMessage','record', 'isPartial', 'form'));
        $this->render('common/delete-confirm', null, true);
    }

    protected function _getDeleteForm()
    {
        $form = new Zend_Form();
        $form->setElementDecorators(array('ViewHelper'));
        $form->removeDecorator('HtmlTag');
        $form->addElement('hash', 'confirm_delete_hash');
        $form->addElement('submit', 'Delete', array('class' => 'delete'));
        $form->setAction($this->view->url(array('action' => 'delete')));
        return $form;
    }
}
