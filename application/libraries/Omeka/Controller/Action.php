<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * @see Zend_Controller_Action
 */
require_once 'Zend/Controller/Action.php';

/**
 * Base class for Omeka CRUD Controllers.
 * 
 * @abstract
 * @package Omeka
 * @author CHNM
 **/
abstract class Omeka_Controller_Action extends Zend_Controller_Action
{			
	/**
	 * Omeka_Db_Table associated with the controller (initialized optionally within the init() method)
	 *
	 * @var Omeka_Db_Table
	 **/
	protected $_table;
	
	/**
	 * Allows for builtin CRUD scaffolding in the controllers (must be initialized within the init() method)
	 *
	 * @var string
	 **/
	protected $_modelClass;

	/**
	 * Before filter applies a named method to the controller
	 * before calling the actual method.
	 **/
	protected $_beforeFilter = array();
	
	/**
	 * Does the following things:
	 * 
	 * Aliases the redirector helper to clean up the syntax (is this bad?)
	 * Sets the table object automatically if given the class of the model to use for CRUD
	 * Sets all the built-in action contexts for the CRUD actions
	 * 
	 * @param string
	 * @return void
	 **/		
	public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
	{		
		// Zend_Controller_Action __construct finishes by running init()
		$init = parent::__construct($request, $response, $invokeArgs);
				
		$this->redirect = $this->_helper->redirector;
				
		//Get the table obj by automatic
		if( (!$this->_table) and $this->_modelClass) {
			$this->_table = $this->getTable($this->_modelClass); 
		}
		
        $this->setActionContexts();
		
		return $init;
	}
	
	/**
	 * @link http://framework.zend.com/manual/en/zend.controller.actionhelpers.html#zend.controller.actionhelpers.contextswitch
	 * @uses Omeka_Context::getPluginBroker()
	 * @return void
	 **/
	protected function setActionContexts()
	{
        $contextSwitch = $this->_helper->getHelper('contextSwitch');
        $contextSwitch->addActionContexts(array(
                'browse'     => array('xml', 'json', 'dc', 'rss2'),
                'show'      => array('xml', 'json', 'dc')));
        
        //Plugins can hook in to add contexts to actions
        if($broker = Omeka_Context::getInstance()->getPluginBroker()) {
            $broker->add_action_contexts($contextSwitch);
        }
            
        $contextSwitch->initContext();	    
	}
	
	/**
	 * Declare a before filter that will run in preDispatch() for the controller
	 * 
	 * @param string Method name within the controller
	 * @param array Array of actions for which this filter will not run
	 * @return void
	 **/	
	protected function beforeFilter($function, $except = array())
	{
		$this->_beforeFilter[$function] = $except;
	}
	
	/**
	 * Run any before filters prior to dispatching the action
	 * 
	 * @return void
	 **/
	public function preDispatch()
	{						
		$action = $this->_request->getActionName();
		foreach ($this->_beforeFilter as $func => $exceptThese) {
			if (!in_array($action, $exceptThese)) {
				if (!method_exists($this, $func)) {
					throw new Zend_Controller_Exception('The before filter '.$func.' was not found.');
				}
				else {
					$this->$func();
				}
			}
		}
	}
	
	/**
	 * CONVENIENCE METHODS
	 **/
	
	/**
	 * Retrieve the table for queries
	 * 
	 **/
	public function getTable($table = null)
	{
		if(!$table and $this->_table) {
			return $this->_table;
		}
		else {
			return $this->getDb()->getTable($table);
		}
	}
	
	public function getDb()
	{
	    return Omeka_Context::getInstance()->getDb();
	}
	
	public function getCurrentUser()
	{
	    return Omeka_Context::getInstance()->getCurrentUser();
	}
	
	/**
	 * Alias for $this->_helper->acl->isAllowed()
	 * 
	 * @param string $rule
	 * @param string $resource
	 * @return boolean
	 **/
	public function isAllowed($rule, $resource=null)
	{
	    return $this->_helper->acl->isAllowed($rule, $resource);
	}
	
	/**
	 * Flash Methods
	 **/
	
	public function flash($msg=null, $flash_code=null, $priority=null)
	{
		if(!$flash_code) $flash_code = Omeka_Controller_Flash::ALERT;
		
		$flash = new Omeka_Controller_Flash();
		$flash->setFlash($flash_code, $msg, $priority);
	}
	
	public function flashValidationErrors($e, $priority=null)
	{
		if(!$priority) $priority = Omeka_Controller_Flash::DISPLAY_NOW;
		
		$errors = $e->getErrors();
		
		$flash = new Omeka_Controller_Flash();
		
		$flash->setFlash(Omeka_Controller_Flash::VALIDATION_ERROR, $errors, $priority);
	}
	
	public function flashSuccess($msg)
	{
		$flash = new Omeka_Controller_Flash;
		$flash->setFlash(Omeka_Controller_Flash::SUCCESS, $msg, Omeka_Controller_Flash::DISPLAY_NEXT);
	}
	
	///// BASIC CRUD INTERFACE /////
	
	/**
	 * The index action of every controller will forward to the 'browse' action
	 * 
	 * @return void
	 **/
	public function indexAction()
	{
        $this->_forward('browse');
	}
    
