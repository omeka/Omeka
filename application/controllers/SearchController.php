<?php
class SearchController extends Omeka_Controller_Action
{
    public function indexAction()
    {
        $results = $this->_helper->db->getTable('SearchText')->search($this->_getParam('query'));
        $this->view->results = $results;
    }
    
    public function settingsAction()
    {
        if (isset($_POST['index_search_text'])) {
            Zend_Registry::get('bootstrap')->getResource('jobs')
                                           ->sendLongRunning('SearchText_IndexJob');
            $this->_helper->flashMessenger(__('Indexing search text. This may take a while.'), 'success');
        }
    }
}
