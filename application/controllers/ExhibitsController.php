<?php 
require_once MODEL_DIR.DIRECTORY_SEPARATOR.'Exhibit.php';
/**
 * @package Omeka
 **/
require_once 'Kea/Controller/Action.php';
class ExhibitsController extends Kea_Controller_Action
{
	protected $session;
	
	public function init()
	{
		$this->_modelClass = 'Exhibit';
		$this->_table = Doctrine_Manager::getInstance()->getTable('Exhibit');
		
		require_once 'Zend/Session.php';
		$this->session = new Zend_Session('Exhibit');
	}
	
	public function showAction()
	{		
		$slug = $this->_getParam('slug');
		
		//Slug can be either the numeric 'id' for the exhibit or the alphanumeric slug
		if(is_numeric($slug)) {
			$exhibit = $this->_table->findById($slug);
		}else {
			$exhibit = $this->_table->findBySlug($slug);
		}
		
		
//		$theme = $exhibit->theme;
		
		$ps = $this->getSectionAndPage($exhibit);
		extract($ps);
		
		$layout = $page->layout;
		
/*		if(!$exhibit) {
			$this->errorAction();
			return;
		}
*/		
		if(!$section) {
			$this->flash('There are no sections in this exhibit.');
		}
		elseif(!$page) {
			$this->flash('There are no pages in this section of the exhibit.');
		}
		
		//Register these so that theme functions can use them
		Zend::register('section',	$section);
		Zend::register('exhibit',	$exhibit);
		Zend::register('page',		$page);
		
		
		/* 	If there is a theme, render the header/footer and layout page,
			Otherwise render the default exhibits/show.php page
		*/
		if(!empty($exhibit->theme)) {
		
			$this->_view->addScriptPath(SHARED_DIR);
			
			$site = Zend::Registry('path_names');
			
			$headerPath = $site['exhibit_themes'].DIRECTORY_SEPARATOR.$exhibit->theme.DIRECTORY_SEPARATOR.'header.php';
			$layoutPath = $site['exhibit_layouts'].DIRECTORY_SEPARATOR.$page->layout.DIRECTORY_SEPARATOR.'layout.php';
			$footerPath = $site['exhibit_themes'].DIRECTORY_SEPARATOR.$exhibit->theme.DIRECTORY_SEPARATOR.'footer.php';

			if(file_exists(SHARED_DIR.DIRECTORY_SEPARATOR.$headerPath)) {
				$this->render($headerPath, compact('section','exhibit','page'));
			}
			
			if(file_exists(SHARED_DIR.DIRECTORY_SEPARATOR.$layoutPath)) {
				$this->render($layoutPath, compact('section','exhibit','page'));
			}
			
			if(file_exists(SHARED_DIR.DIRECTORY_SEPARATOR.$footerPath)) {
				$this->render($footerPath, compact('section','exhibit','page'));
			}			
		}else {
			$this->render('exhibits/show.php',compact('theme','layout','exhibit','section'));
		}
		
	}
	
	protected function getSectionAndPage($exhibit)
	{
		$section_order = $this->_getParam('section_order');
		$section = $exhibit->getSection($section_order);
		
		if($section) {
			$page_order = $this->_getParam('page_order');
			$page = $section->getPage($page_order);			
		}

		
		return compact('page','section');
	}
	
