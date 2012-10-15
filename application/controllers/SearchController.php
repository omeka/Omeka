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
        // Customize search record types.
        if (isset($_POST['customize_search_record_types'])) {
            if (isset($_POST['search_record_types'])) {
                $option = serialize($_POST['search_record_types']);
            } else {
                $option = serialize(array());
            }
            set_option('search_record_types', $option);
            $this->_helper->flashMessenger(__('You have changed which records are searchable in Omeka. Please re-index the records using the form below.'), 'success');
        }
        
        // Index the records.
        if (isset($_POST['index_records'])) {
            Zend_Registry::get('bootstrap')->getResource('jobs')
                                           ->sendLongRunning('Job_SearchTextIndex');
            $this->_helper->flashMessenger(__('Indexing records. This may take a while. You may continue administering your site.'), 'success');
        }
        
        $this->view->assign('searchRecordTypes', get_search_record_types());
        $this->view->assign('customSearchRecordTypes', get_custom_search_record_types());
    }
    
    protected function _getBrowseRecordsPerPage()
    {
        return 20;
    }
}
