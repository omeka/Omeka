<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * View helper for retrieving lists of metadata for any record that uses 
 * Mixin_ElementText.
 * 
 * @package Omeka\View\Helper
 */
class Omeka_View_Helper_AllElementTexts extends Zend_View_Helper_Abstract
{
    const RETURN_HTML = 'html';
    const RETURN_ARRAY = 'array';

    /**
     * The record being printed.
     * @var Omeka_Record_AbstractRecord
     */
    protected $_record;

    /**
     * Flag to indicate whether to show elements that do not have text.
     * @see self::$_emptyElementString
     * @var boolean
     */
    protected $_showEmptyElements = true;

    /**
     * Whether to include a heading for each Element Set.
     * @var boolean
     */
    protected $_showElementSetHeadings = true;

    /**
     * String to display if elements without text are shown.
     * @see self::$_showEmptyElements
     * @var string
     */
    protected $_emptyElementString;

    /**
     * Element sets to list.
     *
     * @var array
     */
    protected $_elementSetsToShow = array();

    /**
     * Type of data to return.
     *
     * @var string
     */
    protected $_returnType = self::RETURN_HTML;

    /**
     * Path for the view partial.
     *
     * @var string
     */
    protected $_partial = 'common/record-metadata.php';

    /**
     * Get the record metadata list.
     *
     * @param Omeka_Record_AbstractRecord|string $record Record to retrieve
     *  metadata from.
     * @param array $options
     *  Available options:
     *  - show_empty_elements' => bool|string Whether to show elements that
     *    do not contain text. A string will set self::$_showEmptyElements to
     *    true and set self::$_emptyElementString to the provided string.
     *  - 'show_element_sets' => array List of names of element sets to display.
     *  - 'return_type' => string 'array', 'html'.  Defaults to 'html'.
     * @since 1.0 Added 'show_element_sets' and 'return_type' options.
     * @return string|array
     */
    public function allElementTexts($record, array $options = array())
    {
        if (is_string($record)) {
            $record = $this->view->{$this->view->singularize($record)};
        }

        if (!($record instanceof Omeka_Record_AbstractRecord)) {
            throw new InvalidArgumentException('Invalid record passed to recordMetadata.');
        }
        
        $this->_record = $record;
        $this->_setOptions($options);
        return $this->_getOutput();
    }

    /**
     * Set the options.
     *
     * @param array $options
     * @return void
     */
    protected function _setOptions(array $options)
    {
        // Set default options based on site settings
        $this->_showEmptyElements = (bool) get_option('show_empty_elements');
        $this->_showElementSetHeadings = (bool) get_option('show_element_set_headings');
        $this->_emptyElementString = __('[no text]');

        // Handle show_empty_elements option
        if (array_key_exists('show_empty_elements', $options)) {
            if (is_string($options['show_empty_elements'])) {
                $this->_emptyElementString = $options['show_empty_elements'];
            } else {
                $this->_showEmptyElements = (bool) $options['show_empty_elements'];
            }
        }

        if (array_key_exists('show_element_set_headings', $options)) {
            $this->_showElementSetHeadings = (bool) $options['show_element_set_headings'];
        }

        if (array_key_exists('show_element_sets', $options)) {
            $namesOfElementSetsToShow = $options['show_element_sets'];
            if (is_string($namesOfElementSetsToShow)) {
                $this->_elementSetsToShow = array_map('trim', explode(',', $namesOfElementSetsToShow));
            } else {
                $this->_elementSetsToShow = $namesOfElementSetsToShow;
            }
        }

        if (array_key_exists('return_type', $options)) {
            $this->_returnType = (string)$options['return_type'];
        }

        if (array_key_exists('partial', $options)) {
            $this->_partial = (string)$options['partial'];
        }

    }

    /**
     * Get an array of all element sets containing their respective elements.
     *
     * @uses Item::getAllElements()
     * @uses Item::getItemTypeElements()
     * @return array
     */
    protected function _getElementsBySet()
    {
        $elementsBySet = $this->_record->getAllElements();

        // Only show the element sets that are passed in as options.
        if (!empty($this->_elementSetsToShow)) {
            $elementsBySet = array_intersect_key($elementsBySet, array_flip($this->_elementSetsToShow));
        }

        $elementsBySet = $this->_filterItemTypeElements($elementsBySet);

        return apply_filters('display_elements', $elementsBySet);
    }

