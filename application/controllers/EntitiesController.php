<?php
/**
 * @package Omeka
 **/
require_once 'Entity.php';
require_once 'Omeka/Controller/Action.php';
class EntitiesController extends Omeka_Controller_Action
{
	protected $_redirects = array(
		'merge'=>array('entities/'), 
		'edit'=>array('entities/'), 
		'add'=>array('entities/') );
	
	public function init()
	{
		$this->_modelClass = 'Entity';
	}
	
	public function deleteAction()
	{
		$entity = $this->findById();
		
		//Check the permissions of the user associated with this entity
		$user = $entity->User;
		
		if($user and $user->exists()) {
			//If we are trying to delete the entity that belongs to a super user
			if( ($user->role == 'super') and !$this->isAllowed('deleteSuperUser') ) {
				$this->flash('You are not allowed to delete names that are associated with super users!');
				$this->_redirect('entities/browse');
			}
			
			$current = Omeka::loggedIn();
			
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
						
			if($e->saveForm($_POST)) {
				
				$this->flashSuccess('Successfully added!');
				
				$this->_redirect('add');
			} 
		}
		catch (Omeka_Validator_Exception $e)
		{
			$this->flashValidationErrors($e);
		} catch (Exception $e) {
			$this->flash($e->getMessage());
		}
		
		return $this->_forward('browse', 'Entities');
	}

	public function browseAction()
	{
		require_once 'Person.php';
		require_once 'Institution.php';
		try {
						
			if($type = $this->_getParam('displayType')) {
				if(!in_array(strtolower($type), array('person', 'institution'))) {
					throw new Exception( 'That type does not exist.' );
					$this->_redirect('403');
				}
				
				$type = ucwords(strtolower($type));
							
			}else {
				$type = 'Entity';
			}
					
			$select = new Omeka_Select;
			$db = get_db();
			
			//If we are not allowed to display email addresses, don't pull it from the DB
			if(!$this->isAllowed('displayEmail')) {
				$fields = "e.first_name, e.middle_name, e.last_name, e.institution, e.parent_id, e.type";
			}else {
				$fields = "e.*";
			}
			
			$select->from("{$db->Entity} e", $fields);
			
			
			if($parent = $this->_getParam('parent')) {
				if(!is_numeric($parent)) {
					throw new Exception( 'Parent must be a valid institution or person' );
				}
				$select->where('e.id = ?', $parent );
				
				//Make sure that we use the hierarchical view for this one
				$this->_setParam('hierarchy', true);
				$_GET['hierarchy'] = true;
			}
			
			if($this->_getParam('hierarchy')) {
				$select->where('e.parent_id IS NULL');
			}
			
			$entities = $db->getTable('Entity')->fetchObjects($select);
			
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