<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * @package Omeka
 * @copyright Center for History and New Media, 2009
 **/
class ItemBuilder
{
    protected $_item;
    protected $_metadata = array();
    protected $_elementTexts = array();
    
    /**
     * @param Item|integer|null $item
     * @param array $itemMetadata Set of metadata options for configuring the
     *  item.  Array which can include the following properties:
     *  <ul>
     *      <li>'public' (boolean)</li>
     *      <li>'featured' (boolean)</li>
     *      <li>'collection_id' (integer)</li>
     *      <li>'item_type_id' (integer)</li>
     *      <li>'item_type_name' (string)</li>
     *      <li>'tags' (string, comma-delimited)</li>
     *      <li>'tag_entity' (Entity, optional and only checked if 'tags' is given)</li>
     *      <li>'overwriteElementTexts' (boolean) -- determines whether or not to
     *  overwrite existing element texts.  If true, this will loop through the
     *  element texts provided in $elementTexts, and it will update existing
     *  records where possible.  All texts that are not yet in the DB will be
     *  added in the usual manner.  False by default.</li>
     *  </ul> 
     * 
     * @param array $elementTexts Array of element texts to assign to the item. 
     *  This follows the format: 
     * <code>
     * array(
      *     [element set name] => array(
      *         [element name] => array(
      *             array('text' => [string], 'html' => [false|true]), 
      *             array('text' => [string], 'html' => [false|true])
      *         ), 
      *         [element name] => array(
      *             array('text' => [string], 'html' => [false|true]), 
      *             array('text' => [string], 'html' => [false|true])
      *         )
      *     ), 
      *     [element set name] => array(
      *         [element name] => array(
      *             array('text' => [string], 'html' => [false|true]), 
      *             array('text' => [string], 'html' => [false|true])
      *         ), 
      *         [element name] => array(
      *             array('text' => [string], 'html' => [false|true]), 
      *             array('text' => [string], 'html' => [false|true])
      *         )
      *     )
      * );
      * </code>
     *  See ActsAsElementText::addElementTextsByArray() for more info.
     * 
     * @param array $fileMetadata Set of metadata options that allow one or more
     * files to be associated with the item.  Includes the following options:
     *  <ul>    
     *      <li>'file_transfer_type' (string = 'Url|Filesystem|Upload' or 
     * Omeka_File_Transfer_Adapter_Interface).  Corresponds to the 
     * $transferStrategy argument for addFiles().</li>
     *      <li>'file_ingest_options' OPTIONAL (array of possible options to pass
     * modify the behavior of the ingest script).  Corresponds to the $options 
     * argument for addFiles().</li>
     *      <li>'files' (array or string) Represents information indicating the file
     * to ingest.  Corresponds to the $files argument for addFiles().</li>
     * </ul>
     * @see ItemBuilder::addFiles()
     * @see ActsAsElementText::addElementTextsByArray()
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
    
    /**
     * Manipulate the item and save it to the database.
     * 
     * The following processes can occur if correct options are passed to the
     * class:
     * 
     * <ol>
     *  <li>Set basic metadata (public/featured, collection, etc.).</li>
     *  <li>Add or replace element texts.</li>
     *  <li>Ingest files to be associated with the item.</li>
     *  <li>Save the item to the database (created if not already exists).</li>
     *  <li>Add associated tags.</li>
     * </ol>
     * 
     * @return void
     **/
    public function run()
    {
        $item = $this->getItem();
        $this->_setProperties($item, $this->_metadata);
        
        if ($item->exists() 
        and array_key_exists('overwriteElementTexts', $this->_metadata)) {
            $this->_replaceElementTexts();
        } else {
            $this->_addElementTexts();
        }
                                   
        // Files are ingested before the item is saved.  That way, ingest 
        // exceptions that bubble up will prevent the item from being saved.  On
        // the other hand, if 'ignore_invalid_files' is set to true, then the 
        // item will be saved as normally.
        if (array_key_exists('files', $this->_fileMetadata)) {
            if (!array_key_exists('file_transfer_type', $this->_fileMetadata)) {
                throw new Exception("Must specify 'file_transfer_type' when attaching files to an item!");
            }
            $this->addFiles(
                $this->_fileMetadata['file_transfer_type'], 
                $this->_fileMetadata['files'], 
                (array)$this->_fileMetadata['file_ingest_options']);
        }

        // Save Item and all of its metadata.  Throw exception if it fails.
        $item->forceSave();

        // Must take place after save().
        if (array_key_exists('tags', $this->_metadata) 
        and !empty($this->_metadata['tags'])) {
            $this->_addTags();
        }
    }
    
    /**
     * Retrieve the item that was created/updated.
     * 
     * @return Item
     **/
    public function getItem()
    {
        return $this->_item;
    }
    
