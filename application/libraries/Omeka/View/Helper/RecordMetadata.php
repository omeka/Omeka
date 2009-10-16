<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @subpackage Omeka_View_Helper
 */

/**
 * Helper used to retrieve metadata for a record that makes use of element texts.
 * Currently limited to items and files.
 *
 * @package Omeka
 * @subpackage Omeka_View_Helper
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 */
abstract class Omeka_View_Helper_RecordMetadata extends Zend_View_Helper_Abstract
{    
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
     * @param Omeka_Record Database record representing the item from which to retrieve 
     * field data.
     * @param string The element set name of a specified element OR another
     * arbitrary field name for the record.
     * @param string The element belonging to the specified element set. If this 
     * parameter is not set, or if it is null, the code assumes the previous 
     * parameter is a special value.
     * @param array|string|integer Options for formatting the metadata for 
     * display.
     * <ul>
     * <li>Default array options:
     * <ul>
     *   <li>'delimiter' => Return the entire set of metadata as a string, where 
     *       each entry is separated by the string delimiter.</li>
     *   <li>'index' => Return the metadata entry at the specific integer index, 
     *       starting at 0.</li>
     *   <li>'no_filter' => If set to true, return the set of metadata without 
     *       running any of the filters.</li>
     *   <li>'snippet' => Trim the length of each piece of text to the given integer 
     *       length.</li>
     *   <li>'all' => If set to true, this will retrieve an array containing all 
     *       values for a single element rather than a specific value.</li>
     *   <li>'no_escape' => If true, do not escape the resulting values for HTML
     * entities.</li>
     * </ul></li>
     * <li>Default string option:
     *     Passing the string 'all' will retrieve an array containing all values 
     *     for a single element rather than a specific value.</li>
     * <li>Default integer option:
     *     Passing an integer will return the metadata entry at the specific 
     *     integer index, starting at 0.</li>
     * </ul>
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
        if ($snippetLength = (int)$options['snippet']) {
            $text = $this->_snippetText($text, $snippetLength);
        }
        
        // Escape the non-HTML text if necessary.
        $escapedText = (array_key_exists('no_escape', $options) && $options['no_escape'])
                     ? $text : $this->_escapeTextHtml($text);
        
        // Apply plugin filters.
        $filteredText = !array_key_exists('no_filter', $options) 
                        ? $this->_filterText($escapedText, $elementSetName, $elementName, $record) 
                        : $escapedText;
        
        // Extract the text from the records into an array. This has to happen 
        // after escaping text HTML because the html flag is located within the 
        // ElementText record.
        $extractedText = is_array($filteredText) 
                         ? $this->_extractText($filteredText)
                         : $filteredText;
        
        // Apply additional formatting options on that array, including 
        // 'delimiter' and 'index'.
        
        // Return the join'd text
        if (isset($options['delimiter'])) {
            return join((string) $options['delimiter'], $extractedText);
        }
        
        // Return the text at that index.
        if (is_array($extractedText) && isset($options['index'])) {
            // Return null if the index doesn't exist for the record.
            if (!isset($text[$options['index']])) {
                return null;
            }
            return $extractedText[$options['index']];
        }
        
        // If the all option is set, return the entire array of escaped data
        if (is_array($extractedText) && isset($options['all'])) {
            return $extractedText;
        }
        
        // Return the first entry in the array or the whole thing if it's a 
        // string.
        return is_array($extractedText) ? reset($extractedText) : $extractedText;
    }
    
    /**
     * Options can sometimes be an integer or a string instead of an array,
     * which functions as a handy shortcut for theme writers.  This converts
     * the short form of the options into its proper array form.
     * 
     * @param string|integer|array
     * @return array
     **/
    protected function _getOptions($options)
    {
        $converted = array();
        if (is_integer($options)) {
            $converted = array('index' => $options);
        } else if ('all' == $options) {
            $converted = array('all' => true);
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
     **/
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
     **/
    abstract protected function _getRecordMetadata($record, $specialValue);
    
    /**
     * Retrieve the set of ElementText records that correspond to a given
     * element set and element.
     * 
     * @param Omeka_Record $record
     * @param string $elementSetName
     * @param string $elementName
     * @return array Set of ElementText records.
     **/
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
     * Apply filters a set of element text.
     * 
     * @return array|string
     **/
    protected function _filterText($text, $elementSetName, $elementName, $record)
    {
        // Build the name of the filter to use. This will end up looking like: 
        // array('Display', 'Item', 'Dublin Core', 'Title') or something similar.
        $filterName = array('Display', get_class($record));
        if (null === $elementName) {
            $filterName[] = $elementSetName;
        } else {
            $filterName[] = $elementSetName;
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
            $text = apply_filters($filterName, $text, $record, $elementTextRecord);
        }     
        
        return $text;
    }
    
    /**
     * Retrieve a snippet (or set of snippets) for the text values to return.
     * 
     * @param string $text
     * @param integer $length
     * @return string|array
     **/
    protected function _snippetText($text, $length)
    {        // Integers get no formatting
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
     *  This applies all filters defined for the 'html_escape' filter. This will
     *  only be applied to string values or element text records that are not
     *  marked as HTML. If they are marked as HTML, then there should be no
     *  escaping because the values are already stored in the database as fully
     *  valid HTML markup. Any errors resulting from displaying that HTML is the
     *  responsibility of the administrator to fix.
     * 
     * @param string|array
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
     * @param array
     * @return array
     **/
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
