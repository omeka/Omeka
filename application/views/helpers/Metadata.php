<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Helper used to retrieve record metadata for for display.
 * 
 * @package Omeka\View\Helper
 */
class Omeka_View_Helper_Metadata extends Zend_View_Helper_Abstract
{
    const SNIPPET = 'snippet';
    const INDEX = 'index';
    const ALL = 'all';
    const NO_ESCAPE = 'no_escape';
    const NO_FILTER = 'no_filter';
    const DELIMITER = 'delimiter';

    /**
     * Retrieve a specific piece of a record's metadata for display.
     *
     * @param Omeka_Record_AbstractRecord $record Database record representing 
     * the item from which to retrieve field data.
     * @param string|array $metadata The metadata field to retrieve.
     *  If a string, refers to a property of the record itself.
     *  If an array, refers to an Element: the first entry is the set name,
     *  the second is the element name.
     * @param array|string|integer $options Options for formatting the metadata
     * for display.
     * - Array options:
     *   - 'all': If true, return an array containing all values for the field.
     *   - 'delimiter': Return the entire set of metadata as a string, where
     *     entries are separated by the given delimiter.
     *   - 'index': Return the metadata entry at the given zero-based index.
     *   - 'no_escape' => If true, do not escape the resulting values for HTML
     *     entities.
     *   - 'no_filter': If true, return the set of metadata without
     *     running any of the filters.
     *   - 'snippet': Trim the length of each piece of text to the given
     *     length in characters.
     * - Passing simply the string 'all' is equivalent to array('all' => true)
     * - Passing simply an integer is equivalent to array('index' => [the integer])
     * @return string|array|null Null if field does not exist for item. Array
     * if certain options are passed.  String otherwise.
     */
    public function metadata($record, $metadata, $options = array())
    {
        if (is_string($record)) {
            $record = $this->view->getCurrentRecord($record);
        }

        if (!($record instanceof Omeka_Record_AbstractRecord)) {
            throw new InvalidArgumentException('Invalid record passed to recordMetadata.');
        }

        // Convert the shortcuts for the options into a proper array.
        $options = $this->_getOptions($options);

        $snippet = isset($options[self::SNIPPET]) ? (int) $options[self::SNIPPET] : false;
        $escape = empty($options[self::NO_ESCAPE]);
        $filter = empty($options[self::NO_FILTER]);
        $all = isset($options[self::ALL]) && $options[self::ALL];
        $delimiter = isset($options[self::DELIMITER]) ? (string) $options[self::DELIMITER] : false;
        $index = isset($options[self::INDEX]) ? (int) $options[self::INDEX] : 0;

        $text = $this->_getText($record, $metadata);

        if (is_array($text)) {
            // If $all or $delimiter isn't specified, pare the array down to
            // just one entry, otherwise we need to work on the whole thing
            if ($all || $delimiter) {
                foreach ($text as $key => $value) {
                    $text[$key] = $this->_process(
                        $record, $metadata, $value, $snippet, $escape, $filter);
                }

                // Return the joined text if there was a delimiter
                if ($delimiter) {
                    return join($delimiter, $text);
                } else {
                    return $text;
                }
            } else {
                // Return null if the index doesn't exist for the record.
                if (!isset($text[$index])) {
                    $text = null;
                } else {
                    $text = $text[$index];
                }
            }
        }

        // If we get here, we're working with a single value only.
        return $this->_process($record, $metadata, $text, $snippet, $escape, $filter);
    }

    /**
     * Options can sometimes be an integer or a string instead of an array,
     * which functions as a handy shortcut for theme writers.  This converts
     * the short form of the options into its proper array form.
     *
     * @param string|integer|array $options
     * @return array
     */
    protected function _getOptions($options)
    {
        $converted = array();
        if (is_integer($options)) {
            $converted = array(self::INDEX => $options);
        } else if (self::ALL == $options) {
            $converted = array(self::ALL => true);
        } else {
            $converted = (array) $options;
        }
        return $converted;
    }

