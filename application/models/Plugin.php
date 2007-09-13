<?php
require_once 'Metafield.php';
/**
 * Used for plugin storage in the database
 *
 * @package default
 * 
 **/
class Plugin extends Kea_Record
{
	public function setUp() {
		$this->ownsMany("Metafield as Metafields", "Metafield.plugin_id");
		$this->ownsMany("Type as Types", "Type.plugin_id");
	}
	
 	public function setTableDefinition() {
		$this->option('type', 'MYISAM');
		$this->setTableName("plugins");
       	$this->hasColumn('name', 'string', 255, array('notnull' => true, 'unique'=>true, 'notblank'=>true));
        $this->hasColumn('active', 'boolean', null, array('default'=>'0', 'notnull' => true));		
		$this->index('active', array('fields'=>array('active')));
	}
	
	public function commitForm($post, $save=true, $options=array())
	{	
		if(empty($post)) return false;

		$this->config = $post['config'];
		
		if($post['active']) {
			$this->active = (int) !($this->active);
		}
		try{
			$this->save();
			return true;
		}catch( Exception $e) {
			return false;
		}

	}
} // END class Location extends Kea_Record


?>