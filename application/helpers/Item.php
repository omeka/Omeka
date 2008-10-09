<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package OmekaThemes
 */

/**
 * Helper used to retrieve metadata for an item.
 *
 * @see item()
 * @package Omeka
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 */
class Omeka_View_Helper_Item
{
    private $_item;
    private $_elementSetName;
    private $_elementName;
    private $_options;
    private $_text;
    
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
     * @param Item Database record representing the item from which to retrieve 
     * field data.
     * @param string The element set name of a specified element OR the special 
     * value of the item record.
     * @param string The element belonging to the specified element set. If this 
     * parameter is not set, or if it is null, the code assumes the previous 
     * parameter is a special value.
     * @param array|string|integer Options for formatting the metadata for 
     * display.
     * Default array options:
     *   'delimiter' => Return the entire set of metadata as a string, where 
     *       each entry is separated by the string delimiter.
     *   'index' => Return the metadata entry at the specific integer index, 
     *       starting at 0.
     *   'no_filter' => If set to true, return the set of metadata without 
     *       running any of the filters.
     *   'snippet' => Trim the length of each piece of text to the given integer 
     *       length.
     *   'all' => If set to true, this will retrieve an array containing all 
     *       values for a single element rather than a specific value.
     * Default string option:
     *     Passing the string 'all' will retrieve an array containing all values 
     *     for a single element rather than a specific value.
     * Default integer option:
     *     Passing an integer will return the metadata entry at the specific 
     *     integer index, starting at 0.
     *
     * @return string|array|null Null if field does not exist for item. Array
     * if certain options are passed.  String otherwise.
     */
    public function item(Item $item, 
                         $elementSetName, 
                         $elementName = null, 
                         $options     = array())
    {
        // Set this object's properties.
        $this->_item           = $item;
        $this->_elementSetName = $elementSetName;
        $this->_elementName    = $elementName;
        $this->_options        = $options;
        
        // Convert the shortcuts for the options into a proper array.
        $this->_setOptions();
        
        // Set the initial text value, which is either an array of ElementText 
        // records, or a special value string.
        $this->_setText();
        
        // Apply plugin filters to the text prior to making a snippet or 
        // escaping text HTML.
        $this->_filterText();
        
        // Apply the snippet option before escaping text HTML. If applied after
        // escaping the HTML, this may result in invalid markup.
        $this->_snippetText();
        
        // Escape the non-HTML text if necessary.
        $this->_escapeTextHtml();
        
        // Extract the text from the records into an array. This has to happen 
        // after escaping text HTML because the html flag is located within the 
        // ElementText record.
        $this->_extractText();
        
        // Apply additional formatting options on that array, including 
        // 'delimiter' and 'index'.
        
        // Return the join'd text
        if (isset($this->_options['delimiter'])) {
            return join((string) $this->_options['delimiter'], (array) $this->_text);
        }
        
        // Return the text at that index.
        if (is_array($this->_text) && isset($this->_options['index'])) {
            // Return null if the index doesn't exist for the item.
            if (!isset($this->_text[$this->_options['index']])) {
                return null;
            }
            return $this->_text[$this->_options['index']];
        }
        
        // If the all option is set, return the entire array of escaped data
        if (is_array($this->_text) && isset($this->_options['all'])) {
            return $this->_text;
        }
        
        // Return the first entry in the array or the whole thing if it's a 
        // string.
        return is_array($this->_text) ? reset($this->_text) : $this->_text;
    }
    
    /**
     * Options can sometimes be an integer or a string instead of an array,
     * which functions as a handy shortcut for theme writers.  This converts
     * the short form of the options into its proper array form.
     * 
     * @return void
     **/
    private function _setOptions()
    {
        $options = $this->_options;
        if (is_integer($options)) {
            $this->_options = array('index' => $options);
        } else if ('all' == $options) {
            $this->_options = array('all' => true);
        } else {
            $this->_options = (array) $options;
        }
    }
    
    private function _setText()
    {
        $elementSetName = $this->_elementSetName;
        $elementName = $this->_elementName;
        
        // If $elementName is null we assume that $elementSetName is actually a 
        // special value, e.g. id, item type name, date added, etc.
        if (null === $elementName) {
            $text = $this->_getSpecialValue($elementSetName);
            
        // Retrieve the ElementText records (or other values, like strings,
        // integers, booleans) that correspond to all the element text for the
        // given field.
        } else {
            $text = $this->_getElementText($elementSetName, $elementName);
        }
        $this->_text = $text;
    }
    
