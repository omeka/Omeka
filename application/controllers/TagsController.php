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
    protected $_browseRecordsPerPage = 100;

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
     * Browse, filter and search tags
     */
    public function browseAction()
    {
        //Check to see whether it will be tags for exhibits or for items
        //Default is All
        if ($type = $this->getParam('type')) {
            $browse_for = $type;
            //Since tag type must correspond to a valid classname, this will barf an error on Injection attempts
            if (!class_exists($browse_for)) {
                throw new InvalidArgumentException(__('Invalid tagType given.'));
            }
            // for specific record_type we want only tags with at least 1 relation
            $this->setParam('include_zero', 0);
        } else {
            $browse_for = 'All';
            $this->setParam('type', null);
            // by default include all tags, even without any relations to records
            $this->setParam('include_zero', 1);
        }

        parent::browseAction();

        // get all params after the parent::browseAction() added some defaults, like sorting
        $params = $this->getAllParams();
        unset($params['admin'], $params['module'], $params['controller'], $params['action'], $params['include_zero']);

        $sort = array(
            'sort_field' => $this->getParam('sort_field'),
            'sort_dir' => $this->getParam('sort_dir', 'a'),
        );

        //dig up the record types for filtering
        $db = get_db();
        $sql = "SELECT DISTINCT record_type FROM `$db->RecordsTag`";
        $record_types = array_keys($db->fetchAssoc($sql));
        foreach ($record_types as $index => $record_type) {
            if (!class_exists($record_type)) {
                unset($record_types[$index]);
            }
        }

        $csrf = new Omeka_Form_Element_SessionCsrfToken('csrf_token');
        $this->view->csrfToken = $csrf->getToken();
        $this->view->record_types = $record_types;
        $this->view->assign(compact('browse_for', 'sort', 'params'));
    }

    /**
     * Return the default sorting parameters to use when none are specified.
     *
     * @return array|null Array of parameters, with the first element being the
     *  sort_field parameter, and the second (optionally) the sort_dir.
     */
    protected function _getBrowseDefaultSort()
    {
        return array('name', 'a');
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
        $oldTagId = $_POST['pk'];
        $oldTag = $this->_helper->db->findById($oldTagId);
        $oldName = $oldTag->name;
        $newName = trim($_POST['value']);
        $error = __('Error occurred.');

        $oldTag->name = $newName;
        $this->_helper->viewRenderer->setNoRender();
        if ($csrf->isValid($_POST) && $oldTag->save(false)) {
            $this->getResponse()->setBody($newName);
        } else {
            $this->getResponse()->setHttpResponseCode(500);
            $this->getResponse()->setBody($error);
        }
    }
}
