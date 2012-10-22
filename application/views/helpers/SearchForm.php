<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Return the site-wide search form.
 * 
 * @package Omeka\View\Helper
 */
class Omeka_View_Helper_SearchForm extends Omeka_View_Helper_AbstractSearch
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
        
        return $this->view->partial(
            'search/search-form.php', 
            array('options'      => $options, 
                  'filters'      => $this->_filters, 
                  'query_types'  => $this->_validQueryTypes, 
                  'record_types' => $this->_validRecordTypes)
        );
    }
}
