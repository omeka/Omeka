<?php

/**
 * Comprehensive site-wide search
 * @todo delete this controller (and associated view)
 * @package Omeka
 **/
class SearchController extends Kea_Controller_Action
{
	public function init() {
		
	}
	
	public function browseAction() {
		$search = new Kea_Controller_Search_MySQL('Item');
		
		if(!empty($_POST['submit'])) {
			$offset = 0;
			$page = 1;
			$per_page = 2;
				
			$search->offset = $offset;
			$search->page = $page;
			$search->per_page = $per_page;
			$search->terms = $_POST['search'];
			
			$results = $search->run();
		
			
		}else {
			$results = array();
		}
		
		$total = $search->getTotal();
		
		$this->render('search.php', compact('results', 'total'));
	}
} // END class SearchController extends Kea_Controller_Action

?>