	public function addAction()
	{		
		$exhibit = new Exhibit;
		
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
		$retVal = $this->commitExhibitForm($exhibit);
		
		if($retVal) {
			if(array_key_exists('add_section',$_POST)) {
				//forward to addSection & unset the POST vars 
				unset($_POST);
				$this->_redirect('exhibits/addSection/'.$exhibit->id);
				return;
			}elseif(array_key_exists('save_exhibit', $_POST)) {
			
				//stay on this same pag
				$this->_redirect('exhibits/browse');
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
		
		return $this->processSectionForm($section, $exhibit);
	}
	
	protected function processSectionForm($section, $exhibit=null)
	{
		//If successful form submission
		if($this->commitSectionForm($section))
		{
			//Forward around based on what submit button was pressed
			
			if(array_key_exists('exhibit_form',$_POST)) {
				
				//Forward to the 'edit' action
				$this->_redirect('exhibits/edit/'.$section->Exhibit->id); 
				return;
			
			}elseif(array_key_exists('page_form',$_POST)) {
				
				//Forward to the addPage action (id is the section id)
				$this->_redirect('exhibits/addPage/'.$section->id);
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
		
		//Check to see if the page var was saved in the session
		if(isset($this->session->page)) {

			$page = $this->session->page;

		}else {

			$page = new SectionPage;
			$page->Section = $section;			

		}

		
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
			
				//We'll need to register this with the session
				$this->session->page = $page;
			
			}elseif(array_key_exists('change_layout', $_POST)) {
				
				//User wishes to change the current layout
				$page->layout = null;
			
				//We'll need to register this with the session
				$this->session->page = $page;
		
			}
				
			else {
			
				//Otherwise the page form has been submitted
				if($this->commitPageForm($page)) {
				
					//Unset the page var that was saved in the session
					if(isset($this->session->page)) {
						unset($this->session->page);
					}
				
				
					if(array_key_exists('exhibit_form', $_POST)) {
					
						//Return to the exhibit form
						$this->_redirect('exhibits/edit/'.$section->Exhibit->id);
						return;
					
					}elseif(array_key_exists('section_form', $_POST)) {
					
						//Return to the section form
						$this->_redirect('exhibits/editSection/'.$section->id);
						return;
					
					}elseif(array_key_exists('page_form', $_POST)) {
					
						//Add another page
						unset($_POST);
						$this->_redirect('exhibits/addPage/'.$section->id);
						return;
					
					}elseif(array_key_exists('save_and_paginate', $_POST)) {
				
						//User wants to save the current set of pagination
						//@todo How would this work?
						$paginationPage = $this->_getParam('page');
						
						$this->_redirect('exhibits/editPage/'.$page->id.'/'.$paginationPage);
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
		
		return $this->processPageForm($page, $section);
	}
	
	public function deleteSectionAction()
	{
		//Delete the section and re-order the rest of the sections in the exhibit
		
		$section = $this->findById(null,'Section');
		$exhibit = $section->Exhibit;
		
		$section->delete();
		$exhibit->reorderSections();
		
		$this->_redirect('exhibits/edit/'.$exhibit->id);
	}
	
	public function deletePageAction()
	{
		$page = $this->findById(null,'SectionPage');
		$section = $page->Section;
		
		$page->delete();
		$section->reorderPages();
		
		$this->_redirect('exhibits/editSection/'.$section->id);
	}
	
	protected function commitExhibitForm($exhibit) {
		
		if(!empty($_POST)) {
			
			//Whether or not the exhibit is featured
			$exhibit->featured = (bool) $_POST['featured'];
			unset($_POST['featured']);
			
			//Change the order of the sections
			if(array_key_exists('reorder_sections',$_POST)) {
				foreach ($_POST['Sections'] as $key => $section) {
					$exhibit->Sections[$key]->order = $section['order'];
				}
				$exhibit->Sections->save();
			}
			
			//Manipulate a given user's tags
			$current_user = Kea::loggedIn();
			
			$exhibit->applyTagString($_POST['tags'], $current_user->id);
			
			//If the user is a super user, they can remove tags at will
			if($current_user->role == 'super') {
				$deleteTags = true;
				$exhibit->applyTagString($_POST['all_tags'], $current_user->id, $deleteTags);
			}
		}			
		
		$retVal = parent::commitForm($exhibit);
		
		if($retVal) {
			//reload the sections b/c Doctrine is too dumb to do it
			$exhibit->loadSections();
		}
		
		return $retVal;
	}

	
	protected function commitSectionForm($section) {
		
		if(!empty($_POST)) {
			
			$conn = $this->getConn();
		
			//Start the transaction
			$conn->beginTransaction();
		
			try {
				//Fill out the section from the form
				$section->setFromForm($_POST);
			
				//Save it
				$section->save();
			
				//Commit it
				$conn->commit();
			
				return true;
			} catch (Doctrine_Validator_Exception $e) {
			
				//Get the errors
				$this->flash($section->getErrorMsg());
			
				//It's messed up, rollback
				$conn->rollback();
			
				return false;
			} catch (Exception $e) {
			
				//Rollback for all other exceptions too
				$conn->rollback();
			
				//Rethrow the exception so that it can be caught elsewhere (maybe)
				throw $e;
			}
		}
		return false;
	}

	
	/**
	 * Page Form POST will look like:
	 *
	 * Text[1] = 'Text inserted <a href="foobar.com">With HTML</a>'
	 * Item[2] = 35		(integer ID)
	 * Item[3] = 64
	 * Text[3] = 'This is commentary for the Item with ID # 64' 
	 * 
	 * @return void
	 **/
	protected function commitPageForm($page) {
//		Zend::dump( $_POST );exit;
		if(!empty($_POST)) {
			
			$conn = $this->getConn();
			
			//Start the transaction
			$conn->beginTransaction();
			
			try {
				
				if(!empty($_POST['Text'])) {
					//Process the text fields
					foreach ($_POST['Text'] as $key => $text) {
						$ip = $page->ItemsPages[$key];
						$ip->text = $ip->strip($text);
						$ip->order = $key;
					}
				}
				
				if(!empty($_POST['Item'])) {
					//Process the Item fields
					foreach ($_POST['Item'] as $key => $item_id) {
						$ip = $page->ItemsPages[$key];
						$ip->item_id = is_numeric($item_id) ? $item_id : null;
						$ip->order = $key;
					}
				}
				
				$page->save();
				
				$conn->commit();
				
				return true;
			} catch (Doctrine_Validator_Exception $e) {
				$page->gatherErrors($e);
//				Zend::dump( $page->getErrorMsg() );
				
				$conn->rollback();
			} catch (Exception $e) {
				
				$conn->rollback();
				throw $e;
			}
		}
		return false;
	}
} 
?>
