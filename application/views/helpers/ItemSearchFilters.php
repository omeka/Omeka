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
class Omeka_View_Helper_ItemSearchFilters extends Zend_View_Helper_Abstract
{
    /**
     * Get a list of the currently-active filters for item browse/search.
     *
     * @param array $params Optional array of key-value pairs to use instead of
     *  reading the current params from the request.
     * @return string HTML output
     */
    public function itemSearchFilters(array $params = null)
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
            $filter = $key;
            if($value != null) {
                $displayValue = null;
                switch ($key) {
                    case 'type':
                        $filter = 'Item Type';
                        $itemtype = $db->getTable('ItemType')->find($value);
                        $displayValue = $itemtype->name;
                        break;
                    
                    case 'collection':
                        $collection = $db->getTable('Collection')->find($value);
                        $displayValue = strip_formatting(metadata($collection, array('Dublin Core', 'Title')));
                        break;

                    case 'user':
                        $user = $db->getTable('User')->find($value);
                        $displayValue = $user->name;
                        break;

                    case 'public':
                    case 'featured':
                        $displayValue = ($value == 1 ? __('Yes') : $displayValue = __('No'));
                        break;
                        
                    case 'search':
                    case 'tags':
                    case 'range':
                        $displayValue = $value;
                        break;
                }
                if ($displayValue) {
                    $displayArray[$filter] = $displayValue;
                }
            }
        }

        apply_filters('display_search_item_filters', $displayArray, array('request_array' => $requestArray));
        
        // Advanced needs a separate array from $displayValue because it's
        // possible for "Specific Fields" to have multiple values due to 
        // the ability to add fields.
        if(array_key_exists('advanced', $requestArray)) {
            $advancedArray = array();
            foreach ($requestArray['advanced'] as $i => $row) {
                if (!$row['element_id'] || !$row['type']) {
                    continue;
                }
                $elementID = $row['element_id'];
                $elementDb = $db->getTable('Element')->find($elementID);
                $element = $elementDb->name;
                $type = $row['type'];
                $terms = $row['terms'];
                $advancedValue = $element . ' ' . $type;
                if ($terms) {
                    $advancedValue .= ' "' . $terms . '"';
                }
                $advancedArray[$i] = $advancedValue;
            }
        }

        $html = '';
        if (!empty($displayArray) || !empty($advancedArray)) {
            $html .= '<div class="filters">';
            $html .= '<ul id="filter-list">';
            foreach($displayArray as $name => $query) {
                $html .= '<li class="' . $name . '">' . ucfirst($name) . ': ' . $query . '</li>';
            }
            if(!empty($advancedArray)) {
                foreach($advancedArray as $j => $advanced) {
                    $html .= '<li class="advanced">' . $advanced . '</li>';
                }
            }
            $html .= '</ul>';
            $html .= '</div>';
        }
        return $html;
    }
}
