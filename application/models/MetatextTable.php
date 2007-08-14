<?php 
/**
* 
*/
class MetatextTable extends Doctrine_Table
{
	/**
	 * Given an array of key/value pairs where key = metafield name and value = array consisting of (at minimum) metafield name and text,
	 * Find each metatext entry or create a new one given a metafield name
	 * Return the Doctrine_Collection of elements that can then be saved or deleted, etc.
	 *
	 * @return void
	 **/
	public function collectionFromArray($metatext, $item)
	{
		$mfTable = Doctrine_Manager::getInstance()->getTable('Metafield');
		
		$coll = new Doctrine_Collection('Metatext');

		foreach ($metatext as $row) {
			
			$metafield_name = $row['name'];
			$text = $row['text'];
			
			//Try to find the existing metatext row so we can edit it
			$mtObj = $this->findByItemAndMetafieldName($item, $metafield_name);

			//If we can't find it, make a new one!!!1
			if(!$mtObj) {
				
				//Find the metafield as for doing so!
				$mf = $mfTable->findByName($metafield_name);
				
				//Oops, look what you did!
				if(!$mf) throw new Exception( "Metafield named '$metafield_name' does not exist!" );
			
				//Make a new Metatext obj
				$mtObj = new Metatext;
				
				$mtObj->metafield_id = $mf->id;
				$mtObj->item_id = $item->id;
				
			}
			
			//If the 'text' field is null, we have to use '' instead (stupid hack)
			$mtObj->text = ($text === null) ? $text = '' : $text;
			
			//Add this object to the collection (make sure key = metafield name)
			$coll->add($mtObj, $metafield_name);
		}
		
		return $coll;
	}
	
	public function findByItemAndMetafieldName($item, $metafield)
	{
		if(!$item->exists()) return false;
		
		$dql = "SELECT m.* FROM Metatext m INNER JOIN m.Metafield mf WHERE m.item_id = ? AND mf.name = ? LIMIT 1";
		$q = new Doctrine_Query;
		
		$q->parseQuery($dql);
		
		$res = $q->execute(array($item->id, $metafield));
		
		if($res) return $res->getFirst();
		
		return $res;
	}

	/**
	 * @param Item $item
	 * @param array $metafields
	 * @return array
	 **/
	public function findByMetafields($item, $metafields, $simple=true)
	{
		//Create a giant customized 'where' condition
		$where = array();
		foreach ($metafields as $metafield) {
			$where[] = "mf.name = '{$metafield['name']}'";
		}
		$where = '(' . join(' OR ', $where) . ')';
		
		
		$select = new Kea_Select;
		
		$select->from(array('Metafield', 'mf'), 'mf.name as name, mt.text as text, mf.id as metafield_id, mt.item_id')
			
		//Join the metafields, types_metafields, types
		->joinLeft(array('Metatext', 'mt'), 'mt.metafield_id = mf.id')
		->where($where);
		
		$select->where('(mt.item_id = ?)', $item->id);
		
//		echo $select;
		
		$rows = $select->fetchAll();
		
		$metatext = array();
		foreach ($metafields as $name => $metafield) {
			foreach ($rows as $row) {
				if($row['name'] == $name) {
					$metatext[$name] = $row;
					continue 2;
				}
			}
			
		}		

		//At this point, we should have two arrays with metafield names as keys
		//$metafields and $metatext
		//Now merge that business!

		$result = array_merge($metafields, $metatext);
		
		
		if($simple) return $this->getSimplified($result);
		
		return $result;
		
	}
	
	public function findByType($item, $type, $simple=true) {
		
		if(empty($type)) return array();
		
		//Retrieve the metafields

			$select = new Kea_Select;
		
		$type_id = ($type instanceof Type) ? $type->id : $type;
		
		$select->from(array('Metafield','mf'), 'mf.name as name, mf.id as metafield_id')
			->innerJoin(array('TypesMetafields','tm'), 'tm.metafield_id = mf.id')
			->innerJoin(array('Type','t'), 't.id = tm.type_id')
			->where('t.id = ?', $type_id);

		$res = $select->fetchAll();	
	
		//Now pop the metafields into an array that has the name as the key
		$metafields = array();
		foreach ($res as $row) {
			$metafields[$row['name']] = $row;
		}
		
		//Now pull the data via the metafields
		
		return $this->findByMetafields($item, $metafields, $simple);
	}
	
	
	protected function getSimplified($res)
	{
		foreach ($res as $k => $row) {
			$mt[$row['name']] = $row['text'];
		}
		 return $mt;
	}
	
	public function findByItem($item, array $params = array(), $simplified = false)
	{
		$item_id = $item->exists() ? $item->id : 0;
		
		$select = new Kea_Select;
		$select->from(array('Metafield', 'mf'), 'DISTINCT(mf.name) as name, mt.text as text, mf.id as metafield_id')
			
		//Join the metafields, types_metafields, types
		->joinLeft(array('Metatext', 'mt'), 'mt.metafield_id = mf.id')
		->joinLeft(array('TypesMetafields', 'tm'), 'tm.metafield_id = mf.id')
		->joinLeft(array('Type', 't'), 't.id = tm.type_id')
		->where('(mt.item_id = ? OR mt.item_id IS NULL)', $item_id)
		->group('name');
		
		//If we pass it a plugin, retrieve all metatext for that plugin
		if(isset($params['plugin'])) {
			
			$plugin = $params['plugin'];
			
			//Join the plugin table with each of those for the check
			$select->joinLeft(array('Plugin','mfp'), 'mfp.id = mf.plugin_id')
				->joinLeft(array('Plugin', 'tmp'), 'tmp.id = tm.plugin_id')
				->joinLeft(array('Plugin', 'tp'), 'tp.id = t.plugin_id');
				
			//If 'plugin' is set to TRUE, then pull data for all plugins, otherwise pull data for the specific plugin	
			if($params['plugin'] !== true) {
				$select->where("(mfp.name = '$plugin' OR tmp.name = '$plugin' OR tp.name = '$plugin')");
			}
				
			$select->where("(mfp.active = 1 OR tmp.active = 1 OR tp.active = 1)");
		}
		
		//Otherwise ensure that no plugin metatext is retrieved unless the 'all' parameter is set
		elseif(!isset($params['all'])) {
			$select->where('(mf.plugin_id IS NULL AND tm.plugin_id IS NULL AND t.plugin_id IS NULL)');
		}
		
		//Likewise if we pass it a type, pull all metatext for that
		if(isset($params['type'])) {
			$type = $params['type'];
			
			if(is_numeric($type)) {
				$select->where('t.id = ?', $type);
			}
			
			elseif($type instanceof $type) {
				$select->where('t.id = ?', $type->id);
			}
			
			else {
				$select->where('t.name =  ?', $type);
			}
		}
		
		//Retrieve no type metatext unless the 'all' parameter is set
		elseif(!isset($params['all'])) {			
			$select->where('(t.id IS NULL AND tm.id IS NULL AND mf.plugin_id IS NOT NULL)');
		}

//echo $select;

		$res = $select->fetchAll();
		
		$mt = array();
		
		//Simplified just means key/value pairs
		if($simplified) {
			return $this->getSimplified($res);
		}
		
		
		foreach ($res as $k => $v) {
			$mt[$v['name']] = $v;
		}

		return $mt;
	}
}
 
?>
