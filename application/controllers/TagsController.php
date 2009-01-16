<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * @see Tag.php
 */
require_once 'Tag.php';

/**
 * @see Omeka_Controller_Action
 **/
require_once 'Omeka/Controller/Action.php';

/**
 * @package Omeka
 * @subpackage Controllers
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
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
            
            $tags = $this->getTagsforAdministration();
            
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
                $this->flashSuccess("Tag named '{$tag->name}' was successfully deleted.");
            }
            
            $tags = $this->getTagsForAdministration();
            $this->view->assign(compact('tags'));
        }
    }
    
    protected function getTagsForAdministration()
    {
        $user = $this->getCurrentUser();
        
        if (!$user) {
            throw new Exception( 'You have to be logged in to edit tags!' );
        }
        
        $criteria = array('sort' => 'most');
        
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
        $newTags = explode(',', $_POST['new_tag']);
        foreach ($newTags as $k => $t) {
            $newTags[$k] = trim($t);
        }
        $newTags = array_diff($newTags, array(''));
        
        $oldTag = $this->_table->find($oldTagId);
        
        $oldName = $oldTag->name;
        $newNames = $_POST['new_tag'];
        
        if ($this->isAllowed('edit')) {
            $oldTag->rename($newTags);
        } else {
            $oldTag->rename($newTags, $user->id);
        }
        
        $this->flash("Tag named '$oldName' was successfully renamed to '$newNames'.");
    }
    
    /**
     *
     * @return void
     **/
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
            throw new Exception('Invalid tagType given');
        }
        
        if($record = $this->_getParam('record')) {
            $filter['record'] = $record;
        }
        
        //For the count, we only need to check based on permission levels
        $count_params = array_merge($perms, array('recent' => false, 
                                                  'type' => $for));
        
        $total_tags = $this->_table->count($count_params);
        
        $tags = $this->_table->findBy(array_merge($params, $perms, array('type' => $for)), $params['limit']);
        $total_results = count($tags);
        
        Zend_Registry::set('total_tags', $total_tags);
        Zend_Registry::set('total_results', $total_results);    
        
        //Plugin hook
        fire_plugin_hook('browse_tags',  $tags, $for);
        
        $browse_for = $for;
        
        $this->view->assign(compact('tags', 'total_tags', 'browse_for'));
    }
    
    public function autocompleteAction()
    {
        $tagText = $this->_getParam('tag_start');
        if (empty($tagText)) {
            echo '<ul></ul>';exit;
        }
        $tagNames = $this->getTable()->findTagNamesLike($tagText);
        echo '<ul>';
        foreach ($tagNames as $tag) {
            echo '<li>' . $tag . '</li>';
        }
        echo '</ul>';
        // Skip all the post-processing, we need this to be fast.
        exit;
    }
}