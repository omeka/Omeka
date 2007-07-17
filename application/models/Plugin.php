<?php
require_once 'Metafield.php';
require_once 'PluginTable.php';
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
	}
	
 	public function setTableDefinition() {
		$this->option('type', 'MYISAM');
		$this->setTableName("plugins");
       	$this->hasColumn('name', 'string', 255, array('notnull' => true, 'unique'=>true, 'notblank'=>true));
        $this->hasColumn('config', 'array', null);
        $this->hasColumn('active', 'boolean', null, array('default'=>'0', 'notnull' => true));		
		$this->index('active', array('fields'=>array('active')));
	}
	
	public function __get($name) {
		$plugin_name = $this->name;
		if($plugin_name) {
			$path = PLUGIN_DIR.DIRECTORY_SEPARATOR.$plugin_name.DIRECTORY_SEPARATOR.$plugin_name.'.php';
			require_once $path;
			
			$router = Kea_Controller_Front::getInstance()->getRouter();
			$plugin = new $plugin_name($router,$this);
					
			switch ($name) {
				case 'description':
					return $plugin->getMetaInfo('description');
					break;
				case 'author':
					return $plugin->getMetaInfo('author');
				default:
					return parent::__get($name);
					break;
			}			
		}

		
		return parent::__get($name);
	}
	
	public function commitForm($post, $save, $options)
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