<?php
/**
 * @package Omeka
 */
require_once 'Zend/Controller/Action.php';
abstract class Kea_Controller_Action extends Zend_Controller_Action
{
	/**
	 * @var protected methods array
	 */
	protected $_protected = array();
	
	/**
	 * @var Kea_View
	 */
	protected $_view;
	
	/**
	 * Doctrine_Table associated with the controller (initialized optionally within the init() method)
	 *
	 * @var Doctrine_Table
	 **/
	protected $_table;
	
	/**
	 * Most convenient usage would be something like: $this->render("show.php", compact("items", "total", "foo", "bar"));
	 *
	 * @param string The page, including .php extension
	 * @param array The variables to be included on that page, where key = name and value = contents.  see compact()
	 * @return void
	 * @author Kris Kelly
	 **/
	public function render($page, array $vars)
	{
		$this->_view->assign($vars);
		$this->getResponse()->appendBody($this->_view->render($page));
	}
	
	public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
	{
		parent::__construct($request,$response,$invokeArgs);
		$this->_view = new Kea_View();
	}
	
	/**
	 * Find a particular record given its unique ID # and (optionally) its class name.  Essentially a convenience method
	 * $this->_table must be initialized in the init() method if the particular model is to be chosen automagically
	 *
	 * @return Kea_Record
	 **/
	public function findById($id=null, $table=null)
	{
		$id = (!$id) ? $this->getRequest()->getParam('id') : $id;
		
		if(!$id) throw new Exception( 'No ID passed to this request' );
		
		if(!$table) {
			return $this->_table->find($id);
		}else {
			return Doctrine_Manager::getInstance()->getTable($table)->find($id);
		}
	}
}
?>