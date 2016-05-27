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
     * @param array $options Optional options for the search filters. Possible keys:
     * - 'remove_filter' (bool) Enable removing single filter? By default false.
     *    When set to true, you need to pass the key in request params 
     *    that this filter refers to. Example: 
     *    <code>
     *        // GET items/browse?search=&public=1&custom_field=xyz
     *        public function filterItemSearchFilters($displayArray, $args) {
     *            if (!empty($args['request_array']['custom_field'])) {
     *                if (empty($args['options']['remove_filter'])) {
     *                    $displayArray['Custom Label'] = $args['request_array']['custom_field'];
     *                } else {
     *                    $displayArray['Custom Label'] = array(
     *                        'key'   => 'custom_field',
     *                        'value' => $args['request_array']['custom_field']
     *                    );
     *                }
     *            }
     *            return $displayArray;
     *        }
     *    </code>
     * @return string HTML output
     */
    public function itemSearchFilters(array $params = null, $options = array())
    {
        if ($params === null) {
            $request = Zend_Controller_Front::getInstance()->getRequest(); 
            $requestArray = $request->getParams();
            // TODO - here I would rather use $requestArray = $request->getQuery();
            // because $request->getParams(); contains also module, controller, action keys
        } else {
            $requestArray = $params;
        }
        $options = array_merge(array(
            'remove_filter' => false
        ), $options);
        
        $db = get_db();
        $displayArray = array();
        foreach ($requestArray as $key => $value) {
            if($value != null) {
                $filter = ucfirst($key);
                $displayValue = null;
                switch ($key) {
                    case 'type':
                        $filter = 'Item Type';
                        $itemType = $db->getTable('ItemType')->find($value);
                        if ($itemType) {
                            $displayValue = $itemType->name;
                        }
                        break;
                    
                    case 'collection':
                        $collection = $db->getTable('Collection')->find($value);
                        if ($collection) {
                            $displayValue = strip_formatting(
                                metadata(
                                    $collection,
                                    array('Dublin Core', 'Title'),
                                    array('no_escape' => true)
                                )
                            );
                        }
                        break;

                    case 'user':
                        $user = $db->getTable('User')->find($value);
                        if ($user) {
                            $displayValue = $user->name;
                        }
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
                    if ($options['remove_filter']) {
                        // pass the query param key, so we know which part should be removed
                        $displayArray[$filter] = array(
                            'key'   => $key,
                            'value' => $displayValue,
                        );
                    } else {
                        $displayArray[$filter] = $displayValue;
                    }
                }
            }
        }

        $displayArray = apply_filters('item_search_filters', $displayArray, array('request_array' => $requestArray, 'options' => $options));
        
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
                $element = __($elementDb->name);
                $type = __($row['type']);
                $advancedValue = $element . ' ' . $type;
                if (isset($row['terms'])) {
                    $advancedValue .= ' "' . $row['terms'] . '"';
                }
                if ($options['remove_filter']) {
                    // pass the query param index, so we know which part should be removed
                    $advancedArray[$i] = array(
                        'key'   => $i,
                        'value' => $advancedValue,
                    );
                } else {
                    $advancedArray[$i] = $advancedValue;
                }
            }
        }

        return $this->view->partial(
            'items/search-filters.php',
            compact('displayArray', 'advancedArray', 'requestArray', 'options')
        );
    }
}
