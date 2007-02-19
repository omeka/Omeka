<?php

/**
 * Comprehensive site-wide search
 *
 * @package Omeka
 **/
class SearchController extends Kea_Controller_Action
{
	public function init() {
		
	}
	
	public function browseAction() {
		if(!empty($_POST['submit'])) {
			$offset = 0;
			$page = 1;
			$per_page = 1;
		
			$search = new Kea_Controller_Search();
		
			$search->offset = $offset;
			$search->page = $page;
			$search->per_page = $per_page;
			$search->terms = $_POST['search'];
			$results = $search->run();
		
			
		}else {
			$results = array();
		}
		$this->render('search.php', compact('results'));
	}
} // END class SearchController extends Kea_Controller_Action

?>