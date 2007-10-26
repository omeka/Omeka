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
	
	/**
	 * The trail of this function:
	 * 	items_search_form() form helper  --> ItemsController::browseAction()  --> ItemTable::findBy() --> here
	 *
	 * @return void
	 **/
	protected function advancedSearch($select, $advanced)
	{
		$conn = Doctrine_Manager::getInstance()->connection();
		
		$metafields = array();
		
		foreach ($advanced as $k => $v) {
			$field = $v['field'];
			$type = $v['type'];
			$value = $v['terms'];

			//Determine what the SQL clause should look like
			switch ($type) {
				case 'contains':
					$predicate = "LIKE " . $conn->quote('%'.$value .'%');
					break;
				case 'does not contain':
					$predicate = "NOT LIKE " . $conn->quote('%'.$value .'%');
					break;
				case 'is empty':	
					$predicate = "= ''";
					break;
				case 'is not empty':
					$predicate = "!= ''";
					break;
				default:
					throw new Exception( 'Invalid search type given!' );
					break;
			}


			//Strip out the prefix to figure out what table it comin from
			$field_a = explode('_', $field);
			$prefix = array_shift($field_a);
			$field = implode('_', $field_a);
			
			//Process the joins differently depending on what table it needs
			switch ($prefix) {
				case 'item':
					//We don't need any joins because we are already searching the items table
					
					//But we should verify that the field given is a column in the table
					if(!$this->getTypeOf($field)) {
						throw new Exception( 'Invalid field given!' );
					}
					
					//We're good, so start building the WHERE clause
					$where = '(i.' . $field . ' ' . $predicate . ')';
					
					$select->where($where);
					
					break;
				case 'metafield':
					//Ugh, the Metafields query needs to be dealt with separately because just tacking on multiple metafields
					//will not return correct results
					
					
					
					//We need to join on the metafields and metatext tables
					$select->innerJoin(array('Metatext', 'mt'), 'mt.item_id = i.id');
					$select->innerJoin(array('Metafield','m'), 'm.id = mt.metafield_id');
					
					//Start building the where clause
					$where = "(m.name = ". $conn->quote($field) . " AND mt.text $predicate)";
					
					$metafields[] = $where;
					
					break;
				default:
					throw new Exception( 'Search failed!' );
					break;
			}	
			
			//Build the metafields WHERE clause
			//Should look something like the query below
			/*
			mt.id IN 
			(
			SELECT mt.id 
			FROM metatext mt 
			INNER JOIN metafields m ON m.id = mt.metafield_id
			WHERE 
				(m.name = 'Process Edit' AND mt.text != '') 
			OR 
				(m.name = 'Process Review' AND mt.text = '')
			)
				}
			}
			*/
		if(count($metafields)) {
			$subQuery = new Omeka_Select;
			$subQuery->from(array('Metatext','mt'), 'mt.id')
			->innerJoin(array('Metafield','m'), 'm.id = mt.metafield_id')
			->where(join(' OR ', $metafields));
			
			$select->where('mt.id IN ('. $subQuery->__toString().')');			
		}

//	echo $select;exit;
		}
	}
	/**
	 * Can specify a range of valid Item IDs or an individual ID
	 * 
	 * @param Omeka_Select $select
	 * @param string $range Example: 1-4, 75, 89
	 * @return void
	 **/
	protected function filterByRange($select, $range)
	{
		//Comma-separated expressions should be treated individually
		$exprs = explode(',', $range);
		
		//Construct a SQL clause where every entry in this array is linked by 'OR'
		$wheres = array();
		
		foreach ($exprs as $expr) {
			//If it has a '-' in it, it is a range of item IDs.  Otherwise it is a single item ID
			if(strpos($expr, '-') !== false) {
				list($start, $finish) = explode('-', $expr);
				
				//Naughty naughty koolaid, no SQL injection for you
				$start = (int) trim($start);
				$finish = (int) trim($finish);
				
				$wheres[] = "(i.id BETWEEN $start AND $finish)";
			}
			//It is a single item ID
			else {
				$id = (int) trim($expr);
				$wheres[] = "(i.id = $id)";
			}
		}
		
		$where = join(' OR ', $wheres);
		
		$select->where('('.$where.')');
	}
		
	protected function simpleSearch( $select, $terms)
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
				
		$select->innerJoin('temp_search ts', 'ts.item_id = i.id');
		$select->order('ts.id ASC');
	}

	protected function orderSelectByRecent($select)
	{
		if($select instanceof Doctrine_Query) {
			$select->addSelect('ie.time as i.added');
			$select->leftJoin('i.ItemsRelations ie');
			$select->leftJoin('ie.EntityRelationships er');
			$select->addWhere('(er.name = "added" OR er.name IS NULL)');
			$select->addOrderBy('ie.time DESC');
		}else {
			$select->joinLeft('entities_relations ie', 'ie.relation_id = i.id');
			$select->joinLeft('entity_relationships er', 'er.id = ie.relationship_id');
			$select->where('(er.name = "added" OR er.name IS NULL) AND (ie.type = "Item" OR ie.type IS NULL)');
			$select->order('ie.time DESC');
		}
	}
	
	
	/**
	 * Possible options: 'public','user','featured','collection','type','tag','excludeTags', 'search', 'recent', 'range', 'advanced'
	 *
	 * @param array $params Filtered set of parameters from the request
	 * @return Doctrine_Collection(Item)
	 **/
	public function findBy($params=array(), $returnCount=false)
	{
		$select = new Omeka_Select;
		
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
		//Even more duplication of the code above
		elseif(isset($params['entity'])) {
			
			$entity_id = (int) $params['entity'];
			
			$select->innerJoin('entities_relations ie', 'ie.relation_id = i.id');
			$select->innerJoin('entities e', 'e.id = ie.entity_id');
			$select->where('(e.id = ? AND ie.type = "Item")', $entity_id);
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
			$subSelect = new Omeka_Select;
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
			$this->simpleSearch($select, $params['search']);
		}
		
		//Process the advanced search 
		if(isset($params['advanced_search'])) {
			$this->advancedSearch($select, $params['advanced_search']);
		}
		
		$select->limitPage($params['page'], $params['per_page']);

		//Order items by recent
		if(isset($params['recent'])) {
			$this->orderSelectByRecent($select);
		}
		
		if(isset($params['range'])) {
			$this->filterByRange($select, $params['range']);
		}
		
		//Fire a plugin hook to add clauses to the SELECT statement
		fire_plugin_hook('item_browse_sql', $select, $params);

//echo $select;
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
			$where = "i.id = 0";
		}
		
		
		$query->where($where);

		//Order by recent-ness
		if(isset($params['recent'])) {
			$this->orderSelectByRecent($query);
		}
		
//echo $query;exit;
		
		$items = $query->execute();
		
		return $items;
	}
}
 
?>
