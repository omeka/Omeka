<?php 
require_once MODEL_DIR.DIRECTORY_SEPARATOR.'Exhibit.php';
/**
 * @package Omeka
 **/
require_once 'Omeka/Controller/Action.php';
class ExhibitsController extends Omeka_Controller_Action
{
	protected $session;
	
	protected $_redirects = array(
		'addSection' => array('exhibits/addSection/id', array('id')),
		'editSection'=> array('exhibits/editSection/id', array('id')),
		'saveExhibit'=> array('exhibits/slug/', array('slug')),
		'editExhibit'=> array('exhibits/edit/id', array('id')),
		'deleteExhibit'=>array('exhibits/browse'),
		'addPage'=>array('exhibits/addPage/id', array('id')),
		'editPage'=>array('exhibits/editPage/id/page', array('id'), array('page'))
	);
	
	public function init()
	{
		$this->_modelClass = 'Exhibit';
		$this->_table = $this->getTable('Exhibit');
		
		require_once 'Zend/Session.php';
		$this->session = new Zend_Session('Exhibit');
	}
	
	public function tagsAction()
	{
		$this->_forward('Tags', 'browse', array('tagType' => 'Exhibit', 'renderPage'=>'exhibits/tags.php'));
	}
	
	public function browseAction()
	{
		$filter = array();
		
		if(($tags = $this->_getParam('tag')) || ($tags = $this->_getParam('tags'))) {
			$filter['tags'] = $tags;
		}
		
		if(!$this->isAllowed('showNotPublic')) {
			$filter['public'] = true;
		}
		
		$exhibits = $this->_table->findBy($filter);
				
		Zend::register('exhibits', $exhibits);
		
		fire_plugin_hook('browse_exhibits', $exhibits);
		
		return $this->render('exhibits/browse.php', compact('exhibits'));
	}
	
	public function showitemAction()
	{
		$item_id = $this->_getParam('item_id');
		$slug = $this->_getParam('slug');
		
		$exhibit = is_numeric($slug) ?
			$this->_table->find($slug) :
			$this->_table->findBySlug($slug);
			
		$item = $this->findById($item_id, 'Item');	
		
		$section_name = $this->_getParam('section');
		$section = $exhibit->getSection($section_name);

		if( $item->isInExhibit($exhibit->id) ) {
			
			//Permissions check
			if(!$item->public and !$this->isAllowed('showNotPublic')) {

				$this->_redirect('403');
			}
			
			Zend::register('item', $item);
			Zend::register('exhibit', $exhibit);
			Zend::register('section', $section);
			
			//Plugin hooks
			fire_plugin_hook('show_exhibit_item',  $item, $exhibit);
			
			return $this->renderExhibit(compact('exhibit','item', 'section'), 'item');
		}else {
			$this->flash('This item is not used within this exhibit.');
			$this->_redirect('403');
		}
	}
	
	public function itemsAction()
	{
		$params = $this->_getAllParams();
		//Make sure to render that specific page and only show public items
		$params = array_merge($params, array('renderPage'=>'exhibits/_items.php'));
		return $this->_forward('items', 'browse', $params);
	}
	
	public function showAction()
	{		
		$exhibit = $this->findBySlug();

		

		if(!$exhibit) {
			throw new Exception( 'Exhibit with that ID does not exist.' );
		}
		
		$this->checkPermission($exhibit);
		
		$section = $this->_getParam('section');

		$section = $exhibit->getSection($section);
		
		if($section) {
			$page_order = $this->_getParam('page');

			$page = $section->getPage($page_order);			
		}
		
		$layout = $page->layout;

/*
			if(!$section) {
			$this->flash('This section does not exist for this exhibit.');
		}
		elseif(!$page) {
			$this->flash('This page does not exist in this section of the exhibit.');
		}
*/	
		
		//Register these so that theme functions can use them
		Zend::register('section',	$section);
		Zend::register('exhibit',	$exhibit);
		Zend::register('page',		$page);
		
		fire_plugin_hook('show_exhibit', $exhibit,$section,$page);

		$this->renderExhibit(compact('section','exhibit','page'));
	}
	
	protected function findBySlug($slug=null) 
	{
		if(!$slug) {
			$slug = $this->_getParam('slug');
		}
		
		//Slug can be either the numeric 'id' for the exhibit or the alphanumeric slug
		if(is_numeric($slug)) {
			$exhibit = $this->_table->findById($slug);
		}else {
			$exhibit = $this->_table->findBySlug($slug);
		}
		return $exhibit;
	}
	
	protected function checkPermission($exhibit)
	{
		if(!$exhibit->public and !$this->isAllowed('showNotPublic')) {
			$this->_redirect('forbidden', array('controller'=>'exhibits'));
		}
	}
	
