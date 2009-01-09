<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * @todo Must be able to handle file uploads.
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2009
 **/
class InsertItemHelper
{
    protected $_item;
    protected $_metadata = array();
    protected $_elementTexts = array();
    
    /**
     * @param Item|integer|null $item
     * @param array $itemMetadata Set of metadata options for configuring the
     *  item.  Array which can include the following properties:
     *      'public' (boolean)
     *      'featured' (boolean)
     *      'collection_id' (integer)
     *      'item_type_id' (integer)
     *      'item_type_name' (string)
     *      'tags' (string, comma-delimited)
     *      'tag_entity' (Entity, optional and only checked if 'tags' is given)
     *      'overwriteElementTexts' (boolean) -- determines whether or not to
     *  overwrite existing element texts.  If true, this will loop through the
     *  element texts provided in $elementTexts, and it will update existing
     *  records where possible.  All texts that are not yet in the DB will be
     *  added in the usual manner.  False by default.
     * @param array $elementTexts Array of element texts to assign to the item. 
     *  This takes the format: array('Element Set Name'=>array('Element Name'=>array(array('text'=>(string), 'html'=>(boolean))))).
     **/
    public function __construct($item = null, $itemMetadata = array(), $elementTexts = array())
    {
        if (!$item) {
            $item = new Item;
        } else if (is_int($item)) {
            $item = get_db()->getTable('Item')->find($item);
            if (!$item) {
                throw new Exception('Item could not be retrieved for update based on the ID given!');
            }
        } else if (!($item instanceof Item)) {
            throw new Exception('$item must be either an Item record or the item ID!');
        }
        $this->_item = $item;
        $this->_metadata = (array)$itemMetadata;
        $this->_elementTexts = (array)$elementTexts;
    }
    
    public function run()
    {
        $item = $this->getItem();
        $this->setProperties($item, $this->_metadata);
        
        if ($item->exists() 
        and array_key_exists('overwriteElementTexts', $this->_metadata)) {
            $this->replaceElementTexts();
        } else {
            $this->addElementTexts();
        }
                
        // Save Item and all of its metadata.  Throw exception if it fails.
        $item->forceSave();
        
        // Must take place after save().
        if (array_key_exists('tags', $this->_metadata) 
        and !empty($this->_metadata['tags'])) {
            $this->addTags();
        }
        
        // Save Element Texts (necessary)
        $item->saveElementTexts();
    }
    
    public function getItem()
    {
        return $this->_item;
    }
    
    public function setProperties($item, $itemMetadata)
    {
        // Item Metadata
        $settableAttributes = array('public', 'featured', 'collection_id', 'item_type_id');
        foreach ($settableAttributes as $attr) {
            if (array_key_exists($attr, $itemMetadata)) {
                $item->$attr = $itemMetadata[$attr];
            }
        }
        
        if (array_key_exists('item_type_name', $itemMetadata)) {
            $itemType = get_db()->getTable('ItemType')->findBySql('name = ?', array($itemMetadata['item_type_name']), true);

            if(!$itemType) {
                throw new Omeka_Validator_Exception( "Invalid type named {$_POST['type']} provided!");
            }

            $item->item_type_id = $itemType->id;
        }
    }
    
    public function addElementTexts()
    {
        $elementTexts = $this->_elementTexts;
        
        foreach ($elementTexts as $elementSetName => $elements) {
            foreach ($elements as $elementName => $elementTexts) {
                $element = $this->_item->getElementByNameAndSetName($elementName, $elementSetName);
                foreach ($elementTexts as $elementText) {
                    if (!array_key_exists('text', $elementText)) {
                        throw new Exception('Element texts are formatted incorrectly for insert_item()!');
                    }
                    // Only add the element text if it's not empty.  There
                    // should be no empty element texts in the DB.
                    if (!empty($elementText['text'])) {
                        $this->_item->addTextForElement($element, $elementText['text'], $elementText['html']);
                    }
                }
            }
        }
    }    
        
    public function replaceElementTexts()
    {
        $elementTexts = $this->_elementTexts;
        $item = $this->_item;
        // If this option is set, it will loop through the $elementTexts provided,
        // find each one and manually update it (provided it exists).
        // The rest of the element texts will get added as per usual.
        foreach ($elementTexts as $elementSetName => $textArray) {
            foreach ($textArray as $elementName => $elementTextSet) {
                $etRecordSet = $item->getElementTextsByElementNameAndSetName($elementName, $elementSetName);
                foreach ($elementTextSet as $elementTextIndex => $textAttr) {
                    // If we have an existing ElementText record, use that
                    // instead of adding a new one.
                    if (array_key_exists($elementTextIndex, $etRecordSet)) {
                        $etRecord = $etRecordSet[$elementTextIndex];
                        $etRecord->text = $textAttr['text'];
                        $etRecord->html = $textAttr['html'];
                        $etRecord->forceSave();
                    } else {
                        // Otherwise we should just append the new text to the 
                        // pre-existing ones.
                        $elementRecord = $item->getElementByNameAndSetName($elementName, $elementSetName);
                        $item->addTextForElement($elementRecord, $textAttr['text'], $textAttr['html']);
                    }
                }
            }
        }
    }
    
    public function addTags()
    {
        // As of 0.10 we still need to tag for a specific entity.
        // This may change in future versions.
        $entityToTag = array_key_exists('tag_entity', $this->_metadata) ?
            $this->_metadata['tag_entity'] : current_user()->Entity;
        $this->_item->addTags($this->_metadata['tags'], $entityToTag);
    }
}
