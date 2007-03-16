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

	protected $_options = array();
	
	protected $_sql = array();
	
	protected $_terms;
	
	public function __construct($class, Kea_Controller_Action $controller, array $options = array() )
	{
		$this->_class = $class;
		$this->_controller = $controller;
		$this->_options = array_merge($this->_options, $options);
	}
	
	public function browse() {}
		
	public function getOption($name) {
		if (isset($this->_options[$name])) {
			return $this->_options[$name];
		}
	}
	
	protected function formatPluralized() {
		return strtolower($this->_class).'s';
	}
	
	public function addSql($part, $val) {
		$this->_sql[$part][count($this->_sql[$part])] = $val;
	}
	
	/**
	 * @todo option of parsing in boolean mode or with query expansion?
	 * @todo add plugin hooks to add sql to search
	 * 
	 * @todo Why in the sam hell do I have to replicate every Select object ever
	 * @return void
	 **/	
	public function buildQuery() {		
		$tableName = Doctrine_Manager::getInstance()->getTable($this->_class)->getTableName();
		
		//Here is the search business
		if($terms = $_REQUEST['search']) {
			$this->addSql('join', "{$tableName}_fulltext ON {$tableName}_fulltext.id = {$tableName}.id");
			$this->addSql('where', "MATCH({$tableName}_fulltext.text) AGAINST('{$terms}' IN BOOLEAN MODE)");			
		}
		
		$query = new Doctrine_RawSql();
		$sql = "SELECT {{$tableName}.*} FROM $tableName ";
		
		if($joins = $this->_sql['join']) {
			foreach($joins as $join) {
				$sql .= " LEFT JOIN $join";
			}			
		}
		if($wheres = $this->_sql['where']) {
			$sql .= ' WHERE '.implode(' AND ', $wheres);
		}

		if($limit = $this->_sql['limit'][0]) {
			$sql .= " LIMIT $limit";
		}
		if($offset = $this->_sql['offset'][0]) {
			$sql .= " OFFSET $offset";
		}
		
		//@todo Add having, orderby
		
		$query->parseQuery($sql);	
		
		$query->addComponent($tableName, $this->_class);
		return $query;
	}
	
	public function getClass() {
		return $this->_class;
	}
}
?>