<?php
/**
 * Abstract class for browsing strategies
 *
 * @package Omeka
 **/
abstract class Kea_Controller_Browse_Abstract implements Kea_Controller_Browse_Interface
{
	protected $_class;
	protected $_controller;
	
	/**
	 * Kea_Controller_Search
	 *
	 * @var Kea_Controller_Search
	 **/
	protected $_search;
	
	protected $_dbQuery;
	protected $_searchQuery;
	
	protected $_options = array();
	
	
	public function __construct($class, Kea_Controller_Action $controller, array $options = array() )
	{
		$this->_class = $class;
		$this->_controller = $controller;
		$this->_search = new Kea_Controller_Search($class);		
		$this->_options = array_merge($this->_options, $options);
	}
	
	public function browse() {}

	public function getQuery() {
		return $this->_query;
	}
	
	/**
	 * All refinement of the search query if necessary
	 *
	 * @return void
	 **/
	public function setSearchQuery($query) {
		$this->_searchQuery = $query;
		return $this;
	}
	
	public function setDbQuery($query) {
		$this->_dbQuery = $query;
		return $this;
	}
	
	public function getDbQuery() {
		return $this->_dbQuery;
	}
	
	public function getSearchQuery() {
		return $this->_searchQuery;
	}
	
	public function getOption($name) {
		return $this->_options[$name];
	}
	
	protected function formatPluralized() {
		return strtolower($this->_class).'s';
	}

}
?>