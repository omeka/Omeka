<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Show the currently-active filters for a search/browse.
 * 
 * @package Omeka\View\Helper
 */
class Omeka_View_Helper_CollectionSearchFilters extends Zend_View_Helper_Abstract
{
    /**
     * Get a list of the currently-active filters for collection browse.
     *
     * @param array $params Optional array of key-value pairs to use instead of
     *  reading the current params from the request.
     * @return string HTML output
     */
    public function collectionSearchFilters(array $params = null)
    {
        if ($params === null) {
            $request = Zend_Controller_Front::getInstance()->getRequest();
            $requestArray = $request->getParams();
        } else {
            $requestArray = $params;
        }

        $db = get_db();
        $displayArray = array();
        foreach ($requestArray as $key => $value) {
            if ($value != null) {
                $filter = ucfirst($key);
                $displayValue = null;
                switch ($key) {
                    case 'public':
                    case 'featured':
                        $displayValue = ($value == 1 ? __('Yes') : $displayValue = __('No'));
                        break;
                }
                if ($displayValue) {
                    $displayArray[$filter] = $displayValue;
                }
            }
        }

        $displayArray = apply_filters('collection_search_filters', $displayArray, array('request_array' => $requestArray));

        $html = '';
        if (!empty($displayArray) || !empty($advancedArray)) {
            $html .= '<div id="collection-filters">';
            $html .= '<ul>';
            foreach ($displayArray as $name => $query) {
                $class = html_escape(strtolower(str_replace(' ', '-', $name)));
                $html .= '<li class="' . $class . '">' . html_escape(__($name)) . ': ' . html_escape($query) . '</li>';
            }
            if (!empty($advancedArray)) {
                foreach ($advancedArray as $j => $advanced) {
                    $html .= '<li class="advanced">' . html_escape($advanced) . '</li>';
                }
            }
            $html .= '</ul>';
            $html .= '</div>';
        }
        return $html;
    }
}