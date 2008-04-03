<?php
require_once 'Metafield.php' ;
require_once 'TypesMetafields.php' ;
/**
 * @package Omeka
 * 
 **/
class Type extends Omeka_Record { 
    
	public $name;
	public $description = '';
	public $plugin_id;

	protected $_related = array('Metafields'=>'loadMetafields', 'Items'=>'getItems', 'Plugin'=>'getPlugin');

	public function hasMetafield($name) {
		$db = get_db();
		
		$sql = "SELECT COUNT(m.id) FROM $db->Metafield m 
		INNER JOIN $db->TypesMetafields tm ON tm.metafield_id = m.id
		WHERE tm.type_id = ? AND m.name = ?";
		
		$count = (int) $db->fetchOne($sql, array($this->id, $name));
		return ($count > 0);
	}
	
	protected function loadMetafields()
	{
		$db = get_db();
		$sql = "SELECT m.* FROM {$db->Metafield} m 	
				INNER JOIN {$db->TypesMetafields} tm ON tm.metafield_id = m.id 
				WHERE tm.type_id = ? GROUP BY m.id";
		return $this->getTable('Metafield')->fetchObjects($sql, array($this->id));
	}
	
	protected function getPlugin()
	{
		return $this->getTable('Plugin')->find($this->plugin_id);
	}
	
	protected function getItems()
	{
		return $this->getTable('Item')->findBy(array('type'=>$this->id));
	}
	
	/**
	 * Current validation rules for Type
	 * 
	 * 1) 'Name' field can't be blank
	 * 2) 'Name' field must be unique
	 *
	 * @return void
	 **/
	protected function _validate()
	{
		if(empty($this->name)) {
			$this->addError('name', 'Type name must not be blank');
		}
		
		if(!$this->fieldIsUnique('name')) {
			$this->addError('name', 'That name has already been used for a different Type');
		}
	}
	
	/**
	 * Delete all the TypesMetafields joins
	 *
	 * @return void
	 **/
	protected function _delete()
	{
		$tm_objs = get_db()->getTable('TypesMetafields')->findBySql('type_id = ?', array( (int) $this->id));
		
		foreach ($tm_objs as $tm) {
			$tm->delete();
		}
	}
	
	/**
	 * Find a specific TypesMetafields join object and delete it (severing the connection between the two)
	 *
	 * @return void
	 **/
	protected function removeMetafield(Metafield $metafield)
	{
		//Find the join and delete it
		$db = get_db();
		$sql = "SELECT tm.* FROM $db->TypesMetafields tm WHERE tm.type_id = ? AND tm.metafield_id = ? LIMIT 1";
		$tm = $this->getTable('TypesMetafields')->fetchObjects($sql, array($this->id, $metafield->id), true);
		$tm->delete();
	}
	
	/**
	 * Add a Metafield to this Type by creating a new join in the TypesMetafields table
	 *
	 * @param Metafield $metafield
	 * @return void
	 **/
	public function addMetafield(Metafield $metafield)
	{
		//save the metafield if its a new one
		if(!$metafield->exists()) {
			$metafield->save();
		}
		
		//Add a join row in the TypesMetafields table
		$tm = new TypesMetafields;
		
		$tm->metafield_id = $metafield->id;
		$tm->type_id = $this->id;
		$tm->save();			
	}
	
	/**
	 * Post commit hook that will add metafields to a type
	 * This occurs post-commit because that ensures that the Type has a valid ID
	 *
	 * @return void
	 **/
	protected function afterSaveForm($post)
	{

		//Add new metafields
		foreach ($post['NewMetafields'] as $key => $mf_array) {
			
			$mf_name = $mf_array['name'];
			
			if(!empty($mf_name)) {
				$mf = get_db()->getTable('Metafield')->findByName($mf_name);
				if(!$mf) $mf = new Metafield;

				if(!$this->hasMetafield($mf_name)) {
					$mf->setArray($mf_array);
					$this->addMetafield($mf);				
				}				
			}

		}

		//Add new joins for pre-existing metafields
		if(!empty($post['ExistingMetafields'])) {
			foreach ($post['ExistingMetafields'] as $key => $tm_array) {			
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
	
	protected function beforeSaveForm(&$clean)
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