	public function summaryAction()
	{
		$exhibit = $this->findBySlug();
		
		
		
		$this->checkPermission($exhibit);
		
		Zend::register('exhibit', $exhibit);
		
		fire_plugin_hook('show_exhibit', $exhibit);
		
		return $this->renderExhibit(compact('exhibit'), 'summary');
	}
	
	/**
	 * Figure out how to render the exhibit.  
	 * 1) the view needs access to the shared directories
	 * 2) if the exhibit has an associated theme, render the pages for that specific exhibit theme, 
	 *		otherwise display the generic theme pages in the main public theme
	 * 
	 * @return void
	 **/
	protected function renderExhibit($vars, $toRender='show') {
		/* 	If there is a theme, render the header/footer and layout page,
			Otherwise render the default exhibits/show.php page
		*/
		extract($vars);
		
		if(!empty($exhibit->theme)) {
		
			$this->_view->addScriptPath(SHARED_DIR);
			
			$site = Zend::Registry('path_names');

			switch ($toRender) {
				case 'show':
					$renderPath = $site['exhibit_themes'].DIRECTORY_SEPARATOR.$exhibit->theme.DIRECTORY_SEPARATOR.'show.php';
					break;
				case 'summary':
					$renderPath = $site['exhibit_themes']. DIRECTORY_SEPARATOR . $exhibit->theme . DIRECTORY_SEPARATOR . 'summary.php';
					break;
				case 'item':
					$renderPath = $site['exhibit_themes'].DIRECTORY_SEPARATOR.$exhibit->theme.DIRECTORY_SEPARATOR.'item.php';
					break;
				default:
					throw new Exception( 'Hey, you gotta render something!' );
					break;
			}
			
			if(isset($renderPath) and file_exists(SHARED_DIR.DIRECTORY_SEPARATOR.$renderPath)) {
				$this->render($renderPath, $vars);
			}	
			
		}else {
			switch ($toRender) {
				case 'show':
					$path = 'exhibits/show.php';
					break;
				case 'summary':
					$path = 'exhibits/summary.php';
					break;
				case 'item':
					$path = 'items/show.php';
					break;
				default:
					throw new Exception( 'You gotta render some stuff because whatever!' );
					break;
			}
			
			return $this->render($path, $vars);
		}
	}
	
	public function addAction()
	{		
		$exhibit = new Exhibit;
				
		return $this->processExhibitForm($exhibit);
	}

	public function editAction()
	{	
		$exhibit = $this->findById();
		
/*		if(1==1) {
			$q->addSelect("et.*");
			$q->leftJoin("e.ExhibitsTags et");
			$user_id = Omeka::loggedIn()->id;
			$q->addWhere("et.user_id = :user_id", array('user_id'=>$user_id));
		}
*///		echo $q;exit;

		
		
		return $this->processExhibitForm($exhibit);
	}	
	
	/**
	 * This is where all the redirects and page rendering goes
	 *
	 * @return mixed
	 **/
	protected function processExhibitForm($exhibit)
	{
		try {
			$retVal = $exhibit->commitForm($_POST);

			if($retVal) {
				if(array_key_exists('add_section',$_POST)) {
					//forward to addSection & unset the POST vars 
					unset($_POST);
					$this->_redirect('addSection', array('id'=>$exhibit->id) );
					return;
				}elseif(array_key_exists('save_exhibit', $_POST)) {
				
					$this->_redirect('saveExhibit', array('slug'=>$exhibit->slug));
				}else {
				
					//Everything else should render the page
					//return $this->render('exhibits/form/exhibit.php',compact('exhibit'));
				}			
			}
					
		} catch (Exception $e) {
			$this->flash($e->getMessage());
		}
		
		return $this->render('exhibits/form/exhibit.php',compact('exhibit'));
	}
	
	/**
	 * 1st URL param = 'id' for Exhibit
	 *
	 **/
	public function addSectionAction()
	{
		$exhibit = $this->findById();
		$section = new Section;
		$section->Exhibit = $exhibit;
		
		//Give the new section a section order (1, 2, 3, ...)
		$numSections = $exhibit->getSectionCount();
		$section->order = $numSections + 1;
		
		//Tell the plugin hook that we are adding a section
		$this->addSection = true;
		
		return $this->processSectionForm($section, $exhibit);
	}
	
