<?php
/**
 * Abstract Search class (may condense later if speed is an issue)
 *
 * @package Omeka
 * @author CHNM
 **/
abstract class Kea_Controller_Search_Abstract
{
	
	/**
	 * Current page of results (if paginating)
	 *
	 * @var int
	 **/
	public $page;
	
	/**
	 * Number of results per page (if applicable)
	 *
	 * @var int
	 **/
	public $per_page;
	
	/**
	 * Offset for search results
	 *
	 * @var int
	 **/
	public $offset;
	
	/**
	 * Search terms to find
	 *
	 * @var string
	 **/
	public $terms;
	
	protected $_sql = array();
	
	/**
	 * Search that specifies a particular class of Record will only return results of that type 
	 *
	 * @var string
	 **/
	protected $_targetClass;
	
	public function __construct($targetClass, $browse) {
		$this->_targetClass = $targetClass;
		$this->_browse = $browse;
	}
	
	/**
	 * Retrieve the current number of records that were searched over
	 *
	 * @return int
	 **/
	abstract public function getTotal();
	
	/**
	 * Set the target class for search results
	 *
	 * @return void
	 **/
	public function setTarget($class) {
		$this->_targetClass = $class;
	}
	
	/**
	 * Return the results in a Doctrine_Collection
	 *
	 * @return Doctrine_Collection
	 **/
	abstract public function run();
} // END abstract class Kea_Controller_Search_Abstract
?>
