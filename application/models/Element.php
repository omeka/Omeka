<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

require_once 'ElementTable.php';
require_once 'ItemsElements.php';
 
/**
 * 
 *
 * @package Omeka
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class Element extends Omeka_Record
{
    public $element_type_id;
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
     * @return void
     **/
    public function setText(array $text)
    {
        $this->_texts = $text;
    }
    
    /**
     * Retrieve only the text values (useful for for display).
     *
     * @todo Should filters run here?
     * @param integer
     * @return string|array|null
     **/
    public function getTextValues($index=null)
    {
        if (is_integer($index)) {
            $obj = $this->_texts[$index];
            if ($obj) {
                return $obj->text;
            }
        } else {
            $texts = array();
            foreach ($this->_texts as $key => $obj) {
                $texts[$key] = $obj->text;
            }
            
            return $texts;
        }
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
     * Use the type_regex field to validate this Element record.
     * 
     * @return void
     **/
    protected function _validate()
    {
        foreach ($this->getTextValues() as $index => $text) {
            // preg_match returns 1 or 0 (true/false equivalent) based on 
            // whether the text passes the regex
            if (!preg_match($this->type_regex, $text)) {
                $this->addError("$this->name", 
                "'$text' is not valid for the '{$this->name}' field!" .
                "  Please see the description of this field for more information." );
            }
        }
    }
    
    /**
     * The filter naming here is kind of goofy, example:
     *
     * The following will apply 4 filters in progressively more descriptive fashion:
     *
     * array('Save', 'Item', 'Title', 'Dublin Core Element Set') 
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
    
    /**
     * Save a set of texts to the database for a given record.
     * 
     * The assumption here is that the text has already been validated.
     *
     * @todo Currently only works for Item records. Should work for Files as
     * well as other arbitrary join tables. 
     * @param Omeka_Record
     * @return void
     **/
    public function saveTextFor(Omeka_Record $record)
    {
        $text = $this->getTextValues();
        
        // Can't do this if one or both of these records doesn't exist
        if(!$record->exists() or !$this->exists()) {
            throw new Exception("Record must exist in order to save Element text for it!");
        }
        
        switch (get_class($record)) {
            // Saving element text for items consists of finding or creating
            // an instance of ItemsElements (the join table) and saving that.
            case 'Item':
                // Get some join records and save that jax to the database
                $ies = $this->getTable('ItemsElements')
                ->findOrNewByItemAndElement($record->id, $this->id, count($text));

                // Loop through all the element text records and save them
                // Maybe this should be a separate method.
                foreach ($text as $key => $value) {
                    $ie = $ies[$key];
                    
                    // Element filter names are all arrays (makes more readable)
                    $value = $this->applySaveFiltersFor($record, $value);                    
                    
                    // If the text is empty, we should delete the record
                    if (empty($value)) {
                        if ($ie->exists()) {
                            $ie->delete();
                        }
                    } else {
                    // Otherwise we should set the text and save the record
                        $ie->text = $value;
                        $ie->save();
                    }
                }
                
                
                break;
            default:
                throw new Exception('Currently Element text can only be saved for Items!');
                break;
        }
        
    }
}
