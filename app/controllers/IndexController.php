<?php
require_once 'Kea/Controller/Action.php';
class IndexController extends Kea_Controller_Action
{
	public function index()
	{
		$this->foo = array("foo"=>"bar");
		$this->goo = "poo";
	}
}
?>