	protected function processSectionForm($section, $exhibit=null)
	{
		//Check for a 'cancel' button so we can redirect
		if(isset($_POST['cancel_section'])) {
			$this->_redirect('editExhibit', array('id' => $section->exhibit_id));
		}
		
		try {
			//Section form may be prefixed with Section (like name="Section[title]") or it may not be, depending
			
			if(array_key_exists('Section', $_POST)) {
				$toPost = $_POST['Section'];
			}else {
				$toPost = $_POST;
			}
			
			$retVal = $section->commitForm($toPost);
		} catch (Exception $e) {
			$this->flash($e->getMessage());
			$retVal = false;
		}
		
		//If successful form submission
		if($retVal)
		{	
			//Forward around based on what submit button was pressed
			
			if(array_key_exists('exhibit_form',$_POST)) {
				
				//Forward to the 'edit' action
				$this->_redirect('editExhibit', array('id'=>$section->Exhibit->id)); 
				return;
			
			}elseif(array_key_exists('page_form',$_POST)) {
				
				//Forward to the addPage action (id is the section id)
				$this->_redirect('addPage', array('id'=>$section->id));
				return;
				
			}elseif(array_key_exists('add_new_section', $_POST)) {
				//Forward back to adding a new section to the exhibit
				$this->_redirect('addSection', array('id'=>$section->Exhibit->id));
			}
		}
				
		//this is an AJAX request
		if($this->isAjaxRequest()) {
			//If the form submission was invalid 
			if(!$retVal) {
				//Send a header that will inform us that the request was a failure
				//@see http://tech.groups.yahoo.com/group/rest-discuss/message/6183
				header ("HTTP/1.0 422 Unprocessable Entity");

			}				
		}
		
		//If we are trying to render the JSON output, it's on a different page
		if($this->_getParam('output') == 'json') {
			return $this->render('exhibits/section.php', compact('section'));
		}
		//Otherwise we are rendering the admin theme's section form page
		else {
			return $this->render('exhibits/form/section.php',compact('exhibit','section'));		
		}		
	}
	
	/**
	 * Add a page to a section
	 *
	 * 1st URL param = 'id' for the section that will contain the page
	 * 
	 **/
	public function addPageAction()
	{
		$section = $this->findById(null,'Section');
		
		if(isset($_POST['cancel'])) {
			$this->setLayout(null);
			$this->_redirect('editSection', array('id'=>$section->id));
		}
		
		//Check to see if the page var was saved in the session
		if($layout = $this->getLayout()) {
			
			$page = new SectionPage;

			$page->layout = $layout;
			$page->Section = $section;
						
		}else {

			$page = new SectionPage;
			$page->Section = $section;			

		}
				
		//Set the order for the new page
		$numPages = $section->getPageCount();
		$page->order = $numPages + 1;
		
		return $this->processPageForm($page, $section);
	}
	
	protected function getLayout()
	{
		return $this->session->layout;
	}
	
	protected function setLayout($layout)
	{
		$this->session->layout = (string) $layout;
	}
		
	protected function processPageForm($page, $section=null) 
	{
		//'cancel_and_section_form' and 'cancel_and_exhibit_form' as POST elements will cancel adding a page
		//And they will redirect to whatever form is important
		if(isset($_POST['cancel_and_section_form'])) {
			$this->_redirect('editSection', array('id'=>$page->section_id));
		}
		
		if(isset($_POST['cancel_and_exhibit_form'])) {
			$this->_redirect('editExhibit', array('id'=>$section->exhibit_id));
		}
		
		//Register the page var so that theme functions can use it
		Zend::register('page', $page);
		
		if(!empty($_POST)) {
			
			if(array_key_exists('choose_layout', $_POST)) {
			
				//A layout has been chosen for the page
				$this->setLayout($_POST['layout']);
				
				$page->layout = (string) $_POST['layout'];
				
				return $this->render('exhibits/form/page.php', compact('page','section'));
			
			}elseif(array_key_exists('change_layout', $_POST)) {
				
				//User wishes to change the current layout
				
				//Reset the layout vars
				$this->setLayout(null);
				$page->layout = null;
				
				return $this->render('exhibits/form/layout.php', compact('page','section'));		
			}
				
			else {
				try {
					
					if($layout = $this->getLayout()) {
						$page->layout = $layout;
					}

					$retVal = $page->commitForm($_POST);
				} catch (Exception $e) {
					$this->flash($e->getMessage());
				}
								
				//Otherwise the page form has been submitted
				if($retVal) {
				
					//Unset the page var that was saved in the session
					$this->setLayout(null);
				
				
					if(array_key_exists('exhibit_form', $_POST)) {
					
						//Return to the exhibit form
						$this->_redirect('editExhibit', array('id'=>$section->Exhibit->id));
						return;
					
					}elseif(array_key_exists('section_form', $_POST)) {
					
						//Return to the section form
						$this->_redirect('editSection', array('id'=>$section->id));
						return;
					
					}elseif(array_key_exists('page_form', $_POST)) {
					
						//Add another page
						$this->_redirect('addPage', array('id'=>$section->id));
						return;
					
					}elseif(array_key_exists('save_and_paginate', $_POST)) {
				
						//User wants to save the current set of pagination
						//@todo How would this work?
						$paginationPage = $this->_getParam('page');
						
						$this->_redirect('editPage', array('id'=>$page->id, 'page'=>$paginationPage) );
						return;
						
					}
				}
			}
		}
				
		if ( empty($page->layout) ) {
			return $this->render('exhibits/form/layout.php', compact('section','page'));
		}else {
			return $this->render('exhibits/form/page.php',compact('section','page'));	
		}		
	}
	
