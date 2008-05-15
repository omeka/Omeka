<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

require_once 'Item.php';

/**
 * @see Omeka_Controller_Action
 **/
require_once 'Omeka/Controller/Action.php';

/**
 * @package Omeka
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class ItemsController extends Omeka_Controller_Action
{		
	public function init() 
	{
		$this->_modelClass = 'Item';
	}
	
	/**
	 * This wraps the builtin method with permissions checks
	 *
	 **/
	public function editAction()
	{
		if($user = $this->getCurrentUser()) {
			
			$item = $this->findById();
		
			//If the user cannot edit any given item
			if($this->isAllowed('editAll') or 
				//Check if they can edit this specific item
				($this->isAllowed('editSelf') and $item->wasAddedBy($user))) {
				
				return parent::editAction();	
			}
		}

		return $this->forbiddenAction();
	}
	
	/**
	 * Wrapping this crap with permissions checks
	 *
	 **/
	public function deleteAction()
	{
		if($user = $this->getCurrentUser()) {
			$item = $this->findById();
			
			//Permission check
			if($this->isAllowed('deleteAll') or ( $this->isAllowed('deleteSelf') and $item->wasAddedBy($user) )) {
				$item->delete();
				
				$this->redirect->goto('browse');
			}
		}
		
		return $this->_forward('forbidden');
	}
    
    /**
     * Finds all tags associated with items (used for tag cloud)
     * 
     * @return void
     **/
	public function tagsAction()
	{
	    $params = array_merge($this->_getAllParams(), array('type'=>'Item'));
		$tags = $this->getTable('Tag')->findBy($params);
		$this->render(compact('tags'));
	}
    
    /**
     * Browse the items.  Encompasses search, pagination, and filtering of
     * request parameters.  Should perhaps be split into a separate
     * mechanism.
     * 
     * @return void
     **/
	public function browseAction()
	{			
		$perms = array();
		$filter = array();
		$order = array();
		
		//Show only public items
		if( $this->_getParam('public') ) {
			$perms['public'] = true;
		}
		
		//Here we add some filtering for the request	
		try {
			
			//User-specific item browsing
			if($userToView = $this->_getParam('user')) {
						
				//Must be logged in to view items specific to certain users
				if(!$this->isAllowed('browse', 'Users')) {
					throw new Exception( 'May not browse by specific users.' );
				}
			
				if(is_numeric($userToView)) {
					$filter['user'] = $userToView;
				}
			}
			
			//Entity-specific browsing
			//@duplication
			if($entityToView = $this->_getParam('entity')) {
				if(!$this->isAllowed('browse', 'Entities')) {
					throw new Exception( 'May not browse by specific entities' );
				}
				
				if(is_numeric($entityToView)) {
					$filter['entity'] = $entityToView;
				}
			}
			
			
			if($this->_getParam('featured')) {
				$filter['featured'] = true;
			}
			
			if($collection = $this->_getParam('collection')) {
				$filter['collection'] = $collection;
			}
			
			if($type = $this->_getParam('type')) {
				$filter['type'] = $type;
			}
			
			if( ($tag = $this->_getParam('tag')) || ($tag = $this->_getParam('tags')) ) {
				$filter['tags'] = $tag;
			}
			
			if(($excludeTags = $this->_getParam('excludeTags'))) {
				$filter['excludeTags'] = $excludeTags;
			}
			
			$recent = $this->_getParam('recent');
			if($recent !== 'false') {
				$order['recent'] = true;
			}

			if($search = $this->_getParam('search')) {
				$filter['search'] = $search;
				//Don't order by recent-ness if we're doing a search
				unset($order['recent']);
			}
			
			//The advanced or 'itunes' search
			if($advanced = $this->_getParam('advanced')) {

				//We need to filter out the empty entries if any were provided
				foreach ($advanced as $k => $entry) {					
					if(empty($entry['field']) or empty($entry['type'])) {
						unset($advanced[$k]);
					}
				}
				$filter['advanced_search'] = $advanced;
			};

			if($range = $this->_getParam('range')) {
				$filter['range'] = $range;
			}
			
			
		} catch (Exception $e) {
			$this->flash($e->getMessage());
		}
		$params = array_merge($perms, $filter, $order);

		//Get the item count after other filtering has been applied, which is the total number of items found
		$totalResults = $this->getTable('Item')->count($params);
		Zend_Registry::set('total_results', $totalResults);				

		//Permissions are checked automatically at the SQL level
		$totalItems = $this->getTable('Item')->count();
		Zend_Registry::set('total_items', $totalItems);
		
		/** 
		 * Now process the pagination
		 * 
		 **/
		$paginationUrl = $this->getRequest()->getBaseUrl().'/items/browse/';
		$options = array(   'page'		=> 	1,
							'pagination_url' => $paginationUrl);
							
		//check to see if these options were changed by request vars
		$reqOptions = $this->_getAllParams();
		
		$options = array_merge($options, $reqOptions);
				
		$params['page'] = $options['page'];
		
		$params['per_page'] = $this->getItemsPerPage();
		
		//Retrieve the items themselves
		$items = $this->getTable('Item')->findBy($params);

		//Serve up the pagination
		$pagination = array('menu'=>$menu, 'page'=>$options['page'], 'per_page'=>$params['per_page'], 'total_results'=>$totalResults, 'link'=>$options['pagination_url']);
		Zend_Registry::set('pagination', $pagination);
		
		fire_plugin_hook('browse_items', $items);
						
		return $this->render(compact('total_items', 'items'));
	}
		
	/**
	 * Retrieve the number of items to display on any given browse page.
	 * This can be modified as a query parameter provided that a user is actually logged in.
	 *
	 * @return integer
	 **/	
	protected function getItemsPerPage()
	{
        //Retrieve the number from the config file
		$config = Omeka_Context::getInstance()->getConfig('basic');
		$per_page = $config->pagination->per_page;
                
        if($this->isAllowed('modifyPerPage') and $this->_getParam('per_page')) {
			$per_page = $this->_getParam('per_page');
		}	 
		
		return $per_page;   
	}

	///// AJAX ACTIONS /////
	
	/**
	 * Find or create an item for this mini-form
	 *
	 **/
	public function changeTypeAction()
	{
		if($id = $_POST['item_id']) {
			$item = $this->findById($id);
		}else {
			$item = new Item;
		}
		
		$item->type_id = $_POST['type_id'];
		
		return $this->render(compact('item'));
	}
	
	/**
	 * Display the form for tags for a given item.
	 * 
	 * @return void
	 **/
	public function tagFormAction()
	{
	    $item = $this->findById();
	    
	    return $this->render(compact('item'));
	}
	
	/**
	 * Modify the tags for an item (add or remove).  If this is an AJAX request, it will
	 * render the 'tag-list' partial, otherwise it will redirect to the
	 * 'show' action.
	 * 
	 * @return void
	 **/
	public function modifyTagsAction()
	{
		$item = $this->findById();

		//Add the tags
		 
		if(array_key_exists('modify_tags', $_POST) || !empty($_POST['tags'])) {
			
		 	if($this->isAllowed('tag')) {
				$tagsAdded = $item->saveForm($_POST);
				$item = $this->findById();
			}else {
				$this->flash('User does not have permission to add tags.');
			}
		}
		
		if(!$this->getRequest()->isXmlHttpRequest()) {
		    $itemId = $this->_getParam('id');
		    return $this->redirect->gotoRoute(array('controller'=>'items', 'action'=>'show','id'=>$itemId), 'id');
		}
		
		return $this->render(compact('item'), 'tag-list');	    
	}
	
	///// END AJAX ACTIONS /////
	
	/**
	 * Change the 'public' or 'featured' status of items
	 * 
	 * @return void
	 **/
	public function powerEditAction()
	{
		/*POST in this format:
		 			items[1][public],
		 			items[1][featured],
					items[1][id],
					items[2]...etc
		*/
		if(empty($_POST)) {
			$this->redirect->goto('browse');
		}
		
		
		try {
			if(!$this->isAllowed('makePublic')) {
				throw new Exception( 'User is not allowed to modify visibility of items.' );
			}

			if(!$this->isAllowed('makeFeatured')) {
				throw new Exception( 'User is not allowed to modify featured status of items' );
			}
			
			if($item_a = $this->_getParam('items')) {
										
				//Loop through the IDs given and toggle
				foreach ($item_a as $k => $fields) {

					$item = $this->findById($fields['id']);
		
					//Process the public field
					
					//Existing status must be compared against new status for the sake of plugin hooks
					$old = $item->public;
					$new = array_key_exists('public', $fields);
					
					//If the item was made public, fire the plugin hook
					if(!$old and $new) {
						fire_plugin_hook('make_item_public', $item);
					}
									
					//If public has been checked
					$item->public = $new;
					
					$item->featured = array_key_exists('featured', $fields);
									
					$item->save();
					
				}		
			}
			$this->flashSuccess('Changes were successful');
			
		} catch (Exception $e) {
			$this->flash($e->getMessage());
		}
		
		$this->redirect->gotoUrl($_SERVER['HTTP_REFERER']);
	}
	
}