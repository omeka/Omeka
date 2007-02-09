<?php
/**
 * @package Omeka
 * @author Everyone and their mothers
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
	 * This should be a convenience function abstracted to Kea_Controller_Action
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

	public function init()
	{
		$this->_view = new Kea_View();
	}
}
?>