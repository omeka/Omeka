<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2009
 */
class ItemBuilder extends Omeka_Record_Builder
{
    const TAGS = 'tags';
    const TAG_ENTITY = 'tag_entity';
    const FILES = 'files';
    const FILE_TRANSFER_TYPE = 'file_transfer_type';
    const FILE_INGEST_OPTIONS = 'file_ingest_options';
    const FILE_INGEST_VALIDATORS_FILTER = 'file_ingest_validators';
    const ITEM_TYPE_NAME = 'item_type_name';
    const ITEM_TYPE_ID = 'item_type_id';
    const COLLECTION_ID = 'collection_id';
    const OVERWRITE_ELEMENT_TEXTS = 'overwriteElementTexts';
    
    /**
     * @internal Constant could not be called 'PUBLIC' because it is a reserved
     * keyword.
     */
    const IS_PUBLIC = 'public';
    const IS_FEATURED = 'featured';
    
    protected $_recordClass = 'Item';
    protected $_settableProperties = array(
        self::ITEM_TYPE_ID, 
        self::COLLECTION_ID, 
        self::IS_PUBLIC, 
        self::IS_FEATURED
    );
    
    private $_elementTexts = array();
    private $_fileMetadata = array();
    
    /**
     * Set the element texts for the item.
     * 
     * @param array $elementTexts
     */    
    public function setElementTexts(array $elementTexts)
    {
        $this->_elementTexts = $elementTexts;
    }
    
    /**
     * Set the file metadata for the item.
     * 
     * @param array $fileMetadata
     */
    public function setFileMetadata(array $fileMetadata)
    {
        $this->_fileMetadata = $fileMetadata;
    }
    
    /**
     * Overrides setRecordMetadata() to allow setting the item type by name
     * instead of ID.
     * 
     * @param array $metadata
     * @return void
     */
    public function setRecordMetadata(array $metadata)
    {
        // Determine the Item Type ID from the name.
        if (array_key_exists(self::ITEM_TYPE_NAME, $metadata)) {
            $itemType = $this->_db->getTable('ItemType')
                                  ->findBySql('name = ?', 
                                              array($metadata[self::ITEM_TYPE_NAME]), 
                                              true);
            if(!$itemType) {
                throw new Omeka_Record_Builder_Exception( "Invalid type named {$metadata[self::ITEM_TYPE_NAME]} provided!");
            }            
            $metadata[self::ITEM_TYPE_ID] = $itemType->id;
        }
        return parent::setRecordMetadata($metadata);
    }
    
    /**
     * Add element texts to a record.
     */            
    private function _addElementTexts()
    {
        return $this->_record->addElementTextsByArray($this->_elementTexts);
    }    
    
    /**
     * Replace all the element texts for existing element texts.
     */    
    private function _replaceElementTexts()
    {
        $item = $this->_record;
        // If this option is set, it will loop through the $elementTexts provided,
        // find each one and manually update it (provided it exists).
        // The rest of the element texts will get added as per usual.
        foreach ($this->_elementTexts as $elementSetName => $textArray) {
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
    
    /**
     * Add tags to an item (must exist in database).
     */
    private function _addTags()
    {
        // As of 0.10 we still need to tag for a specific entity.
        // This may change in future versions.
        $metadata = $this->getRecordMetadata();
        if (array_key_exists(self::TAG_ENTITY, $metadata)) {
            $entity = $metadata[self::TAG_ENTITY];
        } else if ($currentUser = Omeka_Context::getInstance()->getCurrentUser()){
            $entity = $currentUser->Entity;
        } else {
            throw new Omeka_Record_Builder_Exception(__("Cannot add tags to an item if no Entity is available to tag."));
        }
        $this->_record->addTags($metadata[self::TAGS], $entity);
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
     */
    public function addFiles($transferStrategy, $files, array $options = array())
    {        
        if ($transferStrategy instanceof Omeka_File_Ingest_Abstract) {
            $ingester = $transferStrategy;
            $ingester->setItem($this->_record);
            $ingester->setOptions($options);
        } else {
            $ingester = Omeka_File_Ingest_Abstract::factory($transferStrategy,
                                                            $this->_record,
                                                            $options);
        }

        $this->_addIngestValidators($ingester);
                
        $fileRecords = $ingester->ingest($files);
        
        // If we are attaching files to a pre-existing item, only save the files.
        if ($this->_record->exists()) {
            $this->_record->saveFiles();
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
     */
    protected function _addIngestValidators(Omeka_File_Ingest_Abstract $ingester)
    {    
        $validators = get_option(File::DISABLE_DEFAULT_VALIDATION_OPTION) 
                    ? array()
                    : array(
                        'extension whitelist'=> new Omeka_Validate_File_Extension,
                        'MIME type whitelist'=> new Omeka_Validate_File_MimeType);
        
        $validators = apply_filters(self::FILE_INGEST_VALIDATORS_FILTER, $validators);
        
        // Build the default validators.
        foreach ($validators as $validator) {
            $ingester->addValidator($validator);
        }
    }
    
    protected function _beforeBuild(Omeka_Record $record)
    {
        if ($this->_record->exists() 
        and array_key_exists(self::OVERWRITE_ELEMENT_TEXTS, $this->getRecordMetadata())) {
            $this->_replaceElementTexts();
        } else {
            $this->_addElementTexts();
        }
        
        // Files are ingested before the item is saved.  That way, ingest 
        // exceptions that bubble up will prevent the item from being saved.  On
        // the other hand, if 'ignore_invalid_files' is set to true, then the 
        // item will be saved as normally.
        if (array_key_exists(self::FILES, $this->_fileMetadata)) {
            if (!array_key_exists(self::FILE_TRANSFER_TYPE, $this->_fileMetadata)) {
                throw new Omeka_Record_Builder_Exception(__("Must specify a file transfer type when attaching files to an item!"));
            }
            $this->addFiles(
                $this->_fileMetadata[self::FILE_TRANSFER_TYPE], 
                $this->_fileMetadata[self::FILES], 
                (array)$this->_fileMetadata[self::FILE_INGEST_OPTIONS]);
        }
    }
    
    /**
     * Add tags to the item.
     */
    protected function _afterBuild(Omeka_Record $record)
    {
        $metadata = $this->getRecordMetadata();
        // Must take place after save().
        if (array_key_exists(self::TAGS, $metadata) 
        and !empty($metadata[self::TAGS])) {
            $this->_addTags();
        }
    }    
}
