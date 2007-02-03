<?php
require_once 'Kea/Controller/Action.php';
class ItemsController extends Kea_Controller_Action
{		
	protected function _index()
	{
		echo 'index';
	}
	
	/**
	 * Chooses a list of items to display based on $_request data
	 *
	 * Handles both search and display of items within the browse page.
	 *
	 * @param bool $short_desc Show a short description of each item 
	 * @param int $num_items The number of items per page
	 * @param bool $check_location Include items with valid location coordinates
	 * @return array Contains the following keys: 'total' => total # of items found, 'page' => current page, 'per_page' => # per page, 'items' => Item_Collection containing the items found
	 * @author Nate Agrin
	 **/
	protected function _browse()
	{	
		echo 'browse';
	}
}
?>