<?php
class SearchController extends Omeka_Controller_AbstractActionController
{
    public function init()
    {
        $this->_helper->db->setDefaultModelName('SearchText');
    }
    
    public function indexAction()
    {
        parent::browseAction();
    }
    
    public function settingsAction()
    {
        $this->view->assign('searchRecordTypes', Mixin_Search::getSearchRecordTypes());
        if (isset($_POST['index_search_text'])) {
            Zend_Registry::get('bootstrap')->getResource('jobs')
                                           ->sendLongRunning('Job_SearchTextIndex');
            $this->_helper->flashMessenger(__('Indexing records. This may take a while. You may continue administering your site.'), 'success');
        }
    }
    
    protected function _getBrowseRecordsPerPage()
    {
        return 20;
    }
}
