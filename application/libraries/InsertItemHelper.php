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
     * 
     * @param array $elementTexts Array of element texts to assign to the item. 
     *  This takes the format: array('Element Set Name'=>array('Element Name'=>array(array('text'=>(string), 'html'=>(boolean))))).
     *  See ActsAsElementText::addElementTextsByArray() for more info.
     * 
     * @param array $fileMetadata Set of metadata options that allow one or more
     * files to be associated with the item.  Includes the following options:
     *      'file_transfer_type' (string = 'Url|Filesystem|Upload' or 
     * Omeka_File_Transfer_Adapter_Interface).  Corresponds to the 
     * $transferStrategy argument for addFiles().
     *      'file_ingest_options' OPTIONAL (array of possible options to pass
     * modify the behavior of the ingest script).  Corresponds to the $options 
     * argument for addFiles().
     *      'files' (array or string) Represents information indicating the file
     * to ingest.  Corresponds to the $files argument for addFiles().
     * @see InsertItemHelper::addFiles()
     * @see ActsAsElementTexxt::addElementTextsByArray()
     **/
    public function __construct($item = null, $itemMetadata = array(), $elementTexts = array(), $fileMetadata = array())
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
        $this->_fileMetadata = (array)$fileMetadata;
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
                
        // Note to future self: Files need to be ingested at the end of the 
        // process, because there is a possibility that the file ingest could
        // fail with an exception, and this ensures that as much metadata is 
        // retained as possible before that.
        if (array_key_exists('files', $this->_fileMetadata)) {
            if (!array_key_exists('file_transfer_type', $this->_fileMetadata)) {
                throw new Exception("Must specify 'file_transfer_type' when attaching files to an item!");
            }
            $this->addFiles(
                $this->_fileMetadata['file_transfer_type'], 
                $this->_fileMetadata['files'], 
                (array)$this->_fileMetadata['file_ingest_options']);
        }
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
        return $this->_item->addElementTextsByArray($this->_elementTexts);
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
    
    /**
     * Add files to a pre-existing item.
     * 
     * @param string|Omeka_File_Transfer_Adapter_Interface $transferStrategy
     * This can either be one of the following strings denoting built-in transfer
     * methods: 
     *      'Upload', 'Filesystem', 'Url'
     * Or it could be an implemented File_Transfer_Adapter class.
     * 
     * @param string|array $files This can be a single string, an array of strings,
     * or an array of arrays, depending on the parameters that are needed by the 
     * underlying strategy.  Expected parameters for the built in strategies are
     * as follows:
     * 
     *      'Upload' => null|string If a string is given, it represents the 
     * POST parameter name containing the uploaded file(s).  If null is given,
     * all files in the POST will be ingested.
     * 
     *      'Url|Filesystem' => string|array If a string is given, this represents
     * the source identifier of a single file (the URL representing the file, or 
     * the absolute file path, respectively).  If an array is given, it assumes
     * that each entry in the array must be either an array or a string.  If it
     * an array, there are several default keys that may be present:
     *          'source' => Any identifier that is appropriate to the transfer
     * strategy in use.  For 'Url', this should be a valid URL.  For 'Filesystem',
     * it must be an absolute path to the source file to be transferred.
     *          'name' => OPTIONAL The filename to give to the transferred
     * file.  This can be any arbitrary filename and will be listed as the 
     * original filename of the file.  This will also be used to generate the 
     * archival filename for the file.  If none is given, this defaults to using
     * the getOriginalFileName() method of the transfer adapter.
     *          'metadata' => This could contain any metadata that needs to be
     * associated with the file.  This should be indexed in the same fashion
     * as for items.  See ActsAsElementText::addTextsByArray()
     *
     * @param array $options OPTIONAL May contain the following flags where
     * appropriate:
     *      'ignore_invalid_files' => Do not throw exceptions when
     * attempting to ingest invalid files.  Instead, skip to the next file in
     * the list and continue processing.  False by default. (all except Upload).
     *      'ignoreNoFile' => Ignore errors resulting from POSTs that do not 
     * contain uploaded files as expected (only for Upload).
     * @return mixed
     **/
    public function addFiles($transferStrategy, $files, array $options = array())
    {
        if (!$this->_item->exists()) {
            throw new Exception('Can only add files to an item that is persisted in the database!');
        }
        
        if ($transferStrategy instanceof Omeka_File_Ingest_Abstract) {
            $ingester = $transferStrategy;
            $ingester->setItem($this->_item);
            $ingester->setOptions($options);
        } else {
            $ingester = Omeka_File_Ingest_Abstract::factory($transferStrategy,
                                                            $this->_item,
                                                            $options);
        }
        
        return $ingester->ingest($files);
    }
}
