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
		'saveExhibit'=> array('exhibits/browse'),
		'editExhibit'=> array('exhibits/edit/id', array('id')),
		'deleteExhibit'=>array('exhibits/browse'),
		'addPage'=>array('exhibits/addPage/id', array('id')),
		'editPage'=>array('exhibits/editPage/id/page', array('id'), array('page'))
	);
	
	public function init()
	{
		$this->_modelClass = 'Exhibit';
		
		require_once 'Zend/Session.php';
		$this->session = new Zend_Session_Namespace('Exhibit');
	}
	
	public function tagsAction()
	{
		$this->_forward('browse', 'Tags', null, array('tagType' => 'Exhibit', 'renderPage'=>'exhibits/tags.php'));
	}
	
	public function browseAction()
	{
		$filter = array();
		
		if(($tags = $this->_getParam('tag')) || ($tags = $this->_getParam('tags'))) {
			$filter['tags'] = $tags;
		}
				
		$exhibits = $this->_table->findBy($filter);
				
		Zend_Registry::set('exhibits', $exhibits);
		
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

		if( $item and $item->isInExhibit($exhibit) ) {
			
			Zend_Registry::set('item', $item);

			Zend_Registry::set('exhibit', $exhibit);

			Zend_Registry::set('section', $section);

			
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
		return $this->_forward('browse', 'items', null, $params);
	}
	
	public function showAction()
	{		
		$exhibit = $this->findBySlug();

		if(!$exhibit) {
			throw new Exception( 'Exhibit with that ID does not exist.' );
		}
				
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
		Zend_Registry::set('section',	$section);
		Zend_Registry::set('exhibit',	$exhibit);
		Zend_Registry::set('page',		$page);
		
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
	
	public function summaryAction()
	{
		$exhibit = $this->findBySlug();		
				
		Zend_Registry::set('exhibit', $exhibit);

		
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
			
			//Hack to get just the directory name for the exhibit themes
            $exhibitThemesDir = basename(EXHIBIT_THEMES_DIR);
            
			switch ($toRender) {
				case 'show':
					$renderPath = $exhibitThemesDir.DIRECTORY_SEPARATOR.$exhibit->theme.DIRECTORY_SEPARATOR.'show.php';
					break;
				case 'summary':
					$renderPath = $exhibitThemesDir. DIRECTORY_SEPARATOR . $exhibit->theme . DIRECTORY_SEPARATOR . 'summary.php';
					break;
				case 'item':
					$renderPath = $exhibitThemesDir.DIRECTORY_SEPARATOR.$exhibit->theme.DIRECTORY_SEPARATOR.'item.php';
					break;
				default:
					throw new Exception( 'Hey, you gotta render something!' );
					break;
			}
			
			if(isset($renderPath) and file_exists(SHARED_DIR.DIRECTORY_SEPARATOR.$renderPath)) {
				$this->render($renderPath, $vars);
			}else {
				throw new Exception( 
					"Exhibit theme named '$exhibit->theme' no longer exists!\n\n  
					Please change the exhibit's theme in order to properly view the exhibit." );
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
			$retVal = $exhibit->saveForm($_POST);

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
					
		} 
		catch (Omeka_Validator_Exception $e) {
			$this->flashValidationErrors($e);
		}
		catch (Exception $e) {
			$this->flash($e->getMessage());
		}
		
		$pass_to_template = compact('exhibit');
		$pass_to_template['record'] = $exhibit;
		
		return $this->render('exhibits/form/exhibit.php',$pass_to_template);
	}
	
	/**
	 * 1st URL param = 'id' for Exhibit
	 *
	 **/
	public function addSectionAction()
	{
		$exhibit = $this->findById();
		$section = new ExhibitSection;
		$section->exhibit_id = $exhibit->id;
		
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
			
			$retVal = $section->saveForm($toPost);
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
		
		//For a data feed, the record we want to render is the ExhibitSection
		$pass_to_template = compact('exhibit', 'section');
		$pass_to_template['record'] = $section;
		
		return $this->render('exhibits/form/section.php', $pass_to_template);	
			
	}
	
	/**
	 * Add a page to a section
	 *
	 * 1st URL param = 'id' for the section that will contain the page
	 * 
	 **/
	public function addPageAction()
	{
		$section = $this->findById(null,'ExhibitSection');
		
		if(isset($_POST['cancel'])) {
			$this->setLayout(null);
			$this->_redirect('editSection', array('id'=>$section->id));
		}
		
		//Check to see if the page var was saved in the session
		if($layout = $this->getLayout()) {
			
			$page = new ExhibitPage;

			$page->layout = $layout;						
		}else {

			$page = new ExhibitPage;
		}
		$page->section_id = $section->id;
				
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
		Zend_Registry::set('page', $page);
		
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

					$retVal = $page->saveForm($_POST);

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
		$section = $this->findById(null, 'ExhibitSection');
		
		$exhibit = $section->Exhibit;
		
		return $this->processSectionForm($section, $exhibit);
	}
	
	public function editPageAction()
	{
		$page = $this->findById(null,'ExhibitPage');
		$section = $page->Section;
		
		return $this->processPageForm($page, $section);
	}
	
	public function deleteSectionAction()
	{
		//Delete the section and re-order the rest of the sections in the exhibit
		
		$section = $this->findById(null,'ExhibitSection');
		$exhibit = $section->Exhibit;
				
		$section->delete();
		
		$this->_redirect('editExhibit', array('id'=>$exhibit->id) );
	}
	
	public function deletePageAction()
	{
		$page = $this->findById(null,'ExhibitPage');
		$section = $page->Section;
				
		$page->delete();
		
		$this->_redirect('editSection', array('id' => $section->id) );
	}
	
	/////HERE WE HAVE SOME AJAX-ONLY ACTIONS /////
	
	public function sectionFormAction()
	{
		$exhibit = $this->findById();
		
		$section = new ExhibitSection;
		$section->Exhibit = $exhibit;
		
		$this->render('exhibits/_section_form.php', compact('section'));
	}
	
	public function sectionListAction()
	{
		$exhibit = $this->findOrNew();
		
		return $this->render('exhibits/_section_list.php', compact('exhibit'));
	}
	
	public function pageListAction()
	{
		$section = $this->findById(null, 'ExhibitSection');

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
				$exhibit->saveForm($_POST);
			} catch (Omeka_Validator_Exception $e) {
				//Set the 404 response code
				$this->getResponse()->setHttpResponseCode(422); 
				
				$this->flashValidationErrors($e);
			}
			
			//Required to pass a 'record' instance so that Omeka can automatically render the JSON for the exhibit
			$passVariables = array('exhibit'=>$exhibit, 'record'=>$exhibit);
			
			$this->render('exhibits/show.php', $passVariables);
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
			return $this->_forward($slug, 'exhibits', null, $this->_getAllParams());
			exit;
		}
		
		//Check if it is a static page
		$page = $slug . '.php';
		
		//Try to render, invalid pages will be caught as exceptions
		try {
			return $this->render('exhibits' . DIRECTORY_SEPARATOR . $page);
		} catch (Zend_View_Exception $e) {}
		
		
		//Otherwise this is a slug for an Exhibit
		
		$this->_forward('summary', 'exhibits', null, $this->_getAllParams());
	}
} 
?>
