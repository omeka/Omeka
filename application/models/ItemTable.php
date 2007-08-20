<?php 
/**
* ItemTable
*/
class ItemTable extends Doctrine_Table
{
	protected function getCountFromSelect($select)
	{
		//Grab the total number of items in the table(as differentiated from the result count)
		$countQuery = clone $select;
		$countQuery->resetFrom('items i', 'COUNT(DISTINCT(i.id))');
		$total_items = $countQuery->fetchOne();
		if(!$total_items) $total_items = 0;
		return $total_items;
	}
		
	protected function search( $select, $terms)
	{
		$conn = $this->getConnection();
		$conn->execute("CREATE TEMPORARY TABLE temp_search (id BIGINT AUTO_INCREMENT, item_id BIGINT UNIQUE, PRIMARY KEY(id))");
		
		$itemSelect = clone $select;
		
		//Search the items table	
		$itemsClause = "i.title, i.publisher, i.language, i.relation, i.spatial_coverage, i.rights, i.description, i.source, i.subject, i.creator, i.additional_creator, i.contributor, i.rights_holder, i.provenance, i.citation";
		
		$itemSelect->where("MATCH ($itemsClause) AGAINST (? WITH QUERY EXPANSION)", $terms);
				
		//Grab those results, place in the temp table		
		$insert = "INSERT INTO temp_search (item_id) ".$itemSelect->__toString();
		$conn->execute($insert);
		
		
		//Search the metatext table
		$mSelect = clone $select;
		$metatextClause = "m.text";
		$mSelect->innerJoin("metatext m", "m.item_id = i.id");
		$mSelect->where("MATCH ($metatextClause) AGAINST (? WITH QUERY EXPANSION)", $terms);
	//	echo $mSelect;
		
		//Put those results in the temp table
		$insert = "REPLACE INTO temp_search (item_id) ".$mSelect->__toString();
		$conn->execute($insert);
		
	//	Zend::dump( $conn->execute("SELECT * FROM temp_search")->fetchAll() );exit;
		
		$select->innerJoin('temp_search ts', 'ts.item_id = i.id');
		$select->order('ts.id ASC');
	}

