<?php
class SearchController extends Omeka_Controller_Action
{
    public function indexAction()
    {
        $results = $this->_helper->db->getTable('SearchText')->search($this->_getParam('query'));
        $this->view->results = $results;
    }
}