    private function _setProperties($item, $itemMetadata)
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
    
    private function _addElementTexts()
    {
        return $this->_item->addElementTextsByArray($this->_elementTexts);
    }    
        
    private function _replaceElementTexts()
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
    
    private function _addTags()
    {
        // As of 0.10 we still need to tag for a specific entity.
        // This may change in future versions.
        $entityToTag = array_key_exists('tag_entity', $this->_metadata) ?
            $this->_metadata['tag_entity'] : current_user()->Entity;
        $this->_item->addTags($this->_metadata['tags'], $entityToTag);
    }
    
    /**
     * Add files to an item.
     * 
     * @param string|Omeka_File_Ingest_Abstract $transferStrategy
     * This can either be one of the following strings denoting built-in transfer
     * methods: 
     *      'Upload', 'Filesystem', 'Url'
     * Or it could be an implemented Omeka_File_Ingest_Abstract class.
     * 
     * @param string|array $files This can be a single string, an array of strings,
     * or an array of arrays, depending on the parameters that are needed by the 
     * underlying strategy.  Expected parameters for the built in strategies are
     * as follows:
     * <ul>
     *      <li>'Upload' => null|string If a string is given, it represents the 
     * POST parameter name containing the uploaded file(s).  If null is given,
     * all files in the POST will be ingested.</li>
     * 
     *      <li>'Url|Filesystem' => string|array If a string is given, this represents
     * the source identifier of a single file (the URL representing the file, or 
     * the absolute file path, respectively).  If an array is given, it assumes
     * that each entry in the array must be either an array or a string.  If it
     * an array, there are several default keys that may be present:
     *      <ul>
     *          <li>'source' => Any identifier that is appropriate to the transfer
     * strategy in use.  For 'Url', this should be a valid URL.  For 'Filesystem',
     * it must be an absolute path to the source file to be transferred.</li>
     *          <li>'name' => OPTIONAL The filename to give to the transferred
     * file.  This can be any arbitrary filename and will be listed as the 
     * original filename of the file.  This will also be used to generate the 
     * archival filename for the file.  If none is given, this defaults to using
     * the getOriginalFileName() method of the transfer adapter.</li>
     *          <li>'metadata' => OPTIONAL This could contain any metadata that needs to be
     * associated with the file.  This should be indexed in the same fashion
     * as for items.  See ActsAsElementText::addTextsByArray()</li>
     *      </ul></li>
     * </ul>
     * @param array $options OPTIONAL May contain the following flags where
     * appropriate:
     * <ul>
     *      <li>'ignore_invalid_files' => Do not throw exceptions when
     * attempting to ingest invalid files.  Instead, skip to the next file in
     * the list and continue processing.  False by default. (all except Upload).</li>
     *      <li>'ignoreNoFile' => Ignore errors resulting from POSTs that do not 
     * contain uploaded files as expected (only for Upload).</li>
     * </ul>
     * @return array Set of File records ingested.  May be empty if no files 
     * were ingested.
     **/
    public function addFiles($transferStrategy, $files, array $options = array())
    {        
        if ($transferStrategy instanceof Omeka_File_Ingest_Abstract) {
            $ingester = $transferStrategy;
            $ingester->setItem($this->_item);
            $ingester->setOptions($options);
        } else {
            $ingester = Omeka_File_Ingest_Abstract::factory($transferStrategy,
                                                            $this->_item,
                                                            $options);
        }

        $this->_addIngestValidators($ingester);
                
        $fileRecords = $ingester->ingest($files);
        
        // If we are attaching files to a pre-existing item, only save the files.
        if ($this->_item->exists()) {
            $this->_item->saveFiles();
        }
        
        return $fileRecords;
    }
    
    /**
     * Add the default validators for ingested files.  
     * 
     * The default validators are whitelists for file extensions and MIME types,
     * and those lists can be configured via the admin settings form.
     * 
     * These default validators can be disabled by the 'disable_default_file_validation'
     * flag in the settings panel.
     * 
     * Plugins can add/remove/modify validators via the 'file_ingest_validators'
     * filter.
     * 
     * @param Omeka_File_Ingest_Abstract $ingester
     * @return void
     **/
    protected function _addIngestValidators(Omeka_File_Ingest_Abstract $ingester)
    {    
        $validators = get_option('disable_default_file_validation') 
                    ? array()
                    : array(
                        'extension whitelist'=> new Omeka_Validate_File_Extension,
                        'MIME type whitelist'=> new Omeka_Validate_File_MimeType);
        
        $validators = apply_filters('file_ingest_validators', $validators);
        
        // Build the default validators.
        foreach ($validators as $validator) {
            $ingester->addValidator($validator);
        }
    }
}