    /**
     * Filter the display of the Item Type element set, if present.
     *
     * @param array $elementsBySet
     * @return array
     */
    protected function _filterItemTypeElements($elementsBySet)
    {
        if ($this->_record instanceof Item) {
            if ($this->_record->item_type_id) {
                // Overwrite elements assigned to the item type element set with only
                // those that belong to this item's particular item type. This is
                // necessary because, otherwise, all item type elements will be shown.
                $itemTypeElementSetName = $this->_record->getProperty('item_type_name') . ' ' . ElementSet::ITEM_TYPE_NAME;
                
                // Check to see if either the generic or specific Item Type element
                // set has been chosen, i.e. 'Item Type Metadata' or 'Document
                // Item Type Metadata', etc.

                $itemTypeElements = $this->_record->getItemTypeElements();
                
                if (!empty($this->_elementSetsToShow)) {
                    if (in_array($itemTypeElementSetName, $this->_elementSetsToShow) or
                    in_array(ElementSet::ITEM_TYPE_NAME, $this->_elementSetsToShow)) {
                        $elementsBySet[$itemTypeElementSetName] = $itemTypeElements;
                    }
                }
                else {
                    $elementsBySet[$itemTypeElementSetName] = $itemTypeElements;
                }
            }

            // Unset the existing 'Item Type' element set b/c it shows elements
            // for all item types.
            unset($elementsBySet[ElementSet::ITEM_TYPE_NAME]);
        }

        return $elementsBySet;
    }

    /**
     * Determine if an element is allowed to be shown.
     *
     * @param Element $element
     * @param array $texts
     * @return boolean
     */
    protected function _elementIsShowable(Element $element, $texts)
    {
        return $this->_showEmptyElements || !empty($texts);
    }

    /**
     * Return a formatted version of all the texts for the requested element.
     *
     * @param Omeka_Record_AbstractRecord $record
     * @param array $metadata
     * @return array
     */
    protected function _getFormattedElementTexts($record, $metadata)
    {
        return $this->view->metadata($record, $metadata, array('all' => true));
    }

    /**
     * Output the default HTML format for displaying record metadata.
     * @return string
     */
    protected function _getOutputAsHtml()
    {
        // Prepare the metadata for display on the partial.  There should be no
        // need for method calls by default in the view partial.
        $elementSets = $this->_getElementsBySet();
        $emptyString = html_escape(__($this->_emptyElementString));
        $elementsForDisplay = array();
        foreach ($elementSets as $setName => $elementsInSet) {
            $setInfo = array();
            foreach ($elementsInSet as $elementName => $element) {
                $elementTexts = $this->_getFormattedElementTexts(
                    $this->_record, array($element->set_name, $element->name)
                );
                if (!$this->_elementIsShowable($element, $elementTexts)) {
                    continue;
                }

                $displayInfo = array();
                $displayInfo['element'] = $element;
                if (empty($elementTexts)) {
                    $displayInfo['texts'] = array($emptyString);
                } else {
                    $displayInfo['texts'] = $elementTexts;
                }

                $setInfo[$elementName] = $displayInfo; 
            }
            if (!empty($setInfo)) {
                $elementsForDisplay[$setName] = $setInfo;
            }
        }
        // We're done preparing the data for display, so display it.
        return $this->_loadViewPartial(array(
            'elementsForDisplay' => $elementsForDisplay,
            'record' => $this->_record,
            'showElementSetHeadings' => $this->_showElementSetHeadings
        ));
    }

    /**
     * Get the metadata list as a PHP array.
     *
     * @return array
     */
    protected function _getOutputAsArray()
    {
        $elementSets = $this->_getElementsBySet();
        $outputArray = array();
        foreach ($elementSets as $setName => $elementsInSet) {
            $outputArray[$setName] = array();
            foreach ($elementsInSet as $key => $element) {
                $elementName = $element->name;                
                $textArray = $this->_getFormattedElementTexts($this->_record, array($element->set_name, $elementName));
                if (!empty($textArray[0]) or $this->_showEmptyElements) {
                    $outputArray[$setName][$elementName] = $textArray;
                }
            }
        }
        return $outputArray;
    }

    /**
     * Get the metadata list.
     *
     * @return string|array
     */
    protected function _getOutput()
    {
        switch ($this->_returnType) {
            case self::RETURN_HTML:
                return $this->_getOutputAsHtml();
            case self::RETURN_ARRAY:
                return $this->_getOutputAsArray();
            default:
                throw new Omeka_View_Exception('Invalid return type!');
        }
    }

    /**
     * Load a view partial to display the data.
     *
     * @param array $vars Variables to pass to the partial.
     * @return string
     */
    protected function _loadViewPartial($vars = array())
    {
        return $this->view->partial($this->_partial, $vars);
    }
}
