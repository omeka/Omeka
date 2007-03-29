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
		
	protected $_tableName;
	
	protected $_isModified = false;
	
	public function __construct($class, Kea_Controller_Action $controller, array $options = array() )
	{
		$this->_class = $class;
		$this->_controller = $controller;
		$this->_options = array_merge($this->_options, $options);
		$this->_query = new Doctrine_RawSql();
		$tableName = Doctrine_Manager::getInstance()->getTable($this->_class)->getTableName();
		$this->_tableName = $tableName;
		$sql = "SELECT {{$tableName}.*} FROM $tableName ";
		$this->_query->parseQuery($sql);	
		$this->_query->addComponent($tableName, $this->_class);
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
	 * @todo if we need complicated search criteria (like boolean mode or query expansion, other search criteria nonsense) we may need to go about it in a roundabout fashion, i.e. make a custom query that returns only IDs, then using Doctrine to hydrate those IDs
	 * @todo MySQL search may be able to switch out w/ Lucene if Lucene search can be convinced to return a query instead of a set of objects
	 * @param Doctrine_RawSql
	 * @return void
	 **/	
	public function buildQuery($query=null) {		
		$tableName = $this->_tableName;
		
		if(!$query)
			$query = $this->getQuery();
		
		//Here is the search business
		if($terms = $_REQUEST['search']) {
			$query->join("{$tableName}_fulltext ON {$tableName}_fulltext.id = {$tableName}.id");
			$query->where("MATCH({$tableName}_fulltext.text) AGAINST(:search)");
			$query->addParam('search', $terms);			
		}
		

		
		return $query;
	}
	
	public function getClass() {
		return $this->_class;
	}
}
?>