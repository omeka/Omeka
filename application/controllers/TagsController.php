<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * @package Omeka\Controller
 */
class TagsController extends Omeka_Controller_AbstractActionController
{
    public function init()
    {
        $this->_helper->db->setDefaultModelName('Tag');
    }

    public function addAction()
    {
        $this->_helper->redirector('');
    }

    public function editAction()
    {
        $this->_helper->redirector('');
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
            throw new InvalidArgumentException(__('Invalid tagType given.'));
        }
        
        if($record = $this->_getParam('record')) {
            $filter['record'] = $record;
        }
        
        //For the count, we only need to check based on permission levels
        $count_params = array_merge($perms, array('type' => $for));
        
        $total_tags = $this->_helper->db->count($count_params);
           
        $findByParams = array_merge(array('sort_field' => 'name'), 
                                    $params, 
                                    $perms, 
                                    array('type' => $for));

        $limit = isset($params['limit']) ? $params['limit'] : null;
        $tags = $this->_helper->db->findBy($findByParams, $limit);
        $total_results = count($tags);
        
        Zend_Registry::set('total_tags', $total_tags);
        Zend_Registry::set('total_results', $total_results);    
        
        $browse_for = $for;
        $sort = array_intersect_key($findByParams, array('sort_field' => '', 'sort_dir' => ''));

        //dig up the record types for filtering
        $db = get_db();
        $sql = "SELECT DISTINCT record_type FROM `$db->RecordsTag`";
        $record_types = array_keys($db->fetchAssoc($sql));
        foreach($record_types as $index => $record_type) {
            if(!class_exists($record_type)) {
                unset($record_types[$index]);
            }
        }

        $csrf = new Omeka_Form_Element_SessionCsrfToken('csrf_token');
        $this->view->csrfToken = $csrf->getToken();
        $this->view->record_types = $record_types;
        $this->view->assign(compact('tags', 'total_tags', 'browse_for', 'sort'));
    }
    
    public function autocompleteAction()
    {
        $tagText = $this->_getParam('term');
        if (empty($tagText)) {
            $this->_helper->json(array());
        }
        $tagNames = $this->_helper->db->getTable()->findTagNamesLike($tagText);
        $this->_helper->json($tagNames);
    }
    
    public function renameAjaxAction()
    {
        $csrf = new Omeka_Form_SessionCsrf;
        $oldTagId = $_POST['id'];
        $oldTag = $this->_helper->db->findById($oldTagId);
        $oldName = $oldTag->name;
        $newName = trim($_POST['value']);

        $oldTag->name = $newName;
        $this->_helper->viewRenderer->setNoRender();
        if ($csrf->isValid($_POST) && $oldTag->save(false)) {
            $this->getResponse()->setBody($newName);
        } else {
            $this->getResponse()->setHttpResponseCode(500);
            $this->getResponse()->setBody($oldName);
        }
    }
}
