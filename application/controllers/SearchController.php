<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

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
class SearchController extends Omeka_Controller_Action
{
    public $contexts = array(
            'browse' => array('json', 'dcmes-xml', 'rss2'),
            'show'   => array('json', 'dcmes-xml')
    );
    
    public function init() 
    {
    }
    
    /**
      * Browse the items.  Encompasses search, pagination, and filtering of
      * request parameters.  Should perhaps be split into a separate
      * mechanism.
      * 
      * @return void
      **/
     public function browseAction()
     {
         $results = $this->_getSearchResults();
             
         /** 
          * Now process the pagination
          * 
          **/
         $paginationUrl = $this->getRequest()->getBaseUrl() . '/search/browse/';
    
         //Serve up the pagination
         $pagination = array('menu'          => null, 
                             'page'          => $results['page'], 
                             'per_page'      => $results['per_page'], 
                             'total_results' => $results['total_results'], 
                             'link'          => $paginationUrl);
    
         Zend_Registry::set('pagination', $pagination);
    
         fire_plugin_hook('browse_search_hits',  $results['hits']);
    
         $this->view->assign(array('searchQuery' => $results['search_query'], 'hits'=> $results['hits'], 'totalResults'=>$results['total_results']));
     }
     
     private function _getSearchResults()
     {
         $request = $this->getRequest();
         
         $resultPage = $request->get('page') or $resultPage = 1;
          
         // Get the search hits
         $searchIndex = Omeka_Context::getInstance()->getSearch()->getLuceneIndex();
         
         $searchQuery = $_GET['search'];
         
         try {
             $hits = $searchIndex->find($searchQuery);
         } catch (Zend_Search_Lucene_Exception $e) {
             $hits = array();
             // wildcard exception
             $this->flashError('Invalid lucene search. For using wildcards, at least three characters must precede the wildcard character.');
         }         
         $hitCount = count($hits);
                  
         // Get the hits per page        
         $hitsPerPage = $this->_getHitsPerPage();
         
         // wrap a paginator around the hits
         $paginator = Zend_Paginator::factory($hits);
         $paginator->setCurrentPageNumber($resultPage);
         $paginator->setItemCountPerPage($hitsPerPage);

         return array(
             'search_query' => $searchQuery,
             'hits' => $paginator,
             'total_results' => $hitCount, 
             'page' => $resultPage, 
             'per_page' => $hitsPerPage);
     }
     
     /**
      * Retrieve the number of hits to display on any given browse page.
      * This can be modified as a query parameter provided that a user is actually logged in.
      *
      * @return integer
      **/
     public function _getHitsPerPage()
     {
         //Retrieve the number from the options table
         $options = Omeka_Context::getInstance()->getOptions();

         if (is_admin_theme()) {
             $perPage = (int) $options['per_page_admin'];
         } else {
             $perPage = (int) $options['per_page_public'];
         }

         // If users are allowed to modify the # of items displayed per page, 
         // then they can pass the 'per_page' query parameter to change that.        
         if ($this->isAllowed('modifyPerPage', 'Items') && ($queryPerPage = $this->getRequest()->get('per_page'))) {
             $perPage = $queryPerPage;
         }     

         // We can never show less than one item per page (bombs out).
         if ($perPage < 1) {
             $perPage = 1;
         }

         return $perPage;
     }
}