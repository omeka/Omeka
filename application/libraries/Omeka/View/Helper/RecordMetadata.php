<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @subpackage Omeka_View_Helper
 * @access private
 */

/**
 * Helper used to retrieve metadata for a record that makes use of element 
 * texts.
 * Currently limited to items and files.
 *
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @subpackage Omeka_View_Helper
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
abstract class Omeka_View_Helper_RecordMetadata extends Zend_View_Helper_Abstract
{    
    const SNIPPET = 'snippet';
    const INDEX = 'index';
    const ALL = 'all';
    const NO_ESCAPE = 'no_escape';
    const NO_FILTER = 'no_filter';
    const DELIMITER = 'delimiter';
    
    /**
     * Retrieve a specific piece of an item's metadata.
     * 
     * The most common use of this method is to retrieve a single text value for 
     * a given element, e.g. item([Item Record], 'Dublin Core, 'Title') will 
     * return a string corresponding to the first available 'Title' in the 
     * 'Dublin Core' element set. For item metadata not belonging to an element 
     * set (e.g. id, date added, and public), use the simple syntax: 
     * item([Item Record], 'id'). There are a number of options that can be 
     * passed via an array as the fourth argument.
     * 
     * @param Omeka_Record $record Database record representing the item from 
     * which to retrieve field data.
     * @param string $elementSetName The element set name of a specified element
     * OR another arbitrary field name for the record.
     * @param string $elementName The element belonging to the specified element 
     * set. If this parameter is not set, or if it is null, the code assumes the 
     * previous parameter is a special value.
     * @param array|string|integer $options Options for formatting the metadata 
     * for display.
     * - Default array options:
     *   - 'delimiter' => Return the entire set of metadata as a string, where 
     *     each entry is separated by the string delimiter.
     *   - 'index' => Return the metadata entry at the specific integer index, 
     *     starting at 0.
     *   - 'no_filter' => If set to true, return the set of metadata without 
     *     running any of the filters.
     *   - 'snippet' => Trim the length of each piece of text to the given 
     *     integer length.
     *   - 'all' => If set to true, this will retrieve an array containing all 
     *     values for a single element rather than a specific value.
     *   - 'no_escape' => If true, do not escape the resulting values for HTML
     *     entities.
     * - Default string option:
     *   Passing the string 'all' will retrieve an array containing all values 
     *   for a single element rather than a specific value.
     * - Default integer option:
     *   Passing an integer will return the metadata entry at the specific 
     *   integer index, starting at 0.
     * @return string|array|null Null if field does not exist for item. Array
     * if certain options are passed.  String otherwise.
     */
    protected function _get(Omeka_Record $record, 
                         $elementSetName, 
                         $elementName = null, 
                         $options     = array())
    {
        // Convert the shortcuts for the options into a proper array.
        $options = $this->_getOptions($options);

        // Set the initial text value, which is either an array of ElementText 
        // records, or a special value string.
        $text = $this->_getText($record, $elementSetName, $elementName);

        // Apply the snippet option before escaping text HTML. If applied after
        // escaping the HTML, this may result in invalid markup.
        if (array_key_exists(self::SNIPPET, $options)) {
            $text = $this->_snippetText($text, (int)$options[self::SNIPPET]);
        }

        // Escape the non-HTML text if necessary.
        if (!(array_key_exists(self::NO_ESCAPE, $options) && $options[self::NO_ESCAPE])) {
            $text = $this->_escapeTextHtml($text);
        }

        // Apply plugin filters.
        if (!(array_key_exists(self::NO_FILTER, $options) && $options[self::NO_FILTER])) {
            $text = $this->_filterText($text, $elementSetName, $elementName, $record);
        }

        if (is_array($text)) {
            // Extract the text from the records into an array.
            // This has to happen after escaping HTML because the html
            // flag is located within the ElementText record.
            $text = $this->_extractText($text);

            // Return the joined text
            if (isset($options[self::DELIMITER])) {
                return join((string) $options[self::DELIMITER], $text);
            }

            // Return the text at that index.
            if (isset($options[self::INDEX])) {
                // Return null if the index doesn't exist for the record.
                if (!isset($text[$options[self::INDEX]])) {
                    return null;
                }
                return $text[$options[self::INDEX]];
            }

            // If the all option is set, return the entire array of escaped data
            if (isset($options[self::ALL])) {
                return $text;
            }

            // Otherwise, return the first entry in the array.
            return reset($text);
        }

        // Or, if we're dealing with a string, return the whole thing.
        return $text;
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
     * @param Omeka_Record $record
     * @param string $elementSetName
     * @param string|null $elementName
     * @return string|array Either an array of ElementText records or a string.
     */
    protected function _getText($record, $elementSetName, $elementName)
    {        
        // If $elementName is null we assume that $elementSetName is actually a 
        // special value, e.g. id, item type name, date added, etc.
        if (null === $elementName) {
            $text = $this->_getRecordMetadata($record, $elementSetName);
            
        // Retrieve the ElementText records (or other values, like strings,
        // integers, booleans) that correspond to all the element text for the
        // given field.
        } else {
            $text = $this->_getElementText($record, $elementSetName, $elementName);
        }
        return $text;
    }
    
    /**
     * Retrieve record metadata that is not stored in the element_texts table. 
     * Examples include the database ID of the item, the name of the item type, 
     * the name of the collection, etc.
     * 
     * @param Omeka_Record $record
     * @param string $specialValue Field name.
     * @return mixed
     */
    abstract protected function _getRecordMetadata($record, $specialValue);
    
    /**
     * Retrieve the set of ElementText records that correspond to a given
     * element set and element.
     * 
     * @param Omeka_Record $record
     * @param string $elementSetName
     * @param string $elementName
     * @return array Set of ElementText records.
     */
    protected function _getElementText($record, $elementSetName, $elementName)
    {        
        $elementTexts = $record->getElementTextsByElementNameAndSetName($elementName, $elementSetName);
        
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
     * Apply filters to a set of element text.
     * 
     * @param array|string $text
     * @param string $elementSetName
     * @param string $elementName
     * @param Omeka_Record $record
     * @return array|string
     */
    protected function _filterText($text, $elementSetName, $elementName, $record)
    {
        // Build the name of the filter to use. This will end up looking like: 
        // array('Display', 'Item', 'Dublin Core', 'Title') or something similar.
        $filterName = array('Display', get_class($record), $elementSetName);
        if ($elementName !== null) {
            $filterName[] = $elementName;
        }

        if (is_array($text)) {
            
            // What to do if there is no text to filter?  For now, filter an 
            // empty string.
            if (empty($text)) {
                $text[] = new ElementText;
            }
            
            // This really needs to be an instance of ElementText for the 
            // following to work.
            if (!(reset($text) instanceof ElementText)) {
                throw new Exception('The provided text needs to be an instance of ElementText.');
            }
            
            // Apply the filters individually to each text record.
            foreach ($text as $elementTextRecord) {
                // This filter receives the Item record as well as the 
                // ElementText record
                $elementTextRecord->setText(apply_filters($filterName, $elementTextRecord->getText(), $record, $elementTextRecord));
            }
        } else {
            $text = apply_filters($filterName, $text, $record);
        }     
        
        return $text;
    }
    
    /**
     * Retrieve a snippet (or set of snippets) for the text values to return.
     * 
     * @param string|array $text
     * @param integer $length
     * @return string|array
     */
    protected function _snippetText($text, $length)
    {
        // Integers get no formatting
        if (is_int($text)) {

        } else if (is_string($text)) {
            $text = snippet($text, 0, $length);
        } else if (is_array($text)) {
            foreach ($text as $textRecord) {
                $textRecord->setText(snippet($textRecord->getText(), 0, $length));
            }   
        } else {
            throw new Exception('Cannot retrieve a text snippet for a data type that is a '. gettype($text));
        }
        
        return $text;
    }
    
    /**
     * This applies all filters defined for the 'html_escape' filter. 
     * This will only be applied to string values or element text records that 
     * are not marked as HTML. If they are marked as HTML, then there should be     
     * no escaping because the values are already stored in the database as 
     * fully valid HTML markup. Any errors resulting from displaying that HTML 
     * is the responsibility of the administrator to fix.
     * 
     * @param string|array $text
     * @return string|array
     */
    protected function _escapeTextHtml($text)
    {           
        // The assumption here is that all string values (item type name, 
        // collection name, etc.) will need to be escaped.
        if (is_string($text)) {
            $text = html_escape($text);
        } else if (is_array($text)) {
            foreach ($text as $record) {
                 if (!$record->isHtml()) {
                     $record->setText(html_escape($record->getText()));
                 }
             }
        } 
        
        return $text;
    }
    
    /**
     * Extract texts from an array.
     *
     * @param array $text
     * @return array
     */
    protected function _extractText($text)
    {
        $extractedText = array();
        foreach ($text as $key => $record) {
            if ($textString = $record->getText()) {
                $extractedText[$key] = $textString;
            }
        }
        return $extractedText;
    }
}
