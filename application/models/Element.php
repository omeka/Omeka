<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

require_once 'ElementTable.php';
require_once 'ItemsElements.php';
require_once 'RecordType.php';
 
/**
 * 
 *
 * @package Omeka
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class Element extends Omeka_Record
{
    public $data_type_id;
    public $element_set_id;
    public $plugin_id;
    public $name = '';
    public $description;
    
    /**
     * Store element text within the element record itself.  Makes saving to 
     * the database convenient.
     * 
     * @var array
     **/
    protected $_texts = array();
    
    /**
     * Add some text to the element.
     * 
     * @param string
     * @return void
     **/
    public function addText($text)
    {
        $this->_texts[] = $text;
    }
    
    /**
     * Retrieve text values for this element.
     * 
     * @param integer
     * @return string|array
     **/
    public function getTextObjects($index=null)
    {
        if (is_integer($index)) {
            return $this->_texts[$index];
        }
        
        return $this->_texts;
    }
            
    /**
     * 
     * @todo Make the validation against the data type of the element. This
     *  should be possible to override by plugins (filters for validation).
     * @return void
     **/
    protected function _validate()
    {
        foreach ($this->getTextObjects() as $index => $text) {
            // preg_match returns 1 or 0 (true/false equivalent) based on 
            // whether the text passes the regex
            // if (!preg_match($this->type_regex, $text)) {
            //     $this->addError("$this->name", 
            //     "'$text' is not valid for the '{$this->name}' field!" .
            //     "  Please see the description of this field for more information." );
            // }
        }
    }
    
    /**
     * The filter naming here is kind of goofy, example:
     *
     * The following will apply 4 filters in progressively more descriptive fashion:
     *
     * array('Save', 'Item', 'Title', 'Dublin Core')
     *
     * First it will apply the array('Item') filter, then array(Item, Save), etc.
     * 
     * @param Omeka_Record
     * @param string Text for this element
     * @return string
     **/
    protected function applySaveFiltersFor($record, $value)
    {
        // This is always a 'Save' hook first and foremost
        $filterName = array('Save');
        
        // Item or File, currently
        $filterName[] = get_class($record);
        
        // Name of the element
        $filterName[] = $this->name;
        
        // Name of the element set (if applicable)
        if($this->set_name) {
            $filterName[] = $this->set_name;
        }
        
        return get_plugin_broker()->applySubFilters($filterName, $value, $record, $this);
    }    
}
