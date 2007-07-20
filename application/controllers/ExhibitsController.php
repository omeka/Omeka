<?php 
require_once MODEL_DIR.DIRECTORY_SEPARATOR.'Exhibit.php';
/**
 * @package Omeka
 **/
require_once 'Kea/Controller/Action.php';
class ExhibitsController extends Kea_Controller_Action
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
		
		$exhibits = $this->_table->findBy($filter);
				
		Zend::register('exhibits', $exhibits);
		
		return $this->render('exhibits/browse.php');
	}
	
	public function showitemAction()
	{
		$item_id = $this->_getParam('item_id');
		$slug = $this->_getParam('slug');
		
		$exhibit = is_numeric($slug) ?
			$this->_table->find($slug) :
			$this->_table->findBySlug($slug);
			
		$item = $this->findById($item_id, 'Item');	
		
		$section_order = $this->_getParam('section_order');

		$section = $exhibit->getSection($section_order);

		if( $item->isInExhibit($exhibit->id) ) {
			
			//Permissions check
			if(!$item->public and !$this->isAllowed('showNotPublic')) {

				$this->_redirect('403');
			}
			
			Zend::register('item', $item);
			Zend::register('exhibit', $exhibit);
			Zend::register('section', $section);
			
			//Plugin hooks
			$this->pluginHook('onShowExhibitItem', array($item, $exhibit));
			
			return $this->renderExhibit(compact('exhibit','item'), 'item');
		}else {
			$this->flash('This item is not used within this exhibit.');
			$this->_redirect('403');
		}
	}
	
	public function showAction()
	{		
		$exhibit = $this->findBySlug();
				
		if(!$exhibit) {
			throw new Exception( 'Exhibit with that ID does not exist.' );
		}
		
//		$theme = $exhibit->theme;
		
		$section_order = $this->_getParam('section_order');
				
		if(!$section_order) {
			throw new Exception( 'Please update your routes.ini file.' );
			$this->_redirect('404');
		}
		$section = $exhibit->getSection($section_order);
		
		if($section) {
			$page_order = $this->_getParam('page_order');

			$page = $section->getPage($page_order);			
		}
		
		$layout = $page->layout;
		
/*		if(!$exhibit) {
			$this->errorAction();
			return;
		}
*/		
		if(!$section) {
			$this->flash('This section does not exist for this exhibit.');
		}
		elseif(!$page) {
			$this->flash('This page does not exist in this section of the exhibit.');
		}
		
		//Register these so that theme functions can use them
		Zend::register('section',	$section);
		Zend::register('exhibit',	$exhibit);
		Zend::register('page',		$page);
		
		$this->pluginHook('onShowExhibit', array($exhibit,$section,$page));

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
		Zend::register('exhibit', $exhibit);
		return $this->renderExhibit(compact('exhibit'), 'summary');
	}
	
	protected function renderExhibit($vars, $toRender='layout') {
		/* 	If there is a theme, render the header/footer and layout page,
			Otherwise render the default exhibits/show.php page
		*/
		extract($vars);
		
		if(!empty($exhibit->theme)) {
		
			$this->_view->addScriptPath(SHARED_DIR);
			
			$site = Zend::Registry('path_names');

			$headerPath = $site['exhibit_themes'].DIRECTORY_SEPARATOR.$exhibit->theme.DIRECTORY_SEPARATOR.'header.php';
			if(file_exists(SHARED_DIR.DIRECTORY_SEPARATOR.$headerPath)) {
				$this->render($headerPath, $vars);
			}
			
			switch ($toRender) {
				case 'layout':
					$renderPath = $site['exhibit_layouts'].DIRECTORY_SEPARATOR.$page->layout.DIRECTORY_SEPARATOR.'layout.php';
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
			
			$footerPath = $site['exhibit_themes'].DIRECTORY_SEPARATOR.$exhibit->theme.DIRECTORY_SEPARATOR.'footer.php';
			if(file_exists(SHARED_DIR.DIRECTORY_SEPARATOR.$footerPath)) {
				$this->render($footerPath, $vars);
			}			
			
		}else {
			switch ($toRender) {
				case 'layout':
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
		
		//Set a sess var so that plugin hooks can tell whether adding or editing
		$this->session->adding = true;
		
		return $this->processExhibitForm($exhibit);
	}

	public function editAction()
	{	
		//Make sure the exhibit is retrieved with sections in the proper order		
		$dql = "SELECT e.*, s.* FROM Exhibit e, e.Sections s WHERE e.id = :id ORDER BY s.section_order ASC";
		$q = new Doctrine_Query;
		$q->parseQuery($dql);
		
/*		if(1==1) {
			$q->addSelect("et.*");
			$q->leftJoin("e.ExhibitsTags et");
			$user_id = Kea::loggedIn()->id;
			$q->addWhere("et.user_id = :user_id", array('user_id'=>$user_id));
		}
*///		echo $q;exit;
		
		$id = $this->_getParam('id');
		
		$exhibit = $q->execute(compact('id'))->getFirst();
		
		return $this->processExhibitForm($exhibit);
	}	
	
	/**
	 * This is where all the redirects and page rendering goes
	 *
	 * @return mixed
	 **/
	protected function processExhibitForm($exhibit)
	{
		$retVal = $exhibit->commitForm($_POST);
		
		//Fire the plugin hook depending on whether exhibit is added or edited
		if($retVal) {
			$hookName = (isset($this->session->adding) ? 'onAddExhibit' : 'onEditExhibit');
			$this->pluginHook($hookName, array($exhibit));
		}
		
		//Unset the session var that tells the plugins which hook to fire
		unset($this->session->adding);
		
		if($retVal) {
			if(array_key_exists('add_section',$_POST)) {
				//forward to addSection & unset the POST vars 
				unset($_POST);
				$this->_redirect('addSection', array('id'=>$exhibit->id) );
				return;
			}elseif(array_key_exists('save_exhibit', $_POST)) {
			
				$this->_redirect('saveExhibit');
			}else {
			
				//Everything else should render the page
				//return $this->render('exhibits/form/exhibit.php',compact('exhibit'));
			}			
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
		$this->session->addingSection = true;
		
		return $this->processSectionForm($section, $exhibit);
	}
	
	protected function processSectionForm($section, $exhibit=null)
	{
		$retVal = $section->commitForm($_POST);
		
		if($retVal) {
			$hook = ($this->session->addingSection ? 'onAddExhibitSection' : 'onEditExhibitSection');
			$this->pluginHook($hook, array($section));
		}
		
		//Unset session var for plugin hooks
		unset($this->session->addingSection);
		
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
				
			}
		}
		
		return $this->render('exhibits/form/section.php',compact('exhibit','section'));		
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
		
		if(isset($_GET['cancel'])) {
			unset($this->session->page);
			$this->_redirect('editSection', array('id'=>$section->id));
		}
		
		//Check to see if the page var was saved in the session
		if(isset($this->session->page)) {

			$page = $this->session->page;

		}else {

			$page = new SectionPage;
			$page->Section = $section;			

		}
		
		$this->session->addingPage = true;
		
		//Set the order for the new page
		$numPages = $section->getPageCount();
		$page->order = $numPages + 1;
		
		return $this->processPageForm($page, $section);
	}
	
	protected function processPageForm($page, $section=null) 
	{
		if(!empty($_POST)) {
			
			if(array_key_exists('choose_layout', $_POST)) {
			
				//A layout has been chosen for the page
				$page->layout = $_POST['layout'];
				$this->session->page = $page;
				
		//		$page->save();
				
				$this->_redirect('addPage', array('id'=> $section->id));
			
			}elseif(array_key_exists('change_layout', $_POST)) {
				
				//User wishes to change the current layout
				$page->layout = null;
			
				//We'll need to register this with the session
				$this->session->page = $page;
		
			}
				
			else {
				$retVal = $page->commitForm($_POST);
				
				if($retVal) {
					$hook = ($this->session->addingPage ? 'onAddExhibitPage' : 'onEditExhibitPage');
					$this->pluginHook($hook, array($page));
				}
				
				//Unset session var that tells us we are adding a page
				unset($this->session->addingPage);
				
				//Otherwise the page form has been submitted
				if($retVal) {
				
					//Unset the page var that was saved in the session
					if(isset($this->session->page)) {
						unset($this->session->page);
					}
				
				
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
						unset($_POST);
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
		
		//Register the page var so that theme functions can use it
		Zend::register('page', $page);
		return $this->render('exhibits/form/page.php',compact('section','page'));		
	}
	
	/**
	 * 1st URL param = Section ID
	 *
	 **/
	public function editSectionAction()
	{
		$dql = "SELECT s.*, p.* FROM Section s, s.Pages p WHERE s.id = ? ORDER BY p.page_order ASC";
		$q = new Doctrine_Query;
		$section_id = $this->_getParam('id');
		$section = $q->parseQuery($dql)->execute(array($section_id))->getFirst();

		return $this->processSectionForm($section, $exhibit);
	}
	
	public function editPageAction()
	{
		$page = $this->findById(null,'SectionPage');
		$section = $page->Section;
	//	Zend::dump( $_POST );exit;
		//Session var says we are not adding a page (for plugin hooks)
		$this->session->addingPage = false;
		
		return $this->processPageForm($page, $section);
	}
	
	public function deleteSectionAction()
	{
		//Delete the section and re-order the rest of the sections in the exhibit
		
		$section = $this->findById(null,'Section');
		$exhibit = $section->Exhibit;
		
		$this->pluginHook('onDeleteExhibitSection', array($section));
		
		$section->delete();
		$exhibit->reorderSections();
		
		$this->_redirect('editExhibit', array('id'=>$exhibit->id) );
	}
	
	public function deletePageAction()
	{
		$page = $this->findById(null,'SectionPage');
		$section = $page->Section;
		
		$this->pluginHook('onDeleteExhibitPage', array($page));
		
		$page->delete();
		
		$this->_redirect('editSection', array('id' => $section->id) );
	}
} 
?>
