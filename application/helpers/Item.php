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
     *
     * @return string|array|null Null if field does not exist for item.  String
     * if certain options are passed.  Array otherwise.
     **/
    public function item($field, $options=array())
    {
        //Convert the shortcuts for the options into a proper array
        $options = $this->_getOptions($options);
        
        $text = $this->getElementText($field, $options);
        
        if(!isset($options['noFilter'])) {
            $text = $this->filterElementText($text, $field);
        }
        
        return $this->_formatWithOptions($text, $options);        
    }
    
    /**
     * Format the set of text based on the options passed to the helper.
     * 
     * @param array|string
     * @param array
     * @return mixed
     **/
    protected function _formatWithOptions($text, array $options)
    {
        // Return the join'd text
        if(isset($options['delimiter'])) {
            return join($options['delimiter'], (array) $text);
        }
        
        // Return the text at that index (suppress errors)
        if(isset($options['index'])) {
            return @$text[$options['index']];
        }
        
        return $text;
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
            default:
                # code...
                break;
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

        //All elements
        $elements = $item->Elements;

        $fieldElements = @$elements[$field];
        
        if(!$fieldElements) return array();
        
        $text = array();
        foreach ($fieldElements as $key => $element) {
            $text[$key] = $element->text;
        }
        
        return $text;
    }
}
