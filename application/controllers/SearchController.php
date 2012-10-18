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
    
    /**
     * Return the number of results to display per page.
     * 
     * An authorized user can modify this using the "per_page" query parameter.
     *
     * @return int
     */
    protected function _getBrowseRecordsPerPage()
    {
        // Return the per page if the current user has permission to modify it.
        if ($this->_helper->acl->isAllowed('modifyPerPage', 'Search')) {
            $perPage = (int) $this->getRequest()->get('per_page');
            if ($perPage) {
                return $perPage;
            } 
        }
        if (is_admin_theme()) {
            $perPage = (int) get_option('per_page_admin');
        } else {
            $perPage = (int) get_option('per_page_public');
        }
        return $perPage;
    }
}
