<?php 
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * An item and its metadata.
 * 
 * @package Omeka\Record
 */
class Item extends Omeka_Record_AbstractRecord implements Zend_Acl_Resource_Interface
{
    /**
     * The ID for this Item's ItemType, if any.
     *
     * @var int
     */
    public $item_type_id;

    /**
     * The ID for this Item's Collection, if any.
     *
     * @var int
     */
    public $collection_id;

    /**
     * Whether this Item is featured.
     *
     * @var int
     */
    public $featured = 0;

    /**
     * Whether this Item is publicly accessible.
     *
     * @var int
     */
    public $public = 0;

    /**
     * The date this Item was added.
     *
     * @var string
     */
    public $added;

    /**
     * The date this Item was last modified.
     *
     * @var string
     */
    public $modified;

    /**
     * ID of the User who created this Item.
     *
     * @var int
     */
    public $owner_id;

    /**
     * Records related to an Item.
     *
     * @var array
     */
    protected $_related = array(
        'Collection' => 'getCollection', 
        'TypeMetadata' => 'getTypeMetadata', 
        'Type' => 'getItemType',
        'Tags' => 'getTags',
        'Files' => 'getFiles',
        'Elements' => 'getElements',
        'ItemTypeElements' => 'getItemTypeElements',
        'ElementTexts' => 'getAllElementTexts'
    );
    
    /**
     * Set of non-persistent File objects to attach to the item.
     * 
     * @var array 
     * @see Item::addFile()  
     */
    private $_files = array();

    /**
     * Initialize the mixins.
     */
    protected function _initializeMixins()
    {
        $this->_mixins[] = new Mixin_Tag($this);
        $this->_mixins[] = new Mixin_Owner($this);
        $this->_mixins[] = new Mixin_ElementText($this);
        $this->_mixins[] = new Mixin_PublicFeatured($this);
        $this->_mixins[] = new Mixin_Timestamp($this);
        $this->_mixins[] = new Mixin_Search($this);
    }
    
    // Accessor methods
        
    /**
     * Get this Item's Collection, if any.
     * 
     * @return Collection|null
     */
    public function getCollection()
    {
        $lk_id = (int) $this->collection_id;
        return $this->getTable('Collection')->find($lk_id);            
    }
    
    /**
     * Get the ItemType record associated with this Item.
     * 
     * @return ItemType|null
     */
    public function getItemType()
    {
        if ($this->item_type_id) {
            $itemType = $this->getTable('ItemType')->find($this->item_type_id);
            return $itemType;
        }
    }
    
    /**
     * Get the set of File records associated with this Item.
     * 
     * @return array
     */
    public function getFiles()
    {
        return $this->getTable('File')->findByItem($this->id);
    }

    /**
     * Get a single File associated with this Item, by index.
     *
     * The default is to get the first file.
     *
     * @param integer $index
     * @return File
     */
    public function getFile($index = 0)
    {
        return $this->getTable('File')->findOneByItem($this->id, $index);
    }
    
    /**
     * Get a set of Elements associated with this Item's ItemType.
     *
     * Each one of the Element records that is retrieved should contain all the 
     * element text values associated with it.
     *
     * @uses ElementTable::findByItemType()
     * @return array Element records that are associated with the item type of
     * the item.  This array will be empty if the item does not have an 
     * associated type.
     */
    public function getItemTypeElements()
    {
        $elements = $this->getTable('Element')->findByItemType($this->item_type_id);

        $indexedElements = array();
        foreach ($elements as $element) {
            $indexedElements[$element->name] = $element;
        }
        return $indexedElements;
    }

    /**
     * Get a property for display.
     *
     * @param string $property
     * @return mixed
     */
    public function getProperty($property)
    {
        switch($property) {
            case 'item_type_name':
                if ($type = $this->Type) {
                    return $type->name;
                } else {
                    return null;
                }
            case 'collection_name':
                if ($collection = $this->Collection) {
                    return strip_formatting(metadata($collection, array('Dublin Core', 'Title')));
                } else {
                    return null;
                }
            case 'permalink':
                return record_url($this, null, true);
            case 'has_files':
                return (bool) $this->fileCount();
            case 'file_count':
                return $this->fileCount();
            case 'has_thumbnail':
                return $this->hasThumbnail();
            case 'citation':
                return $this->getCitation();
            default:
                return parent::getProperty($property);
        }
    }

