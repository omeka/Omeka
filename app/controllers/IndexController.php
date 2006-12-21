<?php
require_once 'Kea/Controller/Action.php';

class User extends Doctrine_Record { 
    public function setTableDefinition() {
        // set 'user' table columns, note that
        // id column is always auto-created
        $this->hasColumn("name","string",30, array("unique"));
        $this->hasColumn("username","string",20);
        $this->hasColumn("password","string",16);
        $this->hasColumn("created","integer",11);
		$this->hasColumn("foo", "integer", 11);
    }

	public function save(Doctrine_Connection $conn = null)
	{
		try{
			parent::save($conn);
		} catch (Doctrine_Validator_Exception $e) {
			print_r($e);
		}
	}
}

class IndexController extends Kea_Controller_Action
{
	protected function _index()
	{
		$q = new Doctrine_RawSql();
		$s = $q->parseQuery("select {user.*} from user limit 13 offset 1");
		echo count($s->execute()). " ";
	}
}
?>