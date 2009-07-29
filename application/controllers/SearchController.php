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
     
     /**
      * This shows the advanced search form for all models by going to the correct URI.
      * 
      * This form can be loaded as a partial by calling advanced_search_form().
      * 
      * @return void
      **/
     public function advancedSearchAction()
     {
         // filter the models to search
         $searchModels = array('Item', 'Collection');
         
         // Only show this form as a partial if it's being pulled via XmlHttpRequest
         $this->view->isPartial = $this->getRequest()->isXmlHttpRequest();

         // If this is set to null, use the default search/browse action.
         $this->view->formActionUri = null;

         $this->view->formAttributes = array('id'=>'search');
     }
     
     /**
      * Returns an array with the search results based on the parameters in the request
      * 
      * @return array
      **/
     private function _getSearchResults()
     {
         $request = $this->getRequest();
         $requestParams = $this->_getNonEmptyParams($request->getParams());
         
         $resultPage = $request->get('page') or $resultPage = 1;
          
         // Get the search hits
         $search = Omeka_Context::getInstance()->getSearch();
         $searchIndex = $search->getLuceneIndex();
         
         // initialize the search query
         $searchQuery = new Zend_Search_Lucene_Search_Query_Boolean();
                 
         // if the model is specified, then it is an advanced search         
         if (isset($_GET['model'])) {
             //advanced search             
             if (isset($_GET['search']) && trim($_GET['search']) != '') {
                 $userQuery= Zend_Search_Lucene_Search_QueryParser::parse(trim($_GET['search']));
                 $searchQuery->addSubquery($userQuery, true);
             }
             $rawUserQuery = '';
             // add the advanced search based on the parameters in the request
             $this->_addAdvancedSearchQuery($searchQuery, $requestParams);
         } else {
             // simple search
              $rawUserQuery = trim($_GET['search']);         
              $userQuery= Zend_Search_Lucene_Search_QueryParser::parse($rawUserQuery);
              $searchQuery->addSubquery($userQuery, true);
              // restrict the simple search to models added by the core and plugins
              $this->_addSimpleSearchModelsQuery($searchQuery);
         }         
    
         // This permission check needs to change from Items to a per model basis
         if (!$this->isAllowed('makePublic', 'Items')) {
             $requireIsPublicQuery = $search->getLuceneTermQueryForFieldName(Omeka_Search::FIELD_NAME_IS_PUBLIC, Omeka_Search::FIELD_VALUE_TRUE, false);
             $searchQuery->addSubquery($requireIsPublicQuery, true);
         }

         try {
             $hits = $searchIndex->find($searchQuery);
         } catch (Zend_Search_Lucene_Exception $e) {
             $hits = array();
             // wildcard exception
             $this->flashError('Invalid Lucene search. For using wildcards, at least three characters must precede the wildcard character.');
         }         
         $hitCount = count($hits);
                  
         // Get the hits per page        
         $hitsPerPage = $this->_getHitsPerPage();
         
         // wrap a paginator around the hits
         $paginator = Zend_Paginator::factory($hits);
         $paginator->setCurrentPageNumber($resultPage);
         $paginator->setItemCountPerPage($hitsPerPage);

         return array(
             'search_query' => $rawUserQuery,
             'hits' => $paginator,
             'total_results' => $hitCount, 
             'page' => $resultPage, 
             'per_page' => $hitsPerPage);
     }
     
     /**
      * Adds subquery to the search query to restrict the models to core models and those added by plugins
      * 
      * @param Zend_Search_Lucene_Search_Query_Boolean $searchQuery
      * @param array $requestParams An associative array where the keys are the request param names and the values are their associated values
      * @return void
      **/
     private function _addSimpleSearchModelsQuery($searchQuery)
     {         
         // core models to search
         $coreModelsToSearch = array('Item', 'Collection', 'File');
         
         // add the models to search from the plugins
         $modelsToSearch = apply_filters('default_search_models', $coreModelsToSearch);
         
         // build the query that restricts the search to the models to search
         $modelsToSearchQuery = new Zend_Search_Lucene_Search_Query_Boolean();
         foreach($modelsToSearch as $modelName) {
             $modelsToSearchQuery->addSubquery(Omeka_Search::getLuceneTermQueryForFieldName('model_name', $modelName, true));
         }
         
         $searchQuery->addSubquery($modelsToSearchQuery, true);
     }
     
     /**
      * Adds subqueries to the search query for the advanced search based on the parameters in the request parameters
      * 
      * @param Zend_Search_Lucene_Search_Query_Boolean $searchQuery
      * @param array $requestParams An associative array where the keys are the request param names and the values are their associated values
      * @return void
      **/
     private function _addAdvancedSearchQuery($searchQuery, $requestParams)
     {
         $modelName = $requestParams['model'];
         try {
             if (!class_exists($modelName)) {
                 throw new Exception('Invalid model type for advanced search.');
             }
             $this->getDb()->getTable($modelName)->addAdvancedSearchQueryForLucene($searchQuery, $requestParams);
         } catch (Exception $e) {
             $controller->flash($e->getMessage());
         }
     }
    
     /**
      * Returns the request parameters that have non-empty values
      * 
      * @param array The request parameters
      * @return array The request parameters that have non-empty values
      **/
     private function _getNonEmptyParams($requestParams) 
     {
         $params = array();
         foreach($requestParams as $requestParamName => $requestParamValue) {
            if (trim($requestParamValue) == '') {
                continue;
            }
            $params[$requestParamName] = $requestParamValue;
         }
         return $params;
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