    // End accessor methods
    
    // ActiveRecord callbacks

    /**
     * Before-save hook.
     *
     * @param array $args
     */
    protected function beforeSave($args)
    {
        if ($args['post']) {
            $post = $args['post'];
            
            $this->beforeSaveElements($post);
            
            if (!empty($post['change_type'])) {
                return false;
            }
            
            try {
                $this->_uploadFiles();
            } catch (Omeka_File_Ingest_InvalidException $e) {
                $this->addError('File Upload', $e->getMessage());
            }
        }
    }
    
    /**
     * After-save hook.
     *
     * @param array $args
     */
    protected function afterSave($args)
    {
        if (!$this->public) {
            $this->setSearchTextPrivate();
        }
        
        $this->saveFiles();
        
        if ($args['post']) {
            $post = $args['post'];
            
            // Update file order for this item.
            if (isset($post['order'])) {
                foreach ($post['order'] as $fileId => $fileOrder) {
                    // File order must be an integer or NULL.
                    $fileOrder = (int) $fileOrder;
                    if (!$fileOrder) {
                        $fileOrder = null;
                    }

                    $file = $this->getTable('File')->find($fileId);
                    $file->order = $fileOrder;
                    $file->save();
                }
            }
            
            // Delete files that have been designated by passing an array of IDs 
            // through the form.
            if (isset($post['delete_files']) && ($files = $post['delete_files'])) {
                $this->_deleteFiles($files);
            }
            
            // Save/delete the tags.
            if (array_key_exists('tags-to-add', $post)) {
                $this->addTags($post['tags-to-add']);
                $this->deleteTags($post['tags-to-delete']);
            }
        }
    }

    /**
     * All of the custom code for deleting an item.
     */
    protected function _delete()
    {    
        $this->_deleteFiles();
        $this->deleteElementTexts();
    }
    
    /**
     * Delete files associated with the item.
     * 
     * If the IDs of specific files are passed in, this will delete only those
     * files (e.g. form submission).  Otherwise, it will delete all files 
     * associated with the item.
     * 
     * @uses FileTable::findByItem()
     * @param array $fileIds Optional
     */
    protected function _deleteFiles(array $fileIds = array())
    {           
        $filesToDelete = $this->getTable('File')->findByItem($this->id, $fileIds, 'id');
        
        foreach ($filesToDelete as $fileRecord) {
            $fileRecord->delete();
        }
    }
    
    /**
     * Iterate through the $_FILES array for files that have been uploaded
     * to Omeka and attach each of those files to this Item.
     */
    private function _uploadFiles()
    {
        fire_plugin_hook('before_upload_files', array('item' => $this));
        // Tell it to always try the upload, but ignore any errors if any of
        // the files were not actually uploaded (left form fields empty).
        if (!empty($_FILES['file'])) {
            $files = insert_files_for_item($this, 'Upload', 'file', array('ignoreNoFile' => true));
        }
     }
    
    /**
     * Save all the files that have been associated with this item.
     */
    public function saveFiles()
    {
        if (!$this->exists()) {
            throw new Omeka_Record_Exception(__("Files cannot be attached to an item that is not persistent in the database!"));
        }
        
        foreach ($this->_files as $key => $file) {
            $file->item_id = $this->id;
            $file->save();
            // Make sure we can't save it twice by mistake.
            unset($this->_files[$key]);
        }
    }
    
    /**
     * Filter post data from form submissions.  
     * 
     * @param array Dirty post data
     * @return array Clean post data
     */
    protected function filterPostData($post)
    {
        $options = array('inputNamespace' => 'Omeka_Filter');
        $filters = array(
            // Foreign keys
            'item_type_id'  => 'ForeignKey',
            'collection_id' => 'ForeignKey',

            // Booleans
            'public'   =>'Boolean',
            'featured' =>'Boolean'
        );  
        $filter = new Zend_Filter_Input($filters, null, $post, $options);
        $post = $filter->getUnescaped();

        $bootstrap = Zend_Registry::get('bootstrap');
        $acl = $bootstrap->getResource('Acl');
        $currentUser = $bootstrap->getResource('CurrentUser');
        // check permissions to make public and make featured
        if (!$acl->isAllowed($currentUser, 'Items', 'makePublic')) {
            unset($post['public']);
        }
        if (!$acl->isAllowed($currentUser, 'Items', 'makeFeatured')) {
            unset($post['featured']);
        }
        
        return $post;
    }
    
