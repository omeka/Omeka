<?php 
/**
* ItemTable
*/
class ItemTable extends Omeka_Table
{	
	/**
	 * The trail of this function:
	 * 	items_search_form() form helper  --> ItemsController::browseAction()  --> ItemTable::findBy() --> here
	 *
	 * @return void
	 **/
	protected function advancedSearch($select, $advanced)
	{
		$db = get_db();
		
		$metafields = array();
		
		foreach ($advanced as $k => $v) {
			$field = $v['field'];
			$type = $v['type'];
			$value = $v['terms'];

			//Determine what the SQL clause should look like
			switch ($type) {
				case 'contains':
					$predicate = "LIKE " . $db->quote('%'.$value .'%');
					break;
				case 'does not contain':
					$predicate = "NOT LIKE " . $db->quote('%'.$value .'%');
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
					
					if(!$this->hasColumn($field)) {
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
					$select->innerJoin("$db->Metatext mt", 'mt.item_id = i.id');
					$select->innerJoin("$db->Metafield m", 'm.id = mt.metafield_id');
					
					//Start building the where clause
					$where = "(m.name = ". $db->quote($field) . " AND mt.text $predicate)";
					
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
			$subQuery->from("$db->Metatext mt", 'mt.id')
			->innerJoin("$db->Metafield m", 'm.id = mt.metafield_id')
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
	
	/**
	 * Search through the items and metatext table via fulltext, store results in a temporary table
	 * Then search the tags table for atomized search terms (split via whitespace) and store results in the temp table
	 * then join the main query to that temp table and order it by relevance values retrieved from the search
	 *
	 * @return void
	 **/	
	protected function simpleSearch( $select, $terms)
	{
		$db = get_db();
		
		//Create a temporary search table (won't last beyond the current request)
		$tempTable = "{$db->prefix}temp_search";
		$db->exec("CREATE TEMPORARY TABLE IF NOT EXISTS $tempTable (item_id BIGINT UNIQUE, rank FLOAT(10), PRIMARY KEY(item_id))");

		//Search the metatext table
		$mSelect = new Omeka_Select;
		$mSearchClause = "MATCH (m.text) AGAINST (".$db->quote($terms).")";
		
		$mSelect->from("$db->Metatext m", "m.item_id, $mSearchClause as rank");
		
		$mSelect->where($mSearchClause);
	//	echo $mSelect;
		
		//Put those results in the temp table
		$insert = "REPLACE INTO $tempTable (item_id, rank) ".$mSelect->__toString();
		$db->exec($insert);
		
		//Search the items table
		$iSearchClause = 
			"MATCH (
				i.title, 
				i.publisher, 
				i.language, 
				i.relation, 
				i.spatial_coverage, 
				i.rights, 
				i.description, 
				i.source, 
				i.subject, 
				i.creator, 
				i.additional_creator, 
				i.contributor, 
				i.format,
				i.rights_holder, 
				i.provenance, 
				i.citation) 
			AGAINST (".$db->quote($terms).")";
		
		$itemSelect = new Omeka_Select;
		$itemSelect->from("$db->Item i", "i.id as item_id, $iSearchClause as rank");
					
		$itemSelect->where($iSearchClause);

		//Grab those results, place in the temp table		
		$insert = "REPLACE INTO $tempTable (item_id, rank) ".$itemSelect->__toString();

		$db->exec($insert);		

		//Start pulling in search data for the tags
	
		$tagRelevanceRanking = 1;
		$tagSearchList = preg_split('/\s+/', $terms);
		//Also make sure the tag list contains the whole search string, just in case that is found
		$tagSearchList[] = $terms;
		
		$tagSelect = new Omeka_Select;
		$tagSelect->from("$db->Tag t", "i.id as item_id, $tagRelevanceRanking as rank");
		$tagSelect->innerJoin("$db->Taggings tg", "tg.tag_id = t.id");
		$tagSelect->innerJoin("$db->Item i", "(i.id = tg.relation_id AND tg.type = 'Item')");
		
		foreach ($tagSearchList as $tag) {
			$tagSelect->orWhere("t.name LIKE ?", $tag);
		}
		$db->exec("REPLACE INTO $tempTable (item_id, rank) " . $tagSelect->__toString());
		
		//Now add a join to the main SELECT SQL statement and sort the results by relevance ranking		
		$select->innerJoin("$tempTable ts", 'ts.item_id = i.id');
		$select->order('ts.rank DESC');
	}

	protected function orderSelectByRecent($select)
	{
		$select->order('i.id DESC');
	}
	
	
	/**
	 * Possible options: 'public','user','featured','collection','type','tag','excludeTags', 'search', 'recent', 'range', 'advanced'
	 *
	 * @param array $params Filtered set of parameters from the request
	 * @return Doctrine_Collection(Item)
	 **/
	public function findBy($params=array(), $returnCount=false)
	{
		$select = $this->getItemSelectSQL( ($returnCount ? 'count' : 'full') );
		
		$db = get_db();
		
		//Show items associated somehow with a specific user or entity
		if(isset($params['user']) or isset($params['entity'])) {

			$select->joinLeft("$db->EntitiesRelations ie", 'ie.relation_id = i.id');
			$select->joinLeft("$db->Entity e", 'e.id = ie.entity_id');
			
			if($entity_id = (int) $params['entity']) {
				
				$select->where('(e.id = ? AND ie.type = "Item")', $entity_id);
			}elseif($user_id = (int) $params['user']) {
								
				$select->joinLeft("$db->User u", 'u.entity_id = e.id');
				$select->where('(u.id = ? AND ie.type = "Item")', $user_id);
			}						
		}
		
		//Force a preview of the public items
		if(isset($params['public'])) {
			$select->where('i.public = 1');
		}
						
		//filter items based on featured (only value of 'true' will return featured items)
		if(isset($params['featured'])) {
			$select->where('i.featured = 1');
		}
		
		//filter based on collection
		if(isset($params['collection'])) {
			$coll = $params['collection'];		
			$select->innerJoin("$db->Collection c", 'i.collection_id = c.id');
			
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
			
			$select->innerJoin("$db->Type ty",'i.type_id = ty.id');
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
			
			$select->innerJoin("$db->Taggings tg",'tg.relation_id = i.id');
			$select->innerJoin("$db->Tag t", 'tg.tag_id = t.id');
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
			$subSelect->from("$db->Item i INNER JOIN $db->Taggings tg ON tg.relation_id = i.id 
						INNER JOIN $db->Tag t ON tg.tag_id = t.id", 'i.id');
							
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

		if(isset($params['range'])) {
			$this->filterByRange($select, $params['range']);
		}
	
	
		//Fire a plugin hook to add clauses to the SELECT statement
		fire_plugin_hook('item_browse_sql', $select, $params);

//echo $select;exit;

		//At this point we can return the count instead of the items themselves if that is specified
		if($returnCount) {
			
//echo $select;exit;
			$count = (int) $db->fetchOne($select);
			
			if(isset($params['search'])) {
				$this->clearSearch();
			}
		
			return $count;
		}else {
			
			//If we returning the data itself, we need to group by the item ID
			$select->group("i.id");
		}

		//Order items by recent
		//@since 11/7/07  ORDER BY must not be in the COUNT() query b/c it slows down
		if(isset($params['recent'])) {
			$this->orderSelectByRecent($select);
		}

		$items = $this->fetchObjects($select);
		
		
		if(isset($params['search'])) {
			$this->clearSearch();
		}

		return $items;
	}

	/**
	 * Remove the temporary search table
	 *
	 * @return void
	 **/
	private function clearSearch()
	{
		$db = get_db();
		$db->exec("DROP TABLE IF EXISTS {$db->prefix}temp_search");
	}
	
	/**
	 * This is a kind of simple factory that spits out proper beginnings 
	 * of SQL statements when retrieving items
	 *
	 * @param $type string full|simple|count|id
	 * @return Omeka_Select
	 **/
	private function getItemSelectSQL($type='full')
	{
		//@duplication self::findBy()
		$select = new Omeka_Select;
		
		$db = get_db();
		
		switch ($type) {
			case 'count':
				$select->from("$db->Item i", 'COUNT(DISTINCT(i.id))');
				break;
			case 'full':
			
				$select->from("$db->Item i",'i.*, added.time as added, modded.time as modified');
			
				//Join on the entities_relations table so we can pull timestamps
				$select->joinLeft("$db->EntitiesRelations modded", 'modded.relation_id = i.id');
				$select->joinLeft("$db->EntityRelationships mod_r", 'mod_r.id = modded.relationship_id');
	
				$select->joinLeft("$db->EntitiesRelations added", 'added.relation_id = i.id');
				$select->joinLeft("$db->EntityRelationships add_r", 'add_r.id = added.relationship_id');
	
				//This rather complicated mess ensures that no items are left out of the list as a result of DB inconsistencies
				//i.e. an item lacks an entry in the entities_relations table for some reason
				$select->where('( (added.type = "Item" AND add_r.name = "added" OR added.time IS NULL) 
								OR (modded.type = "Item" AND mod_r.name = "modified" OR modded.time IS NULL) )');
				break;
			
			//'Simple' SQL statement just returns id, title
			case 'simple':	
				$select->from("$db->Item i", 'i.id, i.title');
				break;
			default:
				# code...
				break;
		}
		
		new ItemPermissions($select);
		
		return $select;
	}
	
	/**
	 * Override the built-in count() method to filter based on permissions
	 *
	 * @return void
	 **/
	public function count()
	{
		$sql = $this->getItemSelectSQL('count');
		return get_db()->fetchOne($sql);
	}
	
	public function find($id)
	{
		$select = $this->getItemSelectSQL();
		
		$select->where("i.id = ?", $id);
		$select->limit(1);
		
		return $this->fetchObjects($select, array(), true);
	}
	
	public function findPrevious($item)
	{
		return $this->findNearby($item, 'previous');
	}
	
	public function findNext($item)
	{
		return $this->findNearby($item, 'next');
	}
	
	protected function findNearby($item, $position = 'next')
	{
		//This will only pull the title and id for the item
		$select = $this->getItemSelectSQL('simple');
		
		$select->limit(1);
		
		switch ($position) {
			case 'next':
				$select->where('i.id > ?', (int) $item->id);
				$select->order('i.id ASC');
				break;
			
			case 'previous':
				$select->where('i.id < ?', (int) $item->id);
				$select->order('i.id DESC');
				break;
				
			default:
				throw new Exception( 'Invalid position provided to ItemTable::findNearby()!' );
				break;
		}

		return $this->fetchObjects($select, array(), true);
	}
	
	
	
	public function findRandomFeatured($withImage=true)
	{		
		$select = $this->getItemSelectSQL();
		
		$db = get_db();
		
		$select->addFrom('RAND() as rand');
		
		$select->innerJoin("$db->File f", 'f.item_id = i.id');
		$select->where('i.featured = 1');
				
		$select->order('rand DESC');
		$select->limit(1);
		
		if($withImage) {
			$select->where('f.has_derivative_image = 1');
		}
				
//		echo $select;exit;		
				
		$item = $this->fetchObjects($select, array(), true);
	
		return $item;
	}
}
 
?>
