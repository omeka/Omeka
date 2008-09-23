<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * @see Entity.php
 */ 
require_once 'Entity.php';

/**
 * @see Omeka_Controller_Action
 */
require_once 'Omeka/Controller/Action.php';

/**
 * @package Omeka
 * @subpackage Controllers
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class EntitiesController extends Omeka_Controller_Action
{
    public function init()
    {
        $this->_modelClass = 'Entity';
    }
    
    /**
     * Wraps the CRUD delete action with permissions checks.
     * 
     * @todo Separate these permissions checks into separate Zend_Acl_Assert
     * class(es) that can determine whether or not deleting is OK based on the
     * currently retrieved User object
     * @return void
     **/
    public function deleteAction()
    {
        $entity = $this->findById();
        
        //Check the permissions of the user associated with this entity
        $user = $entity->User;
        
        if ($user and $user->exists()) {
            
            //If we are trying to delete the entity that belongs to a super user
            if (($user->role == 'super') && !$this->isAllowed('deleteSuperUser')) {
                $this->flash('You are not allowed to delete names that are associated with super users!');
                $this->redirect->goto('browse');
            }
            
            $current = $this->getCurrentUser();
            
            //We can't delete ourselves
            if ($user->id == $current->id) {
                $this->flash('You are not allowed to delete yourself!');
                $this->redirect->goto('browse');
            }
        }
        
        return parent::deleteAction();
    }
    
    public function addAction()
    {
        $e = new Entity;
        
        try {
            if ($e->saveForm($_POST)) {
                $this->flashSuccess('Successfully added!');
                $this->redirect->goto('browse');
            } 
        } catch (Omeka_Validator_Exception $e) {
            $this->flashValidationErrors($e);
        } catch (Exception $e) {
            $this->flash($e->getMessage());
        }
        
        return $this->_forward('browse', 'Entities');
    }
    
    public function browseAction()
    {
        $options = array();
        
        if ($type = $this->_getParam('displayType')) {
            if (!in_array(strtolower($type), array('person', 'institution'))) {
                $this->redirect->goto('forbidden');
            }
            $options['type'] = ucwords(strtolower($type));
        }
        
        //Whether or not to retrieve the email addresses for display
        if ($this->isAllowed('displayEmail')) {
            $options['get_email'] = true;
        }
        
        if ($parentId = $this->_getParam('parent')) {
            $options['parent_id'] = (int) $parentId;
        }
        
        $entities = $this->getTable()->findBy($options);
                    
        $this->view->assign(compact('entities'));
    }

    public function mergeAction()
    {
        $subjectId = $_POST['merger'];
        $objectId  = $_POST['mergee'];
        
        try {
            if (!is_numeric($objectId) || !is_numeric($subjectId)) {
                throw new Exception('Merge IDs must be numeric');
            }
            
            if ($objectId == $subjectId) {
                throw new Exception('Entity may not merge with itself');
            }
            
            $subject = $this->findById($subjectId);
            $object  = $this->findById($objectId);
            
            if (!$subject->merge($object)) {
                throw new Exception( 'Merge failed!' );            
            }
            
        } catch (Exception $e) {
            $this->flash($e->getMessage());
        }
        $this->redirect->goto('browse');;
    }
}