    /**
     * Retrieve the text associated with a given element or field of the record.
     *
     * @param Omeka_Record_AbstractRecord $record
     * @param string|array $metadata
     * @return string|array Either an array of ElementText records or a string.
     */
    protected function _getText($record, $metadata)
    {
        // If $metadata is a string, we assume that it refers to a
        // special value, e.g. id, item type name, date added, etc.
        if (is_string($metadata)) {
            return $this->_getRecordMetadata($record, $metadata);
        }
        // If we get an array of length 2, retrieve the ElementTexts
        // that correspond to the given field.
        if (is_array($metadata) && count($metadata) == 2) {
            return $this->_getElementText($record, $metadata[0], $metadata[1]);
        }

        // If we didn't fit either of those categories, it's an invalid
        // argument.
        throw new Omeka_View_Exception('Unrecognized metadata specifier.');
    }

    /**
     * Retrieve record metadata that is not stored as ElementTexts.
     *
     * @uses Omeka_Record_AbstractRecord::getProperty()
     * @param Omeka_Record_AbstractRecord $record
     * @param string $specialValue Field name.
     * @return mixed
     */
    protected function _getRecordMetadata($record, $specialValue)
    {
        // Normalize to a valid record property.
        $property = str_replace(' ', '_', strtolower($specialValue));
        return $record->getProperty($property);
    }

    /**
     * Retrieve the set of ElementText records that correspond to a given
     * element set and element.
     *
     * @param Omeka_Record_AbstractRecord $record
     * @param string $elementSetName
     * @param string $elementName
     * @return array Set of ElementText records.
     */
    protected function _getElementText($record, $elementSetName, $elementName)
    {
        $elementTexts = $record->getElementTexts($elementSetName, $elementName);

        // Lock the records so that they can't be accidentally saved back to the
        // database, since we are modifying their values directly at this point.
        // Also clone the record because otherwise it would be passed by
        // reference to all the display filters, which results in munged text.
        foreach ($elementTexts as $key => $textRecord) {
            $elementTexts[$key] = clone $textRecord;
            $textRecord->lock();
        }

        return $elementTexts;
    }

    /**
     * Process an individual piece of text.
     *
     * If given an ElementText record, the actual text string will be
     * extracted automatically.
     *
     * @param Omeka_Record_AbstractRecord $record
     * @param string|array $metadata
     * @param string|ElementText $text Text to process.
     * @param int|bool $snippet Snippet length, or false if no snippet.
     * @param bool $escape Whether to HTML escape the text.
     * @param bool $filter Whether to pass the output through plugin
     *  filters.
     * @return string
     */
    protected function _process($record, $metadata, $text, $snippet, $escape, $filter)
    {
        if ($text instanceof ElementText) {
            $elementText = $text;
            $isHtml = $elementText->isHtml();
            $text = $elementText->getText();
        } else {
            $elementText = false;
            $isHtml = false;
        }

        if (is_string($text)) {
            // Apply the snippet option before escaping text HTML. If applied after
            // escaping the HTML, this may result in invalid markup.
            if ($snippet) {
                $text = snippet($text, 0, $snippet);
            }

            // Escape the non-HTML text if necessary.
            if ($escape && !$isHtml) {
                $text = html_escape($text);
            }
        }

        // Apply plugin filters.
        if ($filter) {
            $text = $this->_filterText($record, $metadata, $text, $elementText);
        }

        return $text;
    }

    /**
     * Apply filters to a text value.
     *
     * @param Omeka_Record_AbstractRecord $record
     * @param string|array $metadata
     * @param string $text
     * @param ElementText|bool $elementText
     * @return string
     */
    protected function _filterText($record, $metadata, $text, $elementText)
    {
        // Build the name of the filter to use. This will end up looking like:
        // array('Display', 'Item', 'Dublin Core', 'Title') or something similar.
        $filterName = array('Display', get_class($record));
        if (is_array($metadata)) {
            $filterName = array_merge($filterName, $metadata);
        } else {
            $filterName[] = $metadata;
        }
        return apply_filters($filterName, $text, array('record' => $record, 'element_text' => $elementText));
    }
}
