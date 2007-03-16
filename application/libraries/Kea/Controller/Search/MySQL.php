<?php
/**
 * MySQL fulltext search
 *
 * @package Omeka
 * @author CHNM
 **/
class Kea_Controller_Search_MySQL extends Kea_Controller_Search_Abstract
{
	public function getTotal() {
		return Doctrine_Manager::getInstance()->getTable($this->_targetClass)->count();
	}
} // END class Kea_Controller_Search_MySQL extends Kea_Controller_Search_Abstract
?>
