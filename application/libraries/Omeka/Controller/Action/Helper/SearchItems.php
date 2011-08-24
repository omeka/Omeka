<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Action helper for item searching.
 * Glue between the search form and the model's search functions.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Omeka_Controller_Action_Helper_SearchItems extends Zend_Controller_Action_Helper_Abstract
{   
    /**
     * Call the search() method when this helper is called directly.
     *
     * @param array Set of options (passed to search()}.
     * @return array
     */ 
    public function direct($options = array())
    {
        return $this->search($options);
    }
    
    /**
     * Search the items.
     *
     * @todo Does this actually need any options, and if so, what are they?
     * An example of one option would be automatically generating the pagination,
     * or automatically assigning to the items to the view object.
     * 
     * @param array Set of options (currently empty).
     * @return array Keyed array containing the following: 
     *  - 'items': All the Item objects returned by the search.
     *  - 'page': Page # of the results.
     *  - 'per_page': Number of items displayed for the given results.
     *  - 'total_results': Total number of results returned by the search.
     *  - 'total_items': Total number of items in the database (equivalent to # of
     *    items that would be returned by a blank search query).
     */
    public function search($options = array())
    {   
        $request = $this->getRequest();
        $controller = $this->getActionController();
        $itemTable = $this->getFrontController()->getParam('bootstrap')
                          ->getResource('Db')->getTable('Item');
        // Page should be passed as the 'page' parameter or it defaults to 1
        $resultPage = $request->get('page') or $resultPage = 1;
        
        // set default params
        $params = array();
        $params['recent'] = true;
        
        $requestParams = $request->getParams();
        try {            
            foreach($requestParams as $requestParamName => $requestParamValue) {
                if (is_string($requestParamValue) && trim($requestParamValue) == '') {
                    continue;
                }
                switch($requestParamName) {
                    case 'user':
                        //Must be logged in to view items specific to certain users
                        if (!$controller->isAllowed('browse', 'Users')) {
                            throw new Exception( 'May not browse by specific users.' );
                        }
                        if (is_numeric($requestParamValue)) {
                            $params['user'] = $requestParamValue;
                        }
                    break;
                
                    case 'public':
                        $params['public'] = is_true($requestParamValue);
                    break;
                
                    case 'featured':
                        $params['featured'] = is_true($requestParamValue);
                    break;
                
                    case 'collection':
                        $params['collection'] = $requestParamValue;
                    break;
                
                    case 'type':
                        $params['type'] = $requestParamValue;
                    break;
                
                    case 'tag':
                    case 'tags':
                        $params['tags'] = $requestParamValue;
                    break;
                
                    case 'excludeTags':
                        $params['excludeTags'] = $requestParamValue;
                    break;
                
                    case 'recent':
                        if (!is_true($requestParamValue)) {
                            $params['recent'] = false;
                        }
                    break;
                
                    case 'search':
                        $params['search'] = $requestParamValue;
                        //Don't order by recent-ness if we're doing a search
                        unset($params['recent']);
                    break;
                
                    case 'advanced':                    
                        //We need to filter out the empty entries if any were provided
                        foreach ($requestParamValue as $k => $entry) {                    
                            if (empty($entry['element_id']) || empty($entry['type'])) {
                                unset($requestParamValue[$k]);
                            }
                        }
                        if (count($requestParamValue) > 0) {
                            $params['advanced_search'] = $requestParamValue;
                        }
                    break;
                
                    case 'range':
                        $params['range'] = $requestParamValue;
                    break;

                    case 'sort_field':
                        $params['sort_field'] = $requestParamValue;
                    break;

                    case 'sort_dir':
                        $params['sort_dir'] = $requestParamValue;
                    break;

                    case 'random':
                        $params['random'] = is_true($requestParamValue);
                    break;

                    case 'hasImage':
                        $params['hasImage'] = is_true($requestParamValue);
                    break;
                }
            }
        } catch (Exception $e) {
             $controller->flash($e->getMessage());
        }
 
        //Get the item count after other filtering has been applied, which is the total number of items found
        $totalResults = $itemTable->count($params);
        Zend_Registry::set('total_results', $totalResults);                
        
        //Permissions are checked automatically at the SQL level
        $totalItems = $itemTable->count();
        Zend_Registry::set('total_items', $totalItems);
        
        // Now that we are retrieving the actual result set, limit and offset are applied.        
        $itemsPerPage = $this->getItemsPerPage();
        
        //Retrieve the items themselves
        $items = $itemTable->findBy($params, $itemsPerPage, $resultPage);
        
        return array(
            'items'=>$items, 
            'total_results'=>$totalResults, 
            'total_items' => $totalItems, 
            'page' => $resultPage, 
            'per_page' => $itemsPerPage);
    }
    
    /**
     * Retrieve the number of items to display on any given browse page.
     * This can be modified as a query parameter provided that a user is 
     * actually logged in.
     *
     * @return integer
     */
    public function getItemsPerPage()
    {
        //Retrieve the number from the options table
        $options = $this->getFrontController()->getParam('bootstrap')
                          ->getResource('Options');
        
        if (is_admin_theme()) {
            $perPage = (int) $options['per_page_admin'];
        } else {
            $perPage = (int) $options['per_page_public'];
        }
        
        // If users are allowed to modify the # of items displayed per page, 
        // then they can pass the 'per_page' query parameter to change that.        
        if ($this->_actionController->isAllowed('modifyPerPage', 'Items') && ($queryPerPage = $this->getRequest()->get('per_page'))) {
            $perPage = $queryPerPage;
        }     
        
        // We can never show less than one item per page (bombs out).
        if ($perPage < 1) {
            $perPage = 1;
        }
        
        return $perPage;
    }
}