	protected function orderSelectByRecent($select)
	{
		if($select instanceof Doctrine_Query) {
			$select->addSelect('ie.time as i.added');
			$select->innerJoin('i.ItemsRelations ie');
			$select->innerJoin('ie.EntityRelationships er');
			$select->addWhere('er.name = "added"');
			$select->addOrderBy('ie.time DESC');
		}else {
			$select->joinLeft('entities_relations ie', 'ie.relation_id = i.id');
			$select->joinLeft(array('EntityRelationships', 'er'), 'er.id = ie.relationship_id');
			$select->where('er.name = "added"');
			$select->order('ie.time DESC');
		}
	}
	
	
	/**
	 * Possible options: 'public','user','featured','collection','type','tag','excludeTags', 'search', 'recent'
	 *
	 * @param array $params Filtered set of parameters from the request
	 * @return Doctrine_Collection(Item)
	 **/
	public function findBy($params=array(), $returnCount=false)
	{
		$select = new Kea_Select;
		
		$select->from('items i','DISTINCT i.id');
		
		//Show only public if we say so
		if(isset($params['public'])) {
			$select->where('i.public = 1');
		}
		//Show both public items and items added or modified by a specific user
		elseif(isset($params['publicAndUser'])) {
		
			$select->joinLeft('entities_relations ie', 'ie.relation_id = i.id');
			$select->joinLeft('entities e', 'e.id = ie.entity_id');
			$select->joinLeft('users u', 'u.entity_id = e.id');
			$select->joinLeft('entity_relationships ier', 'ier.id = ie.relationship_id');
			
			$select->where( '(i.public = 1 OR (u.id = ? AND (ier.name = "added" OR ier.name = "modified") AND ie.type = "Item") )', $params['publicAndUser']->id);
		}			
		//Duplication of some of the code above
		//Show items associated somehow with a specific user
		elseif(isset($params['user'])) {
			
			$user_id = ($params['user'] instanceof User) ? $params['user']->id : $params['user'];
			$select->joinLeft('entities_relations ie', 'ie.relation_id = i.id');
			$select->joinLeft('entities e', 'e.id = ie.entity_id');
			$select->joinLeft('users u', 'u.entity_id = e.id');
			$select->where('(u.id = ? AND ie.type = "Item")', $user_id);			
		
		}
						
		//filter items based on featured (only value of 'true' will return featured items)
		if(isset($params['featured'])) {
			$select->where('i.featured = 1');
		}
		
		//filter based on collection
		if(isset($params['collection'])) {
			$coll = $params['collection'];		
			$select->innerJoin('collections c', 'i.collection_id = c.id');
			
			if($coll instanceof Collection) {
				$select->where('c.id = ?', $coll->id);
			}elseif(is_numeric($coll)) {
				$select->where('c.id = ?', $coll);
			}else {
				$select->where('c.name = ?', $coll);
			}
		}
		
		//filter based on type
		if(isset($params['type'])) {
			$type = $params['type'];
			
			$select->innerJoin('types ty','i.type_id = ty.id');
			if($type instanceof Type) {
				$select->where('ty.id = ?', $type->id);
			}elseif(is_numeric($type)) {
				$select->where('ty.id = ?', $type);
			}else {
				$select->where('ty.name = ?', $type);
			}
		}
		
		//filter based on tags
		if(isset($params['tags'])) {
			$tags = $params['tags'];
			
			$select->innerJoin('taggings tg','tg.relation_id = i.id');
			$select->innerJoin('tags t', 'tg.tag_id = t.id');
			if(!is_array($tags) )
			{
				$tags = explode(',', $tags);
			}
			foreach ($tags as $key => $t) {
				$select->where('t.name = ?', trim($t));
			}	
			$select->where('tg.type= "Item"');		
		}
		
		//exclude Items with given tags
		if(isset($params['excludeTags'])) {
			$excludeTags = $params['excludeTags'];
			if(!is_array($excludeTags))
			{
				$excludeTags = explode(',', $excludeTags);
			}
			$subSelect = new Kea_Select;
			$subSelect->from('items i INNER JOIN taggings tg ON tg.relation_id = i.id 
						INNER JOIN tags t ON tg.tag_id = t.id', 'i.id');
							
			foreach ($excludeTags as $key => $tag) {
				$subSelect->where("t.name LIKE ?", $tag);
			}	
	
			$select->where('tg.type = "Item" AND i.id NOT IN ('.$subSelect->__toString().')');
		}
		
/*
		if(($from_record = $this->_getParam('relatedTo')) && @$from_record->exists()) {
			$componentName = $from_record->getTable()->getComponentName();
			$alias = $this->_table->getAlias($componentName);
			$query->innerJoin("Item.$alias rec");
			$query->addWhere('rec.id = ?', array($from_record->id));
		}
*/

		//Check for a search
		if(isset($params['search'])) {
			$this->search($select, $params['search']);
		}
		
		$select->limitPage($params['page'], $params['per_page']);

		//Order items by recent
		if(isset($params['recent'])) {
			$this->orderSelectByRecent($select);
		}

		//Fire a plugin hook to filter the SELECT statement
		Kea_Controller_Plugin_Broker::getInstance()->filterBrowse($select, "Item");

		//At this point we can return the count instead of the items themselves if that is specified
		if($returnCount) {
			$count = $this->getCountFromSelect($select);
			
			//Drop the search table if it exists
			$this->getConnection()->execute("DROP TABLE IF EXISTS temp_search");
		
			return $count;
		}
		
//echo $select;
		$res = $select->fetchAll();
		
		//Drop the search table if it exists (DUPLICATED)
		$this->getConnection()->execute("DROP TABLE IF EXISTS temp_search");
				
		foreach ($res as $key => $value) {
			$ids[] =  $value['id'];
		}		


		//Finally, hydrate the Doctrine objects with the array of ids given
		$query = new Doctrine_Query;
		$query->select('i.*, t.*')->from('Item i');
		$query->leftJoin('i.Collection c');
		$query->leftJoin('i.Type ty');
				
		//If no IDs were returned in the first query, then whatever
		if(!empty($ids)) {
			$where = "(i.id = ".join(" OR i.id = ", $ids) . ")";
		}else {
			$where = "0";
		}
		
		
		$query->where($where);

		//Order by recent-ness
		if(isset($params['recent'])) {
			$this->orderSelectByRecent($query);
		}
		
//echo $query;
		
		$items = $query->execute();
		
		return $items;
	}
}
 
?>
