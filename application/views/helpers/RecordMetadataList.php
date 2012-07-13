<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2009-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @subpackage Omeka_View_Helper
 */

/**
 * View helper for retrieving lists of metadata for any record that
 * uses ActsAsElementText.
 *
 * @package Omeka
 * @subpackage Omeka_View_Helper
 * @copyright Roy Rosenzweig Center for History and New Media, 2009-2010
 */
class Omeka_View_Helper_RecordMetadataList extends Zend_View_Helper_Abstract
{
    const RETURN_HTML = 'html';
    const RETURN_ARRAY = 'array';

    /**
     * The Item object.
     * @var object
     */
    protected $_record;

    /**
     * Flag to indicate whether to show elements that do not have text.
     * @see self::$_emptyElementString
     * @var boolean
     */
    protected $_showEmptyElements = true;

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
     * Get the record metadata list.
     *
     * @param Omeka_Record $record Record to retrieve metadata from.
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
    public function recordMetadataList(Omeka_Record $record, array $options = array())
    {
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
        // Set a default for show_empty_elements based on site setting
        $this->_showEmptyElements = (bool) get_option('show_empty_elements');
        $this->_emptyElementString = __('[no text]');

        // Handle show_empty_elements option
        if (array_key_exists('show_empty_elements', $options)) {
            if (is_string($options['show_empty_elements'])) {
                $this->_emptyElementString = $options['show_empty_elements'];
            } else {
                $this->_showEmptyElements = (bool) $options['show_empty_elements'];
            }
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
     * @param Omeka_Record $record
     * @param array $metadata
     * @return string
     */
    protected function _getFormattedElementTexts($record, $metadata)
    {
        return $this->view->recordMetadata($record, $metadata, array('all' => true));
    }

    /**
     * Output the default format for displaying record metadata.
     * @return void
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
        $this->_loadViewPartial(array(
            'elementsForDisplay' => $elementsForDisplay,
            'record' => $this->_record
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
                ob_start();
                $this->_getOutputAsHtml();
                $output = ob_get_clean();
                return $output;
            case self::RETURN_ARRAY:
                return $this->_getOutputAsArray();
            default:
                throw new Omeka_View_Exception('Invalid return type!');
        }
    }

    /**
     * Load a view partial to display the data.
     *
     * @todo Could pass the name of the partial in as an argument rather than
     * hardcoding it.  That would allow us to use arbitrary partials for
     * purposes such as repackaging the data for RSS/XML or other data formats.
     *
     * @param array $vars
     * @return void
     */
    protected function _loadViewPartial($vars = array())
    {
        common('record-metadata', $vars);
    }
}