    /**
     * Retrieves and renders a set of records for the controller's model
     * 
     * Uses inflection based on the model class in order to determine
     * which records to retrieve.  Registers the set of records with the
     * pluralized name.  Retrieves all records by default.
     *
     * @todo Incorporate pagination into this CRUD method so that all CRUD controllers paginate by default
     * @return void
     **/
	public function browseAction()
	{		
		if(empty($this->_modelClass)) throw new Exception( 'Scaffolding class has not been specified' );
		
		$pluralName = $this->getPluralized();
        
        $params = $this->_getAllParams();
                
		$records = $this->getTable($this->_modelClass)->findBy($params);
		
		$totalRecords = count($records);
		
		Zend_Registry::set($pluralName, $records);
		
		//Fire the plugin hook
		fire_plugin_hook('browse_' . strtolower(ucwords($pluralName)),  $records);
				
		return $this->render(array($pluralName=>$records, 'total_records'=>$totalRecords));
	}
	
	/**
	 * Retrieve a single record and render it
	 * Every request to this action should pass an 'id' parameter.
	 *
	 * @return void
	 **/
	public function showAction()
	{
		$varName = strtolower($this->_modelClass);
				
		$record = $this->findById();		
		
		Zend_Registry::set($varName, $record);
		
		fire_plugin_hook( 'show_' . strtolower(get_class($record)), $record );
		
		return $this->render(array($varName=>$record));
	}
	
	/**
	 * Add an instance of a record to the database.
	 * This behaves differently based on the contents of the $_POST superglobal.
	 * If the $_POST is empty or invalid, it will render the form used for data entry.
	 * Otherwise, if the $_POST exists and is valid, it will save the new 
	 * record and redirect to the 'browse' action.
	 * 
	 * @return void
	 **/
	public function addAction()
	{
		//Maybe this recurring bit should be abstracted out
		$varName = strtolower($this->_modelClass);
		$class = $this->_modelClass;
		
		$record = new $class();
		
		try {
			if($record->saveForm($_POST))
			{
				$this->redirect->goto('browse');
			}
		} 
		catch (Omeka_Validator_Exception $e)
		{
			$this->flashValidationErrors($e);
		}
		catch (Exception $e) {
			$this->flash($e->getMessage());
		}

		return $this->render(array($varName=>$record));			
	}
	
	/**
	 * Similar to 'add' action, except this requires a pre-existing record.
	 * 
	 * The ID For this record must be passed via the 'id' parameter.
	 *
	 * @return void
	 **/
	public function editAction()
	{
		$varName = strtolower($this->_modelClass);
		
		$record = $this->findById();
		
		try {
			if($record->saveForm($_POST))
			{	
				$this->redirect->goto('show', null,null, array('id'=>$record->id));
			}
		} catch (Omeka_Validator_Exception $e) {
			$this->flashValidationErrors($e);
		}
		catch (Exception $e) {
			$this->flash($e->getMessage());
		}
		
		return $this->render(array($varName=>$record));		
	}
	
	/**
	 * Find a record based on ID, delete it and redirect to 'browse' action
	 * 
	 * @return void
	 **/
	public function deleteAction()
	{		
		$record = $this->findById();			
		$record->delete();
		$this->redirect->goto('browse');
	}
	
	/**
	 * Throws an exception that causes Omeka to reroute to the ErrorController
	 * 
	 * @throws Omeka_Controller_Exception_403
	 * @return void
	 **/
	public function forbiddenAction()
	{
		throw new Omeka_Controller_Exception_403();
	}
	
	/**
	 * Throws an exception that causes Omeka to reroute to the ErrorController
	 * 
	 * @throws Omeka_Controller_Exception_403
	 * @return void
	 **/
	public function errorAction()
	{
		throw new Omeka_Controller_Exception_404();
	}
	
	/**
	 * Convenience method to get the pluralized form of the CRUD data model
	 * 
	 * @param boolean Whether or not to return the name in lowercase
	 * @return string
	 **/
	protected function getPluralized($lower=true)
	{
        $plural = Inflector::pluralize($this->_modelClass);
        return $lower ? strtolower($plural) : $plural;
	}
	
	///// END BASIC CRUD INTERFACE /////
	
	/**
	 * Mostly an alias for Zend_Controller_Action::render().  
	 * 
	 * Differences are that it 
	 * 
	 * @param string
	 * @return void
	 **/
	public function render(array $vars = array(), $action=null, $name=null, $noController=null)
	{	
		$this->view->assign($vars);

	    //Take advantage of built-in behavior
	    if($action) {
	        return parent::render($action,$name,$noController);
	    }
	}
	
	/**
	 * Find a particular record given its unique ID # and (optionally) its class name.  
	 * 
	 * @param int The ID of the record to find (optional)
	 * @param string The model class corresponding to the table that should be checked (optional)
	 * @throws Omeka_Controller_Exception_404
	 * @throws Omeka_Controller_Exception_403
	 * @return Omeka_Record
	 **/
	public function findById($id=null, $table=null)
	{
		$id = (!$id) ? $this->getRequest()->getParam('id') : $id;
		
		if(!$id) throw new Omeka_Controller_Exception_404( get_class($this).': No ID passed to this request' );
					
		$table = !$table ? $this->_table : $this->getTable($table);			
		
		$record = $table->find($id);
		
		if(!$record) {
			
			//Check to see whether to record exists at all
			if(!$table->checkExists($id)) {
				throw new Omeka_Controller_Exception_404( get_class($this).": No record with ID # $id exists" );
			}
			else {
				throw new Omeka_Controller_Exception_403( 'You do not have permission to access this page.' );
			}
			
		}
		
		return $record;
	}
}