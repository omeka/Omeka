<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2015 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Return the site-wide search form.
 * 
 * @package Omeka\View\Helper
 */
class Omeka_View_Helper_SearchForm extends Zend_View_Helper_Abstract
{
    /**
     * Return the site-wide search form.
     * 
     * @param array $options Valid options are as follows:
     * - show_advanced: whether to show the advanced search; default is false.
     * - submit_value: the value of the submit button; default "Submit".
     * - form_attributes: an array containing form tag attributes.
     * @return string The search form markup.
     */
    public function searchForm(array $options = array())
    {
        $validQueryTypes = get_search_query_types();
        $validRecordTypes = get_custom_search_record_types();

        $filters = array(
            'query' => apply_filters('search_form_default_query', ''),
            'query_type' => apply_filters('search_form_default_query_type', 'keyword'),
            'record_types' => apply_filters('search_form_default_record_types',
                array_keys($validRecordTypes))
        );

        if (isset($_GET['submit_search'])) {
            if (isset($_GET['query'])) {
                $filters['query'] = $_GET['query'];
            }
            if (isset($_GET['query_type'])) {
                $filters['query_type'] = $_GET['query_type'];
            }
            if (isset($_GET['record_types'])) {
                $filters['record_types'] = $_GET['record_types'];
            }
        }

        // Set the default flag indicating whether to show the advanced form.
        if (!isset($options['show_advanced'])) {
            $options['show_advanced'] = false;
        }
        
        // Set the default submit value.
        if (!isset($options['submit_value'])) {
            $options['submit_value'] = __('Search');
        }
        
        // Set the default form attributes.
        if (!isset($options['form_attributes'])) {
            $options['form_attributes'] = array();
        }
        if (!isset($options['form_attributes']['action'])) {
            $url = apply_filters('search_form_default_action', url('search'));
            $options['form_attributes']['action'] = $url;
        }
        if (!isset($options['form_attributes']['id'])) {
            $options['form_attributes']['id'] = 'search-form';
        }
        $options['form_attributes']['method'] = 'get';

        $formParams = array(
            'options'      => $options,
            'filters'      => $filters,
            'query_types'  => $validQueryTypes,
            'record_types' => $validRecordTypes
        );

        $form = $this->view->partial('search/search-form.php', $formParams);

        return apply_filters('search_form', $form, $formParams);
    }
}
