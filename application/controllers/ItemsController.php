<?php

require_once MODEL_DIR.DIRECTORY_SEPARATOR.'Item.php';
/**
 * @package Omeka
 **/
require_once 'Kea/Controller/Action.php';
class ItemsController extends Kea_Controller_Action
{		
	public function init() 
	{
		$this->_table = $this->getTable('Item');
		$this->_modelClass = 'Item';
	}
	
	/**
	 * This wraps the builtin method with permissions checks
	 *
	 **/
	public function editAction()
	{
		if($user = Kea::loggedIn()) {
			
			$item = $this->findById();
		
			//If the user cannot edit any given item
			if($this->isAllowed('editAll') or 
				//Check if they can edit this specific item
				($this->isAllowed('editSelf') and $item->wasAddedBy($user))) {
				
				return parent::editAction();	
			}
		}

		$this->_forward('index','forbidden');
	}
	
	/**
	 * Wrapping this crap with permissions checks
	 *
	 **/
	public function deleteAction()
	{
		if($user = Kea::loggedIn()) {
			$item = $this->findById();
			
			//Permission check
			if($this->isAllowed('deleteAll') or ( $this->isAllowed('deleteSelf') and $item->wasAddedBy($user) )) {
				$item->delete();
				
				$this->_redirect('delete', array('controller'=>'items'));
			}
		}
		$this->_forward('index', 'forbidden');
	}

	public function tagsAction()
	{
		$this->_forward('Tags', 'browse', array('tagType' => 'Item', 'renderPage'=>'items/tags.php'));
	}
	
	protected function search( $select, $terms)
	{
		$conn = $this->getConn();
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
		$mSelect->joinInner("metatext m", "m.item_id = i.id");
		$mSelect->where("MATCH ($metatextClause) AGAINST (? WITH QUERY EXPANSION)", $terms);
	//	echo $mSelect;
		
		//Put those results in the temp table
		$insert = "REPLACE INTO temp_search (item_id) ".$mSelect;
		$conn->execute($insert);
		
	//	Zend::dump( $conn->execute("SELECT * FROM temp_search")->fetchAll() );exit;
		
		$select->joinInner('temp_search ts', 'ts.item_id = i.id');
		$select->order('ts.id ASC');
	}
	