	/**
	 * 1st URL param = Section ID
	 *
	 **/
	public function editSectionAction()
	{
		$section = $this->findById(null, 'Section');
		
		$exhibit = $section->Exhibit;
		
		$section->loadPages();

		return $this->processSectionForm($section, $exhibit);
	}
	
	public function editPageAction()
	{
		$page = $this->findById(null,'SectionPage');
		$section = $page->Section;
		
		return $this->processPageForm($page, $section);
	}
	
	public function deleteSectionAction()
	{
		//Delete the section and re-order the rest of the sections in the exhibit
		
		$section = $this->findById(null,'Section');
		$exhibit = $section->Exhibit;
		
		fire_plugin_hook('delete_exhibit_section',  $section);
		
		$section->delete();
		$exhibit->reorderSections();
		
		$this->_redirect('editExhibit', array('id'=>$exhibit->id) );
	}
	
	public function deletePageAction()
	{
		$page = $this->findById(null,'SectionPage');
		$section = $page->Section;
		
		fire_plugin_hook('delete_exhibit_page',  $page);
		
		$page->delete();
		
		$this->_redirect('editSection', array('id' => $section->id) );
	}
	
	/////HERE WE HAVE SOME AJAX-ONLY ACTIONS /////
	
	public function sectionFormAction()
	{
		$exhibit = $this->findById();
		
		$section = new Section;
		$section->Exhibit = $exhibit;
		
		$this->render('exhibits/_section_form.php', compact('section'));
	}
	
	public function sectionListAction()
	{
		$exhibit = $this->findOrNew();
		
		$this->render('exhibits/_section_list.php', compact('exhibit'));
	}
	
	public function pageListAction()
	{
		$section = $this->findById(null, 'Section');
		$section->loadPages();
		$this->render('exhibits/_page_list.php', compact('section'));
	}
	
	protected function findOrNew()
	{
		try {
			$exhibit = $this->findById();
		} catch (Exception $e) {
			$exhibit = new Exhibit;
		}
		return $exhibit;
	}

	/**
	 * Return the Exhibit ID as a JSON value
	 *
	 * @return void
	 **/
	public function saveAction()
	{
		//Run a permission check
		if(!$this->isAllowed('add')) {
			$this->forbiddenAction();
		}
		
		if(!empty($_POST)) {
			$exhibit = $this->findOrNew();
			
			require_once 'Zend/Json.php';
			$return = array();
			try {
				$exhibit->commitForm($_POST);
			} catch (Exception $e) {
				//We pass this stupid header b/c Prototype doesn't know anything otherwise
				header ("HTTP/1.0 404 Not Found"); 
				
				$this->flash($e->getMessage());
			}
			$this->render('exhibits/show.php', compact('exhibit'));
		}
	}
	
	/////END AJAX-ONLY ACTIONS
	
	/**
	 * The route exhibits/whatever can be one of three things: 
	 *	built-in controller action
	 *	static page
	 *	exhibit slug
	 *
	 *	Unfortunately we have no way of knowing which one it is without a complicated database/filesystem check,
	 *	so it can't go in the routes file (at least not in any way I've been able to figure out) -- Kris
	 * 
	 * @return void
	 **/
	public function routeSimpleAction()
	{
		//Check if it is a built in controller action
		$slug = strtolower($this->_getParam('slug'));
		
		$action = $slug . 'Action';
		
		if(method_exists($this, $action)) {
			$this->_setParam('action', $slug);
			return $this->_forward('exhibits', $slug, $this->_getAllParams());
			exit;
		}
		
		//Check if it is a static page
		$page = $slug . '.php';
		
		//Try to render, invalid pages will be caught as exceptions
		try {
			return $this->render('exhibits' . DIRECTORY_SEPARATOR . $page);
		} catch (Zend_View_Exception $e) {}
		
		
		//Otherwise this is a slug for an Exhibit
		
		$this->_forward('exhibits', 'summary', $this->_getAllParams());
	}
} 
?>
