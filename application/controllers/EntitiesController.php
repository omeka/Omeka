<?php
/**
 * @package Omeka
 **/
require_once 'Entity.php';
require_once 'Kea/Controller/Action.php';
class EntitiesController extends Kea_Controller_Action
{
	protected $_redirects = array(
		'merge'=>array('entities/'), 
		'edit'=>array('entities/'), 
		'add'=>array('entities/') );
	
	public function init()
	{
		$this->_table = $this->getTable('Entity');
		$this->_modelClass = 'Entity';
	}
	
	public function deleteAction()
	{
		$entity = $this->findById();
		
		//Check the permissions of the user associated with this entity
		$user = $entity->User;
		
		if($user->exists()) {
			//If we are trying to delete the entity that belongs to a super user
			if( ($user->role == 'super') and !$this->isAllowed('deleteSuperUser') ) {
				$this->flash('You are not allowed to delete names that are associated with super users!');
				$this->_redirect('entities/browse');
			}
			
			$current = Kea::loggedIn();
			
			//We can't delete ourselves
			if( $user->id == $current->id ) {
				$this->flash('You are not allowed to delete yourself!');
				$this->_redirect('entities/browse');
			}
		}
		
		return parent::deleteAction();
	}
	
	
	
	public function addAction()
	{
		$e = new Entity;
		try {
						
			if($e->commitForm($_POST)) {
				
				$this->flash('Successfully added!');
				
				$this->_redirect('add');
			} 
		} catch (Exception $e) {
			$this->flash($e->getMessage());
		}
		
		return $this->_forward('Entities', 'browse');
	}

	public function browseAction()
	{
		require_once 'Person.php';
		require_once 'Institution.php';
		try {
			
			$q = new Doctrine_Query;
			
			if($type = $this->_getParam('displayType')) {
				if(!in_array(strtolower($type), array('person', 'institution'))) {
					throw new Exception( 'That type does not exist.' );
					$this->_redirect('403');
				}
				
				$type = ucwords(strtolower($type));
							
			}else {
				$type = 'Entity';
			}
					
			$q->parseQuery("SELECT e.* FROM $type e");
			
			
			if($parent = $this->_getParam('parent')) {
				if(!is_numeric($parent)) {
					throw new Exception( 'Parent must be a valid institution or person' );
				}
				$q->addWhere('e.id = :parent', compact('parent') );
				
				//Make sure that we use the hierarchical view for this one
				$this->_setParam('hierarchy', true);
				$_GET['hierarchy'] = true;
			}
			
			
			if($this->_getParam('hierarchy')) {
				$q->addWhere('e.parent_id IS NULL');
			}
			
			
			$entities = $q->execute();
			
			if(!$this->isAllowed('displayEmail')) {
				$entities->hideField('email');
			}
		} catch (Exception $e) {
			$this->flash($e->getMessage());
		}
		
		return $this->render('entities/browse.php', compact('entities'));
	}

	public function mergeAction()
	{
		$subjectId = $_POST['merger'];
		$objectId = $_POST['mergee'];
		
		try {
			
			if(!is_numeric($objectId) or !is_numeric($subjectId)) {
				throw new Exception( 'Merge IDs must be numeric' );
			}
			
			if($objectId == $subjectId) {
				throw new Exception( 'Entity may not merge with itself' );
			}
			
			$subject = $this->findById($subjectId);
			$object = $this->findById($objectId);
			
			echo $subject;
			echo $object;
			
			if(!$subject->merge($object)) {
				throw new Exception( 'Merge failed!' );			
			}
			
		} catch (Exception $e) {
			$this->flash($e->getMessage());
		}
		$this->_redirect('merge');
	}
}