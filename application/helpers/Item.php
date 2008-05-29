<?php 

/**
* 
*/
class Omeka_View_Helper_Item
{
    /**
     * Currently, options can be: a string to join the text with:
     * i.e. '</li><li>', so that doing the following:
     * <li><?php echo item('Title', '</li><li>'); ?></li> 
     * Will create a set of list elements for titles. 
     * 
     * @param string Field name to retrieve
     * @return string|array|null Null if field does not exist for item.  String
     * if certain options are passed.  Array otherwise.
     **/
    public function item($field, $options=array())
    {
        $text = $this->getElementText($field);
        
        $text = $this->filterElementText($text);
        
        //If this is an integer, return the text at that particular index
        if(is_integer($options)) {
            return @$text[$options];
        }
        
        //If options is a string, join the element text on that string
        if(is_string($options)) {
            return join($options, $text);
        }     
        
        return $text;
    }
    
    /**
     * Apply filters a set of element text.
     * 
     * @todo
     * @param array Hash of element names containing arrays of element text
     * values.
     * @return array Same structure but run through filters.
     **/
    public function filterElementText($elements)
    {
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
    
    public function getElementText($field)
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
