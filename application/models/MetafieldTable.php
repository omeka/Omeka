<?php

/**
 * MetafieldTable
 *
 * @package Omeka
 * 
 **/
class MetafieldTable extends Doctrine_Table
{
	/**
	 * Find all the metafields that belong to active plugins, (and optionally) plugins that have a given type
	 *
	 * @return void
	 * 
	 **/
	public function findByTypeAndPlugin($type=null, $plugin=null) {
		$query = new Doctrine_Query();
		$query->from('Metafield m')->innerJoin('m.Plugin p')->where('p.active = 1');
		
		if(!empty($type)) {
			$query->innerJoin('m.Types t');
			if(is_string($type)) {
				$query->addWhere('t.name = ?', $type);
			}else {
				if($type instanceof Type and $type->exists()){
					$query->addWhere('t.id = ?', $type->id);
				}elseif(is_numeric($type)) {
					$query->addWhere('t.id = ?', $type);
				}
			}
		}
		
		if(!empty($plugin)) {
			if(is_string($plugin)) {
				$query->addWhere('p.name = ?', $plugin);
			}elseif($plugin instanceof Plugin) {
				$query->addWhere('p.id = ?', $plugin->id);
			}else {
				$query->addWhere('p.id = ?', $plugin);
			}
		}
				
		return $query->execute();
	}

	public function findByName($name) {
		return $this->findBySql("name = ?", array($name))->getFirst();
	}
	
	public function findTypeMetafields() {
		$query = new Doctrine_Query();
		$query->from('Metafield m')->innerJoin('m.TypesMetafields tm');
		return $query->execute();
	}

	public function findMetafieldsWithoutType($type=null) {
		$query = new Doctrine_Query();
		$query->from('Metafield m');
		
		if($type->id) {
			foreach( $type->TypesMetafields as $key => $tm )
			{
				$query->addWhere('m.id != '.$tm->metafield_id);
			}
		}
		$query->addWhere('m.plugin_id IS NULL');
		return $query->execute();
	}
} // END class MetafieldTable extends Doctrine_Table

?>