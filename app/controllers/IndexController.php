<?php
require_once 'Kea/Controller/Action.php';
class IndexController extends Kea_Controller_Action
{
	public function index()
	{
		echo "Index Index";
		return "foo";
	}
}
?>