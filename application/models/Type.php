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
	
	public function loadMetafields()
	{
		$dql = "SELECT m.* FROM Metafield m INNER JOIN m.TypesMetafields tm WHERE tm.type_id = ?";
		$this->Metafields = $this->executeDql($dql, array($this->id));
	}
	
	protected function removeMetafield($metafield)
	{
		//Find the join and delete it
		$dql = "SELECT tm.* FROM TypesMetafields tm WHERE tm.type_id = ? AND tm.metafield_id = ? LIMIT 1";
		$tm = $this->executeDql($dql, array($this->id, $metafield->id), true);
		$tm->delete();
	}
	
	protected function addMetafield($metafield)
	{
		try {
			//save the metafield if its a new one
			if(!$metafield->exists()) {
				$metafield->save();
			}
			
			//Add a join row in the TypesMetafields table
			$tm = new TypesMetafields;
			
			$tm->metafield_id = $metafield->id;
			$tm->type_id = $this->id;
			$tm->save();
			
			return true;
			
		} catch (Exception $e) {
			//Errors indicate that we can't do what we're trying to
			return false;
		}
	}
	
	/**
	 * Post commit hook that will add metafields to a type
	 * This occurs post-commit because that ensures that the Type has a valid ID
	 *
	 * @return void
	 **/
	protected function postCommitForm($post, $options)
	{

		//Add new metafields
		foreach ($post['Metafields']['add'] as $key => $mf_array) {
			
			$mf_name = $mf_array['name'];
			
			if(!empty($mf_name)) {
				$mf = Doctrine_Manager::getInstance()->getTable('Metafield')->findByName($mf_name);
				if(!$mf) $mf = new Metafield;
			
				if(!$this->hasMetafield($mf_name)) {
					$mf->setArray($mf_array);
					$this->addMetafield($mf);				
				}				
			}

		}

		//Add new joins for pre-existing metafields
		if(!empty($post['TypesMetafields']['add'])) {
			foreach ($post['TypesMetafields']['add'] as $key => $tm_array) {			
				$tm = new TypesMetafields;
				$tm->metafield_id = $tm_array['metafield_id'];
				$tm->type_id = $this->id;
			
				//Save & suppress duplicate key errors
				try {
					$tm->save();
				} catch (Exception $e) {}
			}			
		}

		$this->loadMetafields();
	}
	
	protected function preCommitForm(&$clean, $options)
	{

		//duplication (delete/remove existing metafields)
		foreach( $this->Metafields as $key => $metafield )
		{
			if($clean['delete_metafield'][$key] == 'on') {
				$metafield->delete();
			}

			if(empty($metafield->name) || $clean['remove_metafield'][$key] == 'on') {				
				$this->removeMetafield($metafield);
//				$this->Metafields->remove($key);
			}
		}
		
		//Make sure that the form doesn't directly set the Metafields and TypesMetafields
		unset($clean['Metafields']);
		unset($clean['TypesMetafields']);
	}
}

?>