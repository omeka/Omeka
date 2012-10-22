<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Return a list of search filters in the current request.
 * 
 * @package Omeka\View\Helper
 */
class Omeka_View_Helper_SearchFilters extends Omeka_View_Helper_AbstractSearch
{
    /**
     * Return a list of current search filters in use.
     * 
     * @uses Omeka_View_Helper_SearchFilters::searchFilters()
     * @param array $options Valid options are as follows:
     * - id (string): the ID of the filter wrapping div.
     * @return string
     */
    public function searchFilters(array $options = array())
    {
        // Set the default options.
        if (!isset($options['id'])) {
            $options['id'] = 'search-filters';
        }
        
        // Set the record types.
        $recordTypes = array();
        foreach ($this->_filters['record_types'] as $recordType) {
            $recordTypes[] = $this->_validRecordTypes[$recordType];
        }
        
        return $this->view->partial(
            'search/search-filters.php', 
            array('options'      => $options, 
                  'query'        => $this->_filters['query'], 
                  'query_type'   => $this->_validQueryTypes[$this->_filters['query_type']], 
                  'record_types' => $recordTypes)
        );
    }
}