    /**
     * Retrieve a special value of an item that does not correspond to an 
     * Element record. Examples include the database ID of the item, the
     * name of the item type, the name of the collection, etc.
     * 
     * @param string
     * @return mixed
     **/
    private function _getSpecialValue($specialValue)
    {
        $item = $this->_item;
        switch (strtolower($specialValue)) {
            case 'id':
                return $item->id;
                break;
            case 'item type name':
                return $item->Type->name;
                break;
            case 'date added':
                return $item->added;
                break;
            case 'collection name':
                return $item->Collection->name;
                break;
            case 'featured':
                return $item->featured;
                break;
            case 'public':
                return $item->public;
                break;
            default:
                throw new Exception("'$specialValue' is an invalid special value.");
                break;
        }
    }
    
    private function _getElementText($elementSetName, $elementName)
    {
        $item = $this->_item;
        
        $elementTexts = $item->getElementTextsByElementNameAndSetName($elementName, $elementSetName);
        
        // Lock the records so that they can't be accidentally saved back to the 
        // database, since we are modifying their values directly at this point. 
        // Also clone the record because otherwise it would be passed by 
        // reference to all the display filters, which results in munged text.
        foreach ($elementTexts as $key => $record) {
            $elementTexts[$key] = clone $record;
            $record->lock();
        }
        
        return $elementTexts;
    }
    
    /**
     * Apply filters a set of element text.
     * 
     * @return void
     **/
    private function _filterText()
    {
        if (isset($this->_options['no_filter'])) {
            return;
        }
        
        $text = $this->_text;
        $elementSetName = $this->_elementSetName;
        $elementName = $this->_elementName;
        
        // Build the name of the filter to use. This will end up looking like: 
        // array('Display', 'Item', 'Title', 'Dublin Core') or something similar.
        $filterName = array('Display', 'Item');
        if (null === $elementName) {
            $filterName[] = $elementSetName;
        } else {
            $filterName[] = $elementName;
            $filterName[] = $elementSetName;
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
            foreach ($text as $record) {
                // This filter receives the Item record as well as the 
                // ElementText record
                $record->setText(apply_filters($filterName, $record->getText(), $item, $record));
            }
        } else {
            $text = apply_filters($filterName, $text, $item, $record);
        }     
        
        $this->_text = $text;
    }
    
    private function _snippetText()
    {
        if (!isset($options['snippet'])) {
            return;
        }
        
        $length = (int) $this->_options['snippet'];
        $text = $this->_text;
        
        // Integers get no formatting
        if (is_int($text)) {
            $this->_text = $text;
        } else if (is_string($text)) {
            $this->_text = snippet($text, 0, $length);
        } else if (is_array($text)) {
            foreach ($text as $textRecord) {
                $textRecord->setText(snippet($textRecord->getText(), 0, $length));
            }   
            $this->_text = $text;         
        } else {
            throw new Exception('Cannot retrieve a text snippet for a data type that is a '. gettype($text));
        }
    }
    
    /**
     *  This applies all filters defined for the 'html_escape' filter. This will
     *  only be applied to string values or element text records that are not
     *  marked as HTML. If they are marked as HTML, then there should be no
     *  escaping because the values are already stored in the database as fully
     *  valid HTML markup. Any errors resulting from displaying that HTML is the
     *  responsibility of the administrator to fix.
     * 
     * @return void
     */
    private function _escapeTextHtml()
    {   
        $text = $this->_text;
        
        // The assumption here is that all string values (item type name, 
        // collection name, etc.) will need to be escaped.
        if (is_string($text)) {
            $this->_text = apply_filters('html_escape', $text);
        } else if (is_array($text)) {
            foreach ($text as $record) {
                 if (!$record->isHtml()) {
                     $record->setText(apply_filters('html_escape', $record->getText()));
                 }
             }
             $this->_text = $text;
        } else {
            // Just return the text as it is if it is neither a string nor an 
            // array.
            $this->_text = $text;
        }
    }
    
    private function _extractText()
    {
        if (!is_array($this->_text) || !(reset($this->_text) instanceof ElementText)) {
            return;
        }
        $text = $this->_text;
        $extractedText = array();
        foreach ($text as $key => $record) {
            $extractedText[$key] = $record->getText();
        }
        $this->_text = $extractedText;
    }
}
