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

	protected $_options = array();
			
	protected $_query;
		
	protected $_table;
	
	protected $_isModified = false;
	
	public function __construct($class, Kea_Controller_Action $controller, array $options = array() )
	{
		$this->_class = $class;
		$this->_controller = $controller;
		$this->_options = array_merge($this->_options, $this->getRequest()->getParams());
		$this->_table = $table = Doctrine_Manager::getInstance()->getTable($this->_class);
		$this->_query = $table->createQuery();
	}
	
	public function getRequest()
	{
		return $this->_controller->getRequest();
	}
	
	public function getQuery() {
		return $this->_query;
	}
	
	public function setQuery($query) {
		$this->_query = $query;
	}
	
	/**
	 * So you can call stuff like $this->where('active = 1') as opposed to $this->getQuery()->where('active = 1')
	 *
	 * @return mixed
	 **/
	public function __call($m, $a) {
		$this->_isModified = true;
		return call_user_func_array(array($this->_query, $m), $a);
	}
	
	public function isCustomQuery() {
		return $this->_isModified;
	}
	
	public function browse() {}
		
	public function getOption($name) {
		if (isset($this->_options[$name])) {
			return $this->_options[$name];
		}
	}
	
	/**
	 * @todo add check for alternative pluralized spelling
	 *
	 * @return string
	 **/
	protected function formatPluralized() {
		return strtolower($this->_class).'s';
	}

	/**
	 * @todo add plugin hooks to add sql to search
	 * 
	 * @todo MySQL search may be able to switch out w/ Lucene if Lucene search can be convinced to return a query instead of a set of objects
	 * @param Doctrine_Query
	 * @return void
	 **/	
	public function buildQuery($query=null) {		
		$tableName = $this->_tableName;
		
		if(!$query)
			$query = $this->getQuery();
		
		//Here is the search business
		if($terms = $_REQUEST['search']) {
			$fulltextClass = $this->_class.'sFulltext';
			if($this->_table->hasRelation($fulltextClass)) {
				$query->innerJoin($this->_class.'.'.$fulltextClass.' full');
				$query->addWhere("MATCH (full.text) AGAINST (:search)", array('search'=>$terms));
			}
		
		}
		

		
		return $query;
	}
	
	public function getClass() {
		return $this->_class;
	}
}
?>