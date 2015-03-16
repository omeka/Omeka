<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2015 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Return a list of search filters in the current request.
 * 
 * @package Omeka\View\Helper
 */
class Omeka_View_Helper_SearchFilters extends Zend_View_Helper_Abstract
{
    /**
     * Return a list of current search filters in use.
     * 
     * @param array $options Valid options are as follows:
     * - id (string): the ID of the filter wrapping div.
     * @return string
     */
    public function searchFilters(array $options = array())
    {
        $validQueryTypes = get_search_query_types();
        $validRecordTypes = get_custom_search_record_types();

        $query = '';
        $queryType = 'keyword';
        $recordTypes = $validRecordTypes;

        if (isset($_GET['query'])) {
            $query = $_GET['query'];
        }

        if (isset($_GET['query_type']) && array_key_exists($_GET['query_type'], $validQueryTypes)) {
            $queryType = $_GET['query_type'];
        }

        if (isset($_GET['record_types'])) {
            $recordTypes = array();
            foreach ($_GET['record_types'] as $recordType) {
                $recordTypes[] = $validRecordTypes[$recordType];
            }
        }

        // Set the default options.
        if (!isset($options['id'])) {
            $options['id'] = 'search-filters';
        }

        return $this->view->partial(
            'search/search-filters.php', 
            array('options'      => $options, 
                  'query'        => $query,
                  'query_type'   => $validQueryTypes[$queryType],
                  'record_types' => $recordTypes)
        );
    }
}