    /**
     * Get the number of files assigned to this item.
     * 
     * @return int
     */
    public function fileCount()
    {
        $db = $this->getDb();
        $sql = "
        SELECT COUNT(f.id) 
        FROM $db->File f 
        WHERE f.item_id = ?";
        return (int) $db->fetchOne($sql, array((int) $this->id));
    }
    
    /**
     * Get the previous Item in the database.
     *
     * @uses ItemTable::findPrevious()
     * @return Item|false
     */
    public function previous()
    {
        return $this->getDb()->getTable('Item')->findPrevious($this);
    }
    
    /**
     * Get the next Item in the database.
     * 
     * @uses ItemTable::findNext()
     * @return Item|false
     */
    public function next()
    {
        return $this->getDb()->getTable('Item')->findNext($this);
    }

    /**
     * Determine whether or not the Item has a File with a thumbnail image
     * (or any derivative image).
     * 
     * @return bool
     */
    public function hasThumbnail()
    {
        $db = $this->getDb();
        
        $sql = "
        SELECT COUNT(f.id) 
        FROM $db->File f 
        WHERE f.item_id = ? 
        AND f.has_derivative_image = 1";
        
        $count = $db->fetchOne($sql, array((int) $this->id));
            
        return $count > 0;
    }
    
    /**
     * Return a valid citation for this item.
     *
     * Generally follows Chicago Manual of Style note format for webpages. 
     * Implementers can use the item_citation filter to return a customized 
     * citation.
     *
     * @return string
     */
    function getCitation()
    {
        $citation = '';
        
        $creators = metadata($this, array('Dublin Core', 'Creator'), array('all' => true));
        // Strip formatting and remove empty creator elements.
        $creators = array_filter(array_map('strip_formatting', $creators));
        if ($creators) {
            switch (count($creators)) {
                case 1:
                    $creator = $creators[0];
                    break;
                case 2:
                    /// Chicago-style item citation: two authors
                    $creator = __('%1$s and %2$s', $creators[0], $creators[1]);
                    break;
                case 3:
                    /// Chicago-style item citation: three authors
                    $creator = __('%1$s, %2$s, and %3$s', $creators[0], $creators[1], $creators[2]);
                    break;
                default:
                    /// Chicago-style item citation: more than three authors
                    $creator = __('%s et al.', $creators[0]);
            }
            $citation .= "$creator, ";
        }
        
        $title = strip_formatting(metadata($this, array('Dublin Core', 'Title')));
        if ($title) {
            $citation .= "&#8220;$title,&#8221; ";
        }
        
        $siteTitle = strip_formatting(option('site_title'));
        if ($siteTitle) {
            $citation .= "<em>$siteTitle</em>, ";
        }
        
        $accessed = format_date(time(), Zend_Date::DATE_LONG);
        $url = html_escape(record_url($this, null, true));
        /// Chicago-style item citation: access date and URL
        $citation .= __('accessed %1$s, %2$s.', $accessed, $url);
        
        return apply_filters('item_citation', $citation, array('item' => $this));
    }
    
    /**
     * Associate an unsaved (new) File record with this Item.
     * 
     * These File records will not be persisted in the database until the item
     * is saved or saveFiles() is invoked.
     * 
     * @see Item::saveFiles()
     * @param File $file
     */
    public function addFile(File $file)
    {
        if ($file->exists()) {
            throw new Omeka_Record_Exception(__("Cannot add an existing file to an item!"));
        }
        
        if (!$file->isValid()) {
            throw new Omeka_Record_Exception(__("File must be valid before it can be associated with an item!"));
        }
        
        $this->_files[] = $file;
    }

    /**
     * Identify Item records as relating to the Items ACL resource.
     *
     * Required by Zend_Acl_Resource_Interface.
     *
     * @return string
     */
    public function getResourceId()
    {
        return 'Items';
    }
    
    /**
     * Validate this item.
     */
    protected function _validate() {
        $db = $this->getDb();
        if (null !== $this->item_type_id && !$db->getTable('ItemType')->exists($this->item_type_id)) {
            $this->addError('item_type_id', __('Invalid item type.'));
        }
        if (null !== $this->collection_id && !$db->getTable('Collection')->exists($this->collection_id)) {
            $this->addError('collection_id', __('Invalid collection.'));
        }
    }
}
