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
    public $item_type_id;
    public $collection_id;
    public $featured = 0;
    public $public = 0;    
    public $added;
    public $modified;
    public $owner_id;
        
    protected $_related = array('Collection'=>'getCollection', 
                                'TypeMetadata'=>'getTypeMetadata', 
                                'Type'=>'getItemType',
                                'Tags'=>'getTags',
                                'Files'=>'getFiles',
                                'Elements'=>'getElements',
                                'ItemTypeElements'=>'getItemTypeElements',
                                'ElementTexts'=>'getElementText');
    
    /**
     * @var array Set of non-persistent File objects to attach to the item.
     * @see Item::addFile()  
     */
    private $_files = array();
    
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
     * @return null|Collection
     */
    public function getCollection()
    {
        $lk_id = (int) $this->collection_id;
        return $this->getTable('Collection')->find($lk_id);            
    }
    
    /**
     * Retrieve the ItemType record associated with this Item.
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
     * Retrieve the set of File records associated with this Item.
     * 
     * @return array
     */
    public function getFiles()
    {
        return $this->getTable('File')->findByItem($this->id);
    }
    
    /**
     * Retrieve a set of elements associated with the item type of the item.
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
        /* My hope is that this will retrieve a set of elements, where each
        element contains an array of all the values for that element */
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
     * Logic for after the record has been saved.
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
     *
     * @return void
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
     * @return void
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
     * 
     * @param string
     * @return void
     */
    private function _uploadFiles()
    {
        fire_plugin_hook('before_upload_files', array('item' => $this));
        // Tell it to always try the upload, but ignore any errors if any of
        // the files were not actually uploaded (left form fields empty).
        if (!empty($_FILES['file'])) {
            $files = insert_files_for_item($this, 'Upload', 'file', array('ignoreNoFile'=>true));
        }
     }
    
    /**
     * Save all the files that have been associated with this item.
     * 
     * @return boolean
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
        $options = array('inputNamespace'=>'Omeka_Filter');
        $filters = array(                         
                         // Foreign keys
                         'type_id'       => 'ForeignKey',
                         'collection_id' => 'ForeignKey',
                         
                         // Booleans
                         'public'   =>'Boolean',
                         'featured' =>'Boolean');  
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
     * Retrieve the number of files assigned to this item.
     * 
     * @return boolean
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
     * Retrieve the previous Item in the database.
     *
     * @uses ItemTable::findPrevious()
     * @return Item|false
     */
    public function previous()
    {
        return $this->getDb()->getTable('Item')->findPrevious($this);
    }
    
    /**
     * Retrieve the next Item in the database.
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
     * @return boolean
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
                    $creator = "{$creators[0]} and {$creators[1]}";
                    break;
                case 3:
                    $creator = "{$creators[0]}, {$creators[1]}, and {$creators[2]}";
                    break;
                default:
                    $creator = "{$creators[0]} et al.";
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
        
        $accessed = date('F j, Y');
        $url = html_escape(record_url($this, null, true));
        $citation .= "accessed $accessed, $url.";
        
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
     * @return void
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
     * Required by Zend_Acl_Resource_Interface.
     *
     * Identifies Item records as relating to the Items ACL resource.
     *
     * @return string
     */
    public function getResourceId()
    {
        return 'Items';
    }
}