	protected function filterSelectByUser($select, $user_id)
	{
		$select->joinLeft('entities_relations ie', 'ie.relation_id = i.id');
		$select->joinLeft('entities e', 'e.id = ie.entity_id');
		$select->joinLeft('users u', 'u.entity_id = e.id');
		$select->where('(u.id = ? AND ie.inheritance_id = 1)', $user_id);
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
			
	//		$select->order('i.added DESC');
		}
	}
	
	protected function getCountFromSelect($select)
	{
		//Grab the total number of items in the table(as differentiated from the result count)
		//Make sure that the query that retrieves the total number of Items also contains the permissions check
		$countQuery = clone $select;
		$countQuery->resetFrom('items i', 'COUNT(DISTINCT(i.id))');
		$total_items = $countQuery->fetchOne();
		if(!$total_items) $total_items = 0;
		return $total_items;
	}
	
	/**
	 * New Strategy: this will run a SQL query that selects the IDs, then use that to hydrate the Doctrine objects.
	 * Stupid Doctrine.  Maybe their new version will be better.
	 *
	 * @return mixed|void
	 **/
	public function browseAction()
	{			
		require_once 'Kea/Select.php';
		$select = new Kea_Select($this->getConn());
	
		$select->from('items i','DISTINCT i.id');

		//Show only public if we say so
		if($this->_getParam('public')) {
			$select->where('i.public = 1');
		}
		//Don't do any more filtering if user is allowed to see items that aren't public
		elseif( $this->isAllowed('showNotPublic')) {}
		
		//Otherwise check if specific user can see their own items
		elseif($this->isAllowed('showSelfNotPublic')) {
			$user = Kea::loggedIn();
			
			$select->joinLeft('entities_relations ie', 'ie.relation_id = i.id');
			$select->joinLeft('entities e', 'e.id = ie.entity_id');
			$select->joinLeft('users u', 'u.entity_id = e.id');
			$select->joinLeft('entity_relationships ier', 'ier.id = ie.relationship_id');
			
			$select->where( '(i.public = 1 OR (u.id = ? AND (ier.name = "added" OR ier.name = "modified") AND ie.inheritance_id = 1) )', $user->id);
		}
		
		//Otherwise display only public items by default
		else {
			$select->where('i.public = 1');
		} 
		
		try {
			
			//User-specific item browsing
			if($userToView = $this->_getParam('user')) {
							
				//Must be logged in to view items specific to certain users
				if(!$this->isAllowed('browse', 'Users')) {
					throw new Exception( 'May not browse by specific users.' );
				}
				
				if(is_numeric($userToView)) {
					$this->filterSelectByUser($select, $userToView);
				}
			}
		} catch (Exception $e) {
			$this->flash($e->getMessage());
		}
		
		$total_items = $this->getCountFromSelect($select);
		
		Zend::register('total_items', $total_items);
		
		//filter items based on featured (only value of 'true' will return featured items)
		if($featured = $this->_getParam('featured')) {
			$select->where('i.featured = '.($featured == 'true' ? '1':'0'));
		}
		
		//filter based on collection
		if($collection = $this->_getParam('collection')) {
			
			$select->joinInner('collections c', 'i.collection_id = c.id');
			
			if(is_numeric($collection)) {
				$select->where('c.id = ?', $collection);
			}else {
				$select->where('c.name = ?', $collection);
			}
		}
		
		//filter based on type
		if($type = $this->_getParam('type')) {
			
			$select->joinInner('types ty','i.type_id = ty.id');
			if(is_numeric($type)) {
				$select->where('ty.id = ?', $type);
			}else {
				$select->where('ty.name = ?', $type);
			}
		}
		
		//filter based on tags
		if( ($tag = $this->_getParam('tag')) || ($tag = $this->_getParam('tags')) ) {
			
			$select->joinInner('items_tags it','it.item_id = i.id');
			$select->joinInner('tags t', 'it.tag_id = t.id');
			if(!is_array($tag) )
			{
				$tag = explode(',', $tag);
			}
			foreach ($tag as $key => $t) {
				$select->where('t.name = ?', trim($t));
			}			
		}
		
		//exclude Items with given tags
		if(($excludeTags = $this->_getParam('withoutTags'))) {
				if(!is_array($excludeTags))
				{
					$excludeTags = explode(',', $excludeTags);
				}
				$subSelect = new Kea_Select($this->getConn());
				$subSelect->from('items i INNER JOIN items_tags it ON it.item_id = i.id 
							INNER JOIN tags t ON it.tag_id = t.id', 'i.id');
								
				foreach ($excludeTags as $key => $tag) {
					$subSelect->where("t.name LIKE ?", $tag);
				}	
		
				$select->where('i.id NOT IN ('.$subSelect->__toString().')');
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
		if($search = $this->_getParam('search')) {
			$this->search($select, $search);
		}
		
		//Before the pagination, please grab the number of results that this full query will return
		$total_results = $this->getCountFromSelect($select);

		/** 
		 * Now process the pagination
		 * 
		 **/
		$paginationUrl = $this->getRequest()->getBaseUrl().'/items/browse/';
		$options = array(	'num_links'=>	5, 
							'per_page'=>	10,
							'page'		=> 	1,
							'pagination_url' => $paginationUrl);
							
		//check to see if these options were changed by request vars
		$reqOptions = $this->_getAllParams();
		
		$options = array_merge($options, $reqOptions);
		
		$select->limitPage($options['page'], $options['per_page']);

		//Order by recent-ness
		if($recent = $this->_getParam('recent')) {
			$this->orderSelectByRecent($select);
		}
		
//echo $select;exit;
		$res = $select->fetchAll();
		
		//Drop the search table if it exists
		$this->getConn()->execute("DROP TABLE IF EXISTS temp_search");
				
		foreach ($res as $key => $value) {
			$ids[] =  $value['id'];
		}		
		
		//Serve up the pagination
		require_once 'Kea/View/Functions.php';
		$pagination = pagination($options['page'], $options['per_page'], $total_results, $options['num_links'], $options['pagination_url']);
		Zend::register('pagination', $pagination);			

		//Finally, hydrate the Doctrine objects with the array of ids given
		$query = new Doctrine_Query;
		$query->select('i.*, t.*')->from('Item i');
		$query->leftJoin('Item.Tags t');
		$query->leftJoin('Item.Collection c');
		$query->leftJoin('i.Type ty');
		
		//If no IDs were returned in the first query, then whatever
		if(!empty($ids)) {
			$where = "(i.id = ".join(" OR i.id = ", $ids) . ")";
		}else {
			$where = "1 = 0";
		}
		
		
		$query->where($where);

		//Order by recent-ness
		if($recent = $this->_getParam('recent')) {
			$this->orderSelectByRecent($query);
		}
		
		$items = $query->execute();
		
		Zend::register('total_results', $total_results);
		
		$this->pluginHook('onBrowseItems', array($items));
		
		return $this->render('items/browse.php', compact('total_items', 'items'));
	}

	/**
	 * Get all the collections and all the active plugins for the form
	 *
	 * @return void
	 **/
	protected function loadFormData() 
	{
		$collections = $this->getTable('Collection')->findAll();
		$plugins = $this->getTable('Plugin')->findActive();
		$types = $this->getTable('Type')->findAll();
		
		if($this->_view) {
			$this->_view->assign(compact('collections', 'plugins', 'types'));
		}
	}
	
	public function showAction() 
	{
		$item = $this->findById();
		$user = Kea::loggedIn();
		
		//If the item is not public, check for permissions
		$canSeeNotPublic = 	($this->isAllowed('showNotPublic') or 
				($this->isAllowed('showSelfNotPublic') and $item->wasAddedBy($user)));
		
		if(!$item->public && !$canSeeNotPublic) {
			$this->_redirect('403');
		}
		
		//Add the tags
		 
		if(array_key_exists('modify_tags', $_POST) || !empty($_POST['tags'])) {
			
		 	if($this->isAllowed('tag')) {
				$tagsAdded = $item->commitForm($_POST);
				$item = $this->findById();
			}else {
				$this->flash('User does not have permission to add tags.');
			}
		}

		//@todo Does makeFavorite require a permissions check?
		if($this->getRequest()->getParam('makeFavorite')) {
			$item->toggleFavorite($user);
			$this->pluginHook('onMakeFavoriteItem', array($item, $user));
		}
		
		
		if($tagsAdded || $tagsDeleted) {
			//This is a workaround for the fact that the Tags collection doesn't get automatically refreshed
			$item->Tags = $this->getTable('Tag')->findSome(array('item_id'=>$item->id));
		}
		
		$item->refresh();
		
		Zend::register('item', $item);
		
		$this->pluginHook('onShowItem', array($item));
		
		return $this->render('items/show.php', compact("item", 'user'));
	}
	
	/**
	 * Will remove all instances of a particular tag from a particular Item
	 * Checks for $_POST key with name = 'remove_tag' and value = tag ID
	 *
	 * @return bool
	 **/
	
}
?>