<?php 
/**
* Exhibit Table
*/
class ExhibitTable extends Omeka_Table
{
	public function findBySlug($slug)
	{
		$db = get_db();
		$select = new Omeka_Select;
		$select->from("{$db->Exhibit} e", 'e.*');
		$select->where("e.slug = ?");
		$select->limit(1);
		
		new ExhibitPermissions($select);
		
		return $this->fetchObjects($select, array($slug), true);		
	}
	
	/**
	 * Override Omeka_Table::count() to retrieve a permissions-limited
	 *
	 * @return void
	 **/
	public function count()
	{
		$db = get_db();
		$select = new Omeka_Select;
		
		$select->from("$db->Exhibit e", "COUNT(DISTINCT(e.id))");
		
		new ExhibitPermissions($select);
		
		return $db->fetchOne($select);
	}
	
	public function find($id)
	{
		$db = get_db();
		
		$select = new Omeka_Select;
		
		$select->from("$db->Exhibit e", "e.*");
		$select->where("e.id = ?");
		
		new ExhibitPermissions($select);
		
		return $this->fetchObjects($select, array($id), true);
	}
	
	public function exhibitHasItem($exhibit_id, $item_id)
	{
		$db = get_db();
		
		$sql = "SELECT COUNT(i.id) FROM $db->Item i 
				INNER JOIN $db->ExhibitPageEntry ip ON ip.item_id = i.id 
				INNER JOIN $db->ExhibitPage sp ON sp.id = ip.page_id
				INNER JOIN $db->ExhibitSection s ON s.id = sp.section_id
				INNER JOIN $db->Exhibit e ON e.id = s.exhibit_id
				WHERE e.id = ? AND i.id = ?";
				
		$count = (int) $db->fetchOne($sql, array((int) $exhibit_id, (int) $item_id));
		
		return ($count > 0);
	}
	
	public function findBy($params=array())
	{
		$db = get_db();
		
		$select = new Omeka_Select;
		
		$select->from("{$db->Exhibit} e", 'e.*');

		if(isset($params['tags'])) {
			$tags = explode(',', $params['tags']);
			$select->innerJoin("{$db->Taggings} tg", 'tg.relation_id = e.id');
			$select->innerJoin("{$db->Tag} t", "t.id = tg.tag_id");
			foreach ($tags as $k => $tag) {
				$select->where('t.name = ?', trim($tag));
			}
			
			//Ah, inheritance
			$select->where("tg.type = 'Exhibit'");
		}
		
		new ExhibitPermissions($select);
		
		$exhibits = $this->fetchObjects($select);
		
		return $exhibits;
	}
}
 
?>
