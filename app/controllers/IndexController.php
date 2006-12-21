<?php
require_once 'Kea/Controller/Action.php';
require_once 'Kea/Domain/Record.php';
require_once 'app/models/Item.php';
require_once 'app/models/User.php';
require_once 'app/models/Collection.php';
require_once 'app/models/Metafield.php';
require_once 'app/models/Metatext.php';
require_once 'app/models/Tag.php';
require_once 'app/models/Type.php';
require_once 'app/models/File.php';
class IndexController extends Kea_Controller_Action
{
	
	
	protected function _index()
	{
		$conn = Doctrine_Manager::getInstance()->connection();
		$userTable = new Doctrine_Table("user", $conn);
		$itemTable = new Doctrine_Table("item", $conn);
		$collTable = new Doctrine_Table("collection", $conn);
		$metafieldTable = new Doctrine_Table("metafield", $conn);
		$metatextTable = new Doctrine_Table("metatext", $conn);
		$tagTable = new Doctrine_Table("tag", $conn);
		$typeTable = new Doctrine_Table("type", $conn);
		$fileTable = new Doctrine_Table("file", $conn);
/*		
		$q = new Doctrine_RawSql();
		$s = $q->parseQuery("select {user.*} from user limit 13 offset 1");
		echo count($s->execute()). " ";
*/	
		$i = new Item;
		$i->title = "Foo";
		$i->description = "Bar";
		$ret = $i->save();
		var_dump( $ret );exit;
	}
}
?>