<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @subpackage Controllers
 * @author CHNM
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class TagsController extends Omeka_Controller_Action
{
    public function init()
    {
        $this->_modelClass = 'Tag';
    }
    
    public function editAction()
    {
        if ($user = $this->getCurrentUser()) {
            
            if (!empty($_POST)) {
                $this->editTags($user);
            }
            
            $tags = $this->getTagsForAdministration();
            
            $this->view->assign(compact('tags'));
        }
    }
    
    public function deleteAction()
    {
        if ($user = $this->getCurrentUser()) {
            if (!empty($_POST)) {
                
                $tag_id = $_POST['delete_tag'];
                $tag = $this->_table->find($tag_id);
                
                if ($this->isAllowed('remove')) {
                    $tag->delete();
                } else {
                    $tag->deleteForEntity($user->Entity);
                }
                $this->flashSuccess(__("Tag named '%s' was successfully deleted.", $tag->name));
            }
            
            $tags = $this->getTagsForAdministration();
            $this->view->assign(compact('tags'));
        }
    }
    
    protected function getTagsForAdministration()
    {
        $user = $this->getCurrentUser();
        
        if (!$user) {
            throw new Exception( __('You have to be logged in to edit tags!') );
        }
        
        $criteria = array('sort' => 'alpha');
        
        //Having 'rename' permissions really means that user can rename everyone's tags
        if(!$this->isAllowed('rename')) {
            $criteria['user'] = $user->id;
        }
        
        $tags = $this->_table->findBy($criteria);
        
        return $tags;    
    }
    
    protected function editTags($user)
    {
        $oldTagId = $_POST['old_tag'];
        
        //Explode and sanitize the new tags
        $newTags = explode(get_option('tag_delimiter'), $_POST['new_tag']);
        foreach ($newTags as $k => $t) {
            $newTags[$k] = trim($t);
        }
        $newTags = array_diff($newTags, array(''));
        
        $oldTag = $this->_table->find($oldTagId);
        
        $oldName = $oldTag->name;
        $newNames = $_POST['new_tag'];
        
        try {
            if ($this->isAllowed('edit')) {
                $oldTag->rename($newTags);
            } else {
                $oldTag->rename($newTags, $user->id);
            }
            $this->flashSuccess(__('Tag named "%1$s" was successfully renamed to "%2$s".', $oldName, $newNames));
        } catch (Omeka_Validator_Exception $e) {
            $this->flashValidationErrors($e);
        } catch(Exception $e) {
            $this->flashError($e->getMessage());
        }
    }
    
    /**
     *
     * @return void
     */
    public function browseAction()
    {
        $params = $this->_getAllParams();
        $perms = array();
        
        //Check to see whether it will be tags for exhibits or for items
        //Default is Item
        if (isset($params['tagType'])) {
            $for = $params['tagType'];
            unset($params['tagType']);
        } else {
            $for = 'Item';
        }
        //Since tagType must correspond to a valid classname, this will barf an error on Injection attempts
        if (!class_exists($for)) {
            throw new Exception(__('Invalid tagType given'));
        }
        
        if($record = $this->_getParam('record')) {
            $filter['record'] = $record;
        }
        
        //For the count, we only need to check based on permission levels
        $count_params = array_merge($perms, array('recent' => false, 
                                                  'type' => $for));
        
        $total_tags = $this->_table->count($count_params);
           
        $findByParams = array_merge(array('sort' => 'alpha'), 
                                    $params, 
                                    $perms, 
                                    array('type' => $for));

        $limit = isset($params['limit']) ? $params['limit'] : null;
        $tags = $this->_table->findBy($findByParams, $limit);
        $total_results = count($tags);
        
        Zend_Registry::set('total_tags', $total_tags);
        Zend_Registry::set('total_results', $total_results);    
        
        //Plugin hook
        fire_plugin_hook('browse_tags',  $tags, $for);
        
        $browse_for = $for;
        $sort = $findByParams['sort'];
        
        $this->view->assign(compact('tags', 'total_tags', 'browse_for', 'sort'));
    }
    
    public function autocompleteAction()
    {
        $tagText = $this->_getParam('term');
        if (empty($tagText)) {
            $this->_helper->json(array());
        }
        $tagNames = $this->getTable()->findTagNamesLike($tagText);
        $this->_helper->json($tagNames);
    }
}
