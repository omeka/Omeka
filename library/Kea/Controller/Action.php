<?php

/**
 * @edited 12/20/06 n8agrin
 */
abstract class Kea_Controller_Action
{	
	protected $before_filters = array();
	
	protected $after_filters = array();
	
	protected $_response;

	protected $_request;
	
	protected $_conn;
	
	public $validationErrors = array();
	
	/**
	 * Create a controller action object.
	 * Each controller should return one piece of information
	 * in an attempt to be RESTful.  They should not be concerned
	 * with setting all the data needed for a particular view.
	 * If they are interacting directly with a view, they should
	 * be passed a response object on instantiation in order to
	 * pass data between the two seamlessly.
	 */
	public function __construct(Kea_Controller_Response_Abstract $response)
	{
		$this->_conn = Doctrine_Manager::connection();
		$this->_response = $response;
		$this->_request = Kea_Request::getInstance();
	}
	
	final public function __get($name)
	{
		return $this->_response->__get($name);
	}
	
	final public function __set($name, $val)
	{
		return $this->_response->__set($name, $val);
	}
	
	public function setResponse(Kea_Controller_Response $response)
	{
		$this->_response = $response;
	}
	
	public function getResponse()
	{
		return $this->_response;
	}
	
	public function beforeFilter(&$method, &$args)
	{
		foreach ($this->before_filters as $filter) {
			$filter->filter($method, $args, $this);
		}
	}
	
	public function afterFilter(&$result)
	{
		foreach ($this->after_filters as $filter) {
			$filter->filter($result, $this);
		}
	}
	
	protected function attachBeforeFilter(Kea_Filter $filter)
	{
		$this->before_filters[] = $filter;
	}

	protected function attachAfterFilter(Kea_Filter $filter)
	{
		$this->after_filters[] = $filter;
	}


	/**
	 * This may need to be rethought.
	 * It may need to actually just redirect to another controller,
	 * action or template.
	 * eg pass it an array('controller'=>'foo', 'action'=>'bar')
	 */
	public function redirect($redirect_to)
	{
		if (headers_sent()) {
			throw new Kea_Action_Exception(
				'Cannot redirect because output headers have already been sent.');
			return;
		}

		header("Location: " . $redirect_to);

		// This may cause issues with uncaught exceptions, but there should be no
		// uncaught exceptions.
		exit();
	}
	
	public function validates(Kea_Domain_Model $object)
	{
		if($object->validates()) {
			return true;
		}
		
		$namespace = get_class($object);
		$errors = $object->getErrors();

		foreach ($errors as $property => $error) {
			$this->validationErrors[$namespace][$property] = $error;
		}
		
		return false;
	}
	
	public function validationErrors()
	{
		return $this->validationErrors;
	}
	
	public function addError($namespace, $property, $error)
	{
		$this->validationErrors[$namespace][$property] = $error;
	}
	
	protected function _forward($controller, $action)
	{
		$this->_request->addAction(array('controller'=>$controller, 'action'=>$action));
	}

	/**
	 * This method cannot be overwritten in order to allow for embedding of filters
	 */
	final public function __call($method, $args)
	{
		$method = '_'.$method;
		if (method_exists($this, $method)) {
			$this->beforeFilter($method, $args);
			$result = call_user_func_array(array($this, $method), $args);
			$this->afterFilter($result);
			return $result;
		} else {
			throw new Kea_Action_Exception(
				'The method ' . $method . ' doesn\'t exist in the controller '.get_class($this) . '.'
			);
		}
	}
}
?>