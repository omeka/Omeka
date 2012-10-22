<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * @package Omeka\View\Helper
 */
abstract class Omeka_View_Helper_AbstractSearch extends Zend_View_Helper_Abstract
{
    protected $_filters;
    protected $_validQueryTypes;
    protected $_validRecordTypes;
    
    /**
     * Set the values needs for children of this class.
     */
    public function __construct()
    {
        // Set the valid query and record types.
        $this->_validQueryTypes = get_search_query_types();
        $this->_validRecordTypes = get_custom_search_record_types();
        
        // Set default form filters if not passed with the request.
        $filters = array();
        if (isset($_GET['query'])) {
            $filters['query'] = $_GET['query'];
        } else {
            $filters['query'] = '';
        }
        if (isset($_GET['query_type']) && array_key_exists($_GET['query_type'], $this->_validQueryTypes)) {
            $filters['query_type'] = $_GET['query_type'];
        } else {
            $filters['query_type'] = 'keyword';
        }
        if (isset($_GET['record_types'])) {
            $filters['record_types'] = $_GET['record_types'];
        } else {
            $filters['record_types'] = array_keys($this->_validRecordTypes);
        }
        $this->_filters = $filters;
    }
}
