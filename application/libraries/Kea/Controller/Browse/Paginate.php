<?php
require_once 'Kea/Controller/Action.php';
require_once 'Kea/Controller/Browse/Interface.php';
/**
 * In charge of paginated browsing for the controllers
 *
 * @package Omeka
 **/
class Kea_Controller_Browse_Paginate extends Kea_Controller_Browse_Abstract
{	
	protected $_options = array('num_links' => 5, 'limit' => 10);
		
	public function browse()
	{
		$pluralVar = $this->getOption('pluralized');
		if(empty($pluralVar)) $pluralVar = $this->formatPluralized();
		
		//per_page is either a $_POST var, db option, passed via constructor or default to 10 (in that order)
		
		$per_page = $this->getOption('limit');
				
		//page 
		$page = $this->getOption('page');
		if(!$page) $page = 1;
		
		$offset = ($page - 1) * $per_page;
		
		$query = $this->getQuery();

		
		Kea_Controller_Plugin_Broker::getInstance()->filterBrowse($this);

		$query = $this->buildQuery();

		$countQuery = clone $query;
		$total_results = $countQuery->count();
	 	
		$totalVar = "total_$pluralVar"; //i.e. $total_items
		$$totalVar = $this->_table->count();
		
		settype($per_page, 'int');
		$query->limit($per_page);
		
		settype($offset, 'int');
		$query->offset($offset);

//		echo $query;
		$$pluralVar = $query->execute();
		
		//Figure out the pagination 
		
		//num_links defaults to 5, we can make this dynamic as well
		$num_links = $this->getOption('num_links');
		
		//Figure out the URL for the pagination
		$req = $this->getRequest();
		
		$url = $this->getOption('paginationUrl');
		
		//If the pagination URL isn't passed as a param, then use the default controller's browse page
		if(!$url) {
			$url = $req->getBaseUrl().DIRECTORY_SEPARATOR.$pluralVar.DIRECTORY_SEPARATOR.'browse'.DIRECTORY_SEPARATOR;
		}		
		
		//Serve up the pagination
		require_once 'Kea/View/Functions.php';
		$pagination = pagination($page, $per_page, $total_results, $num_links, $url);

		return $this->_controller->render($pluralVar."/browse.php", compact("total_results", $totalVar, "offset", $pluralVar, "per_page", "page", "pagination"));
	}
	
} // END class Kea_Controller_Browse_Paginate

?>