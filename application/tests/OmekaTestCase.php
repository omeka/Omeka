<?php 
/**
 * undocumented class
 *
 * @package Omeka
 * @author CHNM
 **/
abstract class OmekaTestCase extends UnitTestCase
{
	private $init = false;
	
	public function setUp() {
        if( ! $this->init) {
			$this->manager = Doctrine_Manager::getInstance();
			$this->wipeDb();
			$sql = file_get_contents('setup.sql');
			$this->manager->connection()->execute($sql);
			$this->init();
		}
        $this->init    = true;
    }
	public function wipeDb() {
		$conn = $this->manager->connection();
		$conn->setAttribute(Doctrine::ATTR_QUOTE_IDENTIFIER, true);
		$models  = array('Collection',
			//		'Exhibit',
					'File',
					'Group',
					'GroupsPermissions',
					'Item',
			//		'ItemsExhibits',
					'ItemsFavorites',
					'ItemsFulltext',
					'ItemsTags',
					'Metafield',
					'Metatext',
					'Option',
					'Permission',
					'Plugin',
					'Route',
					'Tag',
					'Type',
					'TypesMetafields',
					'User',
					'UsersActivations');
		
		foreach ($models as $model) {
			require_once MODEL_DIR.DIRECTORY_SEPARATOR.$model.'.php';
			$tableName = $this->manager->getTable($model)->getTableName();
			$sql = "DROP TABLE IF EXISTS `$tableName`;";
			$conn->execute($sql);
			$this->manager->getTable($model)->export();
		}		
	}
	public function init() {}
	
	public function getTable($name) { return $this->manager->getTable($name);}
	
} // END abstract class OmekaTestCase extends UnitTestCase 
?>
