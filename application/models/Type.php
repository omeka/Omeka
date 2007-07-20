<?php
require_once 'Metafield.php' ;
require_once 'TypesMetafields.php' ;
/**
 * @package Omeka
 * 
 **/
class Type extends Kea_Record { 
    protected $error_messages = array(	'name' => array('notblank' => 'Type name must not be blank.'));

	public function setUp() {
		//This should be 'ownsMany' to set up the foreign key cascade delete, but it won't work with many-to-many aggregates (Doctrine_Exception)
		$this->hasMany("Metafield as Metafields", "TypesMetafields.metafield_id");
		$this->ownsMany("TypesMetafields", "TypesMetafields.type_id");
		$this->hasMany("Item as Items", "Item.type_id");
		$this->hasOne("Plugin", "Type.plugin_id");
	}

	public function setTableDefinition() {
		$this->option('type', 'MYISAM');
   		$this->setTableName('types');
		$this->hasColumn('name', 'string', 255, array('notnull' => true, 'unique'=>true, 'notblank'=>true));
        $this->hasColumn('description', 'string', null, array('notnull' => true, 'default'=>''));
		$this->hasColumn('plugin_id', 'integer');
 	}

	public function hasMetafield($name) {
		foreach( $this->Metafields as $metafield )
		{
			if($metafield->name == $name) return true;
		}
		return false;
	}
	
	protected function preCommitForm(&$post, $options)
	{
		//Remove empty metafield submissions
		foreach( $this->TypesMetafields as $key => $tm )
		{
			if(empty($tm->metafield_id)) {
				$this->TypesMetafields->remove($key);
			}
		}
		
		//duplication (delete/remove existing metafields)
		foreach( $this->Metafields as $key => $metafield )
		{
			if($_POST['delete_metafield'][$key] == 'on') {
				$metafield->delete();
			}
			
			if(empty($metafield->name) || $_POST['remove_metafield'][$key] == 'on') {
				$this->Metafields->remove($key);
			}
		}
		
		//Remove empty Metafields form entries
		foreach ($post['Metafields'] as $k => $mf) {
			if(empty($tm['name'])) {
				unset($post['Metafields'][$k]);
			}
		}
		
		//Remove all the empty TypesMetafields entries on the form
		foreach ($post['TypesMetafields'] as $k => $tm) {
			if(empty($tm['metafield_id'])) {
				unset($post['TypesMetafields'][$k]);
			}
		}
		
//		Zend::dump( $post );exit;	
	}
}

?>