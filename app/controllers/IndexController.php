<?php
require_once 'Kea/Controller/Action.php';
require_once 'app/models/Item.php';
require_once 'app/models/Collection.php';
class IndexController extends Kea_Controller_Action
{
	public function index()
	{
		$this->foo = array("foo"=>"bar");
		$this->goo = "poo";
		//var_dump( $item->files );exit;
//		$m = Metatext::findById(1);
//		$f = File::findById(2);
		$t = Type::findById(9);
//		$t->insertJoin(9, 31);
		$t->deleteJoin(9, 31);
		var_dump( $t );
	}
}
?>