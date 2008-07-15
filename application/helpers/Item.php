<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * Helper used to retrieve metadata for an item.
 *
 * @see item()
 * @package Omeka
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class Omeka_View_Helper_Item
{
    /**
     * Currently, options can be: a string to join the text with:
     * i.e. '</li><li>', so that doing the following:
     * <li><?php echo item('Title', '</li><li>'); ?></li> 
     * Will create a set of list elements for titles. 
     * 
     * @param string Field name to retrieve
     * @param mixed Options for formatting the metadata for display.
     * Default options: 
     *  'delimiter' => return the entire set of metadata
     *      as a string, where each entry is separated by the string delimiter.
     *  'index' => return the metadata entry at the specific index (starting)
     *      from 0. 
     *  'noFilter' => return the set of metadata without running any of the 
     *      filters.
     *  'snippet' => trim the length of each piece of text to the given length
     *      (integer).
     *  'set_name' => retrieve the element text for an element that belongs to a specific set.
     *
     * @return string|array|null Null if field does not exist for item.  String
     * if certain options are passed.  Array otherwise.
     **/
    public function item($field, $options=array())
    {
        //Convert the shortcuts for the options into a proper array
        $options = $this->_getOptions($options);
        
        // Retrieve the ElementText records (or other values, like strings,
        // integers, booleans) that correspond to all the element text for the
        // given field.
        $text = $this->getElementText($field, $options);
        
        // Apply any plugin filters to the text prior to escaping it to valid HTML.
        if (!isset($options['noFilter'])) {
            $text = $this->filterElementText($text, $field);
        }
        
        // Apply the 'snippet' option before escaping the HTML. If applied after
        // escaping the HTML, this may result in invalid markup.
        if ($snippetLength = (int) @$options['snippet']) {
            $text = $this->_formatSubstring($text, $snippetLength);
        }
        
        // Escape the non-HTML text if necessary.
        $text = $this->_escapeForHtml($text, $options);
        
        // Extract the text from the records into an array.
        // This has to happen after escaping the HTML because the 'html' flag is
        // located within the ElementText record.
        if (is_array($text) and reset($text) instanceof ElementText) {
           $text = $this->_extractTextFromRecords($text); 
        }

        // Apply additional formatting options on that array, including 'delimiter' and 'index'.
        
        // Return the join'd text
        if(isset($options['delimiter'])) {
            return join((string) $options['delimiter'], (array) $text);
        }
        
        // Return the text at that index (suppress errors)
        if(isset($options['index'])) {
            return @$text[$options['index']];
        }

        return $text;
    }
    
    protected function _formatSubstring($texts, $length)
    {
        // Integers get no formatting
        if (is_int($texts)) {
            return $texts;
        }
        
        if (is_string($texts)) {
            return snippet($texts, 0, $length);
        }
        
        if (is_array($texts)) {
            foreach ($texts as $key => $textRecord) {
                $textRecord->setText( snippet($textRecord->getText(), 0, $snippetLength) );
            }   
            return $texts;         
        }
        
        throw new Exception('Cannot retrieve a text snippet for a data type that is a '. gettype($texts));
    }
    
    protected function _extractTextFromRecords($text)
    {
        $extracted = array();
        foreach ($text as $key => $record) {
            $extracted[$key] = $record->getText();
        }
        return $extracted;
    }
    
    /**
     * This applies all filters defined for the 'html_escape' filter. This will
     *  only be applied to string values or element text records that are not
     *  marked as HTML. If they are marked as HTML, then there should be no
     *  escaping because the values are already stored in the database as fully
     *  valid HTML markup. Any errors resulting from displaying that HTML is the
     *  responsibility of the administrator to fix.
     * 
     * @param string|array
     * @return string|array
     **/
    protected function _escapeForHtml($texts, array $options)
    {   
        // The assumption here is that all string values (item type name,
        // collection name, etc.) will need to be escaped.
        if (is_string($texts)) {
            return apply_filters('html_escape', $texts);
        } elseif (is_array($texts)) {
            foreach ($texts as $key => $record) {
                 if (!$record->isHtml()) {
                     $record->setText(apply_filters('html_escape', $record->getText()));
                 }
             }
             return $texts;
        } else {
            // Just return the text as it is if it is neither a string nor an array.
            return $texts;
        }
    }
    
    /**
     * Format the set of text based on the options passed to the helper.
     * 
     * @param array|string
     * @param array
     * @return mixed
     **/
    protected function _formatWithOptions($texts, array $options)
    {        

    }
    
    /**
     * Options can sometimes be an integer or a string instead of an array,
     * which functions as a handy shortcut for theme writers.  This converts
     * the short form of the options into its proper array form.
     * 
     * @param mixed
     * @return array
     **/
    protected function _getOptions($options)
    {
        if(is_integer($options)) {
            return array('index'=>$options);
        }
        
        if(is_string($options)) {
            return array('delimiter'=>$options);
        }
        
        return (array) $options;
    }
    
    /**
     * Apply filters a set of element text.
     * 
     * @todo
     * @param array Set of element text.
     * values.
     * @return array Same structure but run through filters.
     **/
    public function filterElementText($elements, $field)
    {
        // if($pluginBroker = Omeka_Context::getInstance()->getPluginBroker()) {
        //     $elements = $pluginBroker->applyOutputFilters($field, $elements);
        // }
        
        return $elements;
    }
    
    /**
     * List of fields besides elements that an item can display.
     * 
     * @param string
     * @return boolean
     **/
    public function hasOtherField($field)
    {
        return in_array(strtolower($field),
            array('id',
            'featured',
            'public',
            'type name',
            'date added',
            'collection name'));
            
    }
    
    /**
     * Retrieve the value of any field for an item that does not correspond to
     * an Element record.  Examples include the database ID of the item, the
     * name of the item type, the name of the collection, etc.
     * 
     * @param string
     * @param Item
     * @return mixed
     **/
    public function getOtherField($field, $item)
    {
        switch (strtolower($field)) {
            case 'id':
                return $item->id;
                break;
            case 'type name':
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
                # code...
                break;
        }
    }
    
    /**
     * Retrieve an Element record which will contain all of the text records for
     * the current item.
     * 
     * @param string Name of the element
     * @param string Name of the set to which the element belongs.
     * @return Element|null
     **/
    protected function getElementRecordByName($item, $elementName, $setName=null)
    {
        // Get the set of all elements.
        $namedElements = $item->Elements[$elementName];
        
        if (!is_array($namedElements)) {
            throw new Exception("Element named '$elementName' does not exist for this item!");
        }
        
        // These are further indexed by the set, so if a set name is passed, 
        // then look for that element, otherwise return the one element. If
        // there is more than one element, throw an error.
        if ($setName) {
            return @$namedElements[$setName];
        } else {
            if (count($namedElements) > 1) {
                throw new Exception('More than one element named "' .
                $elementName . '" exists, so you must choose the name of the set
                when displaying these elements!');
            } else {
                return current($namedElements);
            }
        }                
    }
    
    /**
     * Retrieve the set of element text for the item.
     * 
     * @param string
     * @return array|string
     **/
    public function getElementText($field, array $options)
    {
        $item = get_current_item();

        //Any built-in fields or special naming schemes
        if($this->hasOtherField($field)) {
            return $this->getOtherField($field, $item);
        }        
        
        $element = $this->getElementRecordByName($item, $field, @$options['set_name']);
        
        // Get all the text records for this item.
        
        // The element text records are indexed by the element_id for easy lookup.
        $elementText = (array) $item->ElementTexts[$element->id];
        
        // Lock the records so that they can't be accidentally saved back to the
        // database, since we are modifying their values directly at this point.
        foreach ($elementText as $key => $record) {
            $record->lock();
        }
        
        return $elementText;
    }
}
