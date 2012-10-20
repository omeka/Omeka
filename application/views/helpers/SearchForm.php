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
class Omeka_View_Helper_SearchForm extends Zend_View_Helper_Abstract
{
    protected $_validQueryTypes;
    protected $_validRecordTypes;
    
    /**
     * Set the valid query and record types.
     */
    public function __construct()
    {
        $this->_validQueryTypes = get_search_query_types();
        $this->_validRecordTypes = get_custom_search_record_types();
    }
    
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
        return $this->view->partial(
            'search/search-form.php', 
            array('options'          => $this->_parseOptions($options), 
                  'request'          => $this->_parseRequest(), 
                  'validQueryTypes'  => $this->_validQueryTypes, 
                  'validRecordTypes' => $this->_validRecordTypes)
        );
    }
    
    /**
     * Set default options if not passed to this helper.
     * 
     * @param array $options
     * @return array
     */
    protected function _parseOptions(array $options)
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
        return $options;
    }
    
    /**
     * Set default form values if not passed with the request.
     * 
     * @return array
     */
    protected function _parseRequest()
    {
        $request = array();
        if (isset($_GET['query'])) {
            $request['query'] = $_GET['query'];
        } else {
            $request['query'] = '';
        }
        if (isset($_GET['query_type']) && array_key_exists($_GET['query_type'], $this->_validQueryTypes)) {
            $request['queryType'] = $_GET['query_type'];
        } else {
            $request['queryType'] = 'full_text';
        }
        if (isset($_GET['record_types'])) {
            $request['recordTypes'] = $_GET['record_types'];
        } else {
            $request['recordTypes'] = array_keys($this->_validRecordTypes);
        }
        return $request;
    }
}
