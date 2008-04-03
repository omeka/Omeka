<?php 
/**
* 
*/
class MetatextTable extends Omeka_Table
{
	
	/**
	 * Retrieve an array of extended metadata for a given item.  
	 * In the top-level array each key is the name of the metafield.
	 *
	 * @return array
	 **/	
	public function findTypeMetadata($item, $simplified=false)
	{
		$db = $this->getConn();
		
		$type_id = (int) $item->type_id;
		$item_id = (int) $item->id;
			
		$sql = 
		"SELECT mf.name, j.text, mf.id as metafield_id, mf.description, j.metatext_id
		FROM $db->Metafield mf
		INNER JOIN $db->TypesMetafields tm ON tm.metafield_id = mf.id
		LEFT JOIN (
			SELECT mt.text, mt.item_id, mt.metafield_id, mt.id as metatext_id, tm.type_id
			FROM $db->Metatext mt
			INNER JOIN $db->TypesMetafields tm ON mt.metafield_id = tm.metafield_id
			WHERE mt.item_id = ? ) j ON j.metafield_id = mf.id
		WHERE tm.type_id = ?";

		$result = $db->query($sql, array($item_id, $type_id))->fetchAll();
		
		$type_metadata = array();
		
		foreach ($result as $key => $row) {
			if($simplified) {
				$type_metadata[$row['name']] = $row['text'];
			}else {
				$type_metadata[$row['name']] = $row;
			}
		}

		return $type_metadata;
		
	}
	
	/**
	 * Find and return a metatext object.  This varies from other finder methods
	 * b/c it returns an instance of Metatext that may not be persisent yet
	 *
	 * @throws Exception 
	 * @return Metatext
	 **/
	public function findByItemAndMetafield($item_id, $metafield_id)
	{
		$db = get_db();
		$sql = "SELECT mt.* FROM $db->Metatext mt WHERE mt.item_id = ? AND mt.metafield_id = ? LIMIT 1";
		$mt_obj = $this->fetchObjects($sql, array($item_id, $metafield_id), true);
		
		$exists = $db->getTable('Metafield')->checkExists($metafield_id);
		
		if(!$exists) {
			throw new Exception( "Metafield (ID#$metafield_id) does not exist!" );
		}
		
		//If not, make a new one
		if(!$mt_obj) {
			$mt_obj = new Metatext;
			$mt_obj->metafield_id = $metafield_id;
			$mt_obj->item_id = $item_id;	
		}
		
		return $mt_obj;
	}
	
	/**
	 * Find all the metatext, not just for extended type metadata
	 * Uses similar query structure as MetatextTable::findTypeMetadata
	 *
	 * @return void
	 **/
	public function findByItem($item_id)
	{
		$db = get_db();
		
		$sql = "SELECT DISTINCT(mf.name) as name, mt.id, mt.text as text, mt.metafield_id, mf.description, mt.item_id
				FROM $db->Metatext mt 
				INNER JOIN $db->Metafield mf ON mf.id = mt.metafield_id
				WHERE mt.item_id = ?
				GROUP BY name";
		
		$mt_objs = $this->fetchObjects($sql, array($item_id));
		
		$indexed = array();
		
		if($mt_objs) {
			foreach ($mt_objs as $key => $mt) {
				$indexed[$mt->name] = $mt;
			}			
		}
		
		return $indexed;
	}
}
 
?>
