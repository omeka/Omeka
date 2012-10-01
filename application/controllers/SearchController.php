<?php
class SearchController extends Omeka_Controller_AbstractActionController
{
    public function init()
    {
        $this->_helper->db->setDefaultModelName('SearchText');
    }
    
    public function indexAction()
    {
        // Find the search texts.
        $searchTexts = $this->_helper->db->findBy($this->getAllParams(), 20, 
                                                  $this->getParam('page', 1));
        // Set the record to the results.
        foreach ($searchTexts as $key => $searchText) {
            $searchTexts[$key]['record'] = $this->_helper->db
                                                ->getTable($searchText['record_type'])
                                                ->find($searchText['record_id']);
        }
        $this->view->searchTexts = $searchTexts;
    }
    
    public function settingsAction()
    {
        if (isset($_POST['index_search_text'])) {
            Zend_Registry::get('bootstrap')->getResource('jobs')
                                           ->sendLongRunning('Job_SearchTextIndex');
            $this->_helper->flashMessenger(__('Indexing search text. This may take a while.'), 'success');
        }
    }
}
