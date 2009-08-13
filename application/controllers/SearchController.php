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
     public function resultsAction()
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
         Zend_Registry::set('total_results', $results['total_results']);
    
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
     public function indexAction()
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
         $allowedModelNames = array();
          
         // Get the search hits
         $search = Omeka_Context::getInstance()->getSearch();
         if ($search && $searchIndex = $search->getLuceneIndex()) {
         
             // initialize the search query
             $searchQuery = new Zend_Search_Lucene_Search_Query_Boolean();
                 
             // if the model is specified, then it is an advanced search         
             if (isset($_GET['model'])) {
                 
                 // set the display query to an empty string       
                 $displayUserQuery = '';
                 
                 // determine the models to search
                 $formModelNames = explode(',', $requestParams['model']);
                 $coreAndPluginModelNames = array_keys($search->getSearchModels());
                 $allowedModelNames = array_intersect($formModelNames, $coreAndPluginModelNames);
                 
                 // add the keyword search           
                 if (isset($_GET['search']) && trim($_GET['search']) != '') {
                     $userQuery= Zend_Search_Lucene_Search_QueryParser::parse(trim($_GET['search']), 'UTF-8');
                     $searchQuery->addSubquery($userQuery, true);
                 }
                                 
                 // add the advanced search based on the parameters in the request
                 $this->_addAdvancedSearchQuery($searchQuery, $requestParams, $allowedModelNames);
                 
                 // make sure the user has permission to view the models in the search results
                 $this->_addPermissionsQuery($searchQuery);             
             } else {

                 // get the display query from the 'search' GET parameter
                 $displayUserQuery = trim($_GET['search']);
                 
                 // create simple search
                 if (isset($_GET['search']) && trim($_GET['search']) != '') {
                      // parse the query supplied by the user
                      $userQuery= Zend_Search_Lucene_Search_QueryParser::parse($displayUserQuery, 'UTF-8');
                      $searchQuery->addSubquery($userQuery, true);
                      
                      //restrict the models to the core models and those added by plugins
                      $allowedModelNames = array_keys($search->getSearchModels());
                      
                      $this->_addSimpleSearchQuery($searchQuery, $allowedModelNames);
                      
                      // make sure the user has permission to view the models in the search results
                      $this->_addPermissionsQuery($searchQuery);
                  }
             }         
                                      
             try {
                 $order = trim($_GET['order']);
                 if ($order == '') {                     
                     $hits = $searchIndex->find($searchQuery);
                 } else {
                     switch($order) {
                         case 'id':
                         default:
                            $sortFieldNameStrings = Omeka_Search::FIELD_NAME_MODEL_ID;
                            $sortType = SORT_NUMERIC;
                            $sortOrder = SORT_DESC;
                         break;
                     }
                     $hits = $search->findLuceneByQueryWithSort($searchQuery, $sortFieldNameStrings, $sortType, $sortOrder);
                 }                 
             } catch (Zend_Search_Lucene_Exception $e) {
                 $hits = array();
                 // wildcard exception
                 $this->flashError('Invalid Lucene search. For using wildcards, at least three characters must precede the wildcard character.');
             }
         } else {
             $hits = array();
         }         
        
         // Get the total number of hits
         $hitCount = count($hits);
                   
         // Get the hits per page        
         $hitsPerPage = $this->_getHitsPerPage();
         
         // wrap a paginator around the hits
         $paginator = Zend_Paginator::factory($hits);
         $paginator->setCurrentPageNumber($resultPage);
         $paginator->setItemCountPerPage($hitsPerPage);

         return array(
             'search_query' => $displayUserQuery,
             'models' => $allowedModelNames,
             'hits' => $paginator,
             'total_results' => $hitCount, 
             'page' => $resultPage, 
             'per_page' => $hitsPerPage);
     }
     
     private function _addPermissionsQuery($searchQuery)
     {
         if ($search = Omeka_Search::getInstance()) {
             $searchModels = $search->getSearchModels();

             // build the query that restricts the search to the models to search
             $acl = get_acl();
             $addsPermission = false;
             $permissionsForModelQuery = new Zend_Search_Lucene_Search_Query_Boolean();
             foreach($searchModels as $modelName => $modelInfo) {

                 // get the resource name for the model
                 $resourceName = trim($modelInfo['resourceName']);
                 $showPrivatePermission = trim($modelInfo['showPrivatePermission']);

                 // if the model specifies a permission for viewing private instances of it
                 // and the user does not have permission to view private instances of it
                 // then restrict the lucene search so it does not include instances of that model
                 if ($resourceName != '' && 
                     $showPrivatePermission != '' && 
                     !$acl->checkUserPermission($resourceName, $showPrivatePermission)) {
                    
                     $addsPermission = true;
                     $permissionForModelQuery = new Zend_Search_Lucene_Search_Query_Boolean();
                     $permissionForModelQuery->addSubquery($search->getLuceneTermQueryForFieldName(Omeka_Search::FIELD_NAME_MODEL_NAME, $modelName), true);
                     $permissionForModelQuery->addSubquery($search->getLuceneTermQueryForFieldName(Omeka_Search::FIELD_NAME_IS_PUBLIC, '1'), true);

                     $permissionsForModelQuery->addSubquery($permissionForModelQuery);
                 }
             }
             if ($addsPermission) {
                 $searchQuery->addSubquery($permissionsForModelQuery, true);
             }
         }
     }
     
     /**
      * Adds subquery to the search query for the simple search
      * 
      * @param Zend_Search_Lucene_Search_Query_Boolean $searchQuery
      * @param array $allowedModelNames
      * @return void
      **/
     private function _addSimpleSearchQuery($searchQuery, $allowedModelNames)
     {         
         if ($search = Omeka_Search::getInstance()) {
             $this->_addRestrictModelsQuery($searchQuery, $allowedModelNames);
         }
     }
     
     /**
      * Adds subquery to the search query to restrict the allowed model names
      * 
      * @param Zend_Search_Lucene_Search_Query_Boolean $searchQuery
      * @param array $requestParams An associative array where the keys are the request param names and the values are their associated values
      * @return void
      **/
     private function _addRestrictModelsQuery($searchQuery, $allowedModelNames)
     {
         if ($search = Omeka_Search::getInstance()) {
             // build the query that restricts the search to the models to search
             $modelsToSearchQuery = new Zend_Search_Lucene_Search_Query_Boolean();
             foreach($allowedModelNames as $modelName) {
                 $modelsToSearchQuery->addSubquery($search->getLuceneTermQueryForFieldName(Omeka_Search::FIELD_NAME_MODEL_NAME, $modelName));
             }
             $searchQuery->addSubquery($modelsToSearchQuery, true);
         }
     }
     
     /**
      * Adds subqueries to the search query for the advanced search based on the parameters in the request parameters
      * 
      * @param Zend_Search_Lucene_Search_Query_Boolean $searchQuery
      * @param array $requestParams An associative array where the keys are the request param names and the values are their associated values
      * @param array $allowedModelNames
      * @return void
      **/
     private function _addAdvancedSearchQuery($searchQuery, $requestParams, $allowedModelNames)
     {
         if ($search = Omeka_Search::getInstance()) {
              // create an advanced search query for all of the models
              $advancedSearchQuery = new Zend_Search_Lucene_Search_Query_Boolean();
              foreach($allowedModelNames as $modelName) {             
                  try {
                      if (!class_exists($modelName)) {
                          throw new Exception('Invalid model type for advanced search.');
                      }
                      $this->getDb()->getTable($modelName)->addAdvancedSearchQueryForLucene($advancedSearchQuery, $requestParams);
                  } catch (Exception $e) {
                      $this->flash($e->getMessage());
                  }
             }
             $searchQuery->addSubquery($advancedSearchQuery, true);

             //restrict the allowed models to the models in the request params, and which are allowed by the core and the plugins       
             $this->_addRestrictModelsQuery($searchQuery, $allowedModelNames);
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