<?php 
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @subpackage Models
 * @author CHNM
 */

/**
 * Represents an item and its metadata.
 *
 * @package Omeka
 * @subpackage Models
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Item extends Omeka_Record implements Zend_Acl_Resource_Interface
{        
    public $item_type_id;
    public $collection_id;
    public $featured = 0;
    public $public = 0;    
    public $added;
    public $modified;
    
        
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
        $this->_mixins[] = new Taggable($this);
        $this->_mixins[] = new Relatable($this);
        $this->_mixins[] = new ActsAsElementText($this);
        $this->_mixins[] = new PublicFeatured($this);
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
        return $this->getTable('File')->findByItem($this->id, null, 'order');
    }
    
    /**
     * @return array Set of ElementText records.
     */
    public function getElementText()
    {
        return $this->getElementTextRecords();
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
        
        return $elements;
    }
    
    /**
     * Retrieve the User record that represents the creator of this Item.
     * 
     * @return User
     */
    public function getUserWhoCreated()
    {
        $creator = $this->getRelatedEntities('added');
        
        if (is_array($creator)) {
            $creator = current($creator);
        }
        
        return $creator->User;
    }
        
    // End accessor methods
    
    // ActiveRecord callbacks
    
    /**
     * Stop the form submission if we are using the non-JS form to change the 
     * Item Type or add files.
     *
     * Also, do not allow people to change the public/featured status of an item
     * unless they have 'makePublic' or 'makeFeatured' permissions.
     *
     * @return void
     */
    protected function beforeSaveForm($post)
    {        
        $this->beforeSaveElements($post);
        
        if (!empty($post['change_type'])) {
            return false;
        }
        if (!empty($post['add_more_files'])) {
            return false;
        }
        
        try {
            $this->_uploadFiles();
        } catch (Omeka_File_Ingest_InvalidException $e) {
            $this->addError('File Upload', $e->getMessage());
        }
    }
    
    /**
     * Things to do in the beforeSave() hook:
     * 
     * Explicitly set the modified timestamp.
     * 
     * @return void
     */
    protected function beforeSave()
    {
        $this->modified = Zend_Date::now()->toString(self::DATE_FORMAT);
        $booleanFilter = new Omeka_Filter_Boolean();
        $this->public = $booleanFilter->filter($this->public);
        $this->featured = $booleanFilter->filter($this->featured);
    }
    
    /**
     * Modify the user's tags for this item based on form input.
     * 
     * Checks the 'tags' field from the post and applies all the differences in
     * the list of tags for the current user.
     * 
     * @uses Taggable::applyTagString()
     * @param ArrayObject
     * @return void
     */
    protected function _modifyTagsByForm($post)
    {
        // Change the tags (remove some, add some)
        if (array_key_exists('my-tags-to-add', $post)) {
            $user = Omeka_Context::getInstance()->getCurrentUser();
            if ($user) {
                $this->addTags($post['my-tags-to-add'], $user);
                $this->deleteTags($post['my-tags-to-delete'], $user);                
                $this->deleteTags($post['other-tags-to-delete'], $user, $this->userHasPermission('untagOthers'));
            }
        }        
    }
        
    /**
     * Save all metadata for the item that has been received through the form.
     *
     * All of these have to run after the Item has been saved, because all 
     * require that the Item is already persistent in the database.
     * 
     * @return void
     */
    protected function afterSaveForm($post)
    {        
        // Update file order for this item.
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
        
        // Delete files that have been designated by passing an array of IDs 
        // through the form.
        if (isset($post['delete_files']) && ($files = $post['delete_files'])) {
            $this->_deleteFiles($files);
        }
        
        $this->_modifyTagsByForm($post);
    }
    
    /**
     * Things to do in the afterSave() hook:
     * 
     * Save all files that had been associated with the item.
     * 
     * @return void
     */
    protected function afterSave()
    {
        $this->saveFiles();
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
        $filesToDelete = $this->getTable('File')->findByItem($this->id, $fileIds);
        
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
        fire_plugin_hook('before_upload_files', $this);
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
            $file->forceSave();
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
    protected function filterInput($post)
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
        
        // check permissions to make public and make featured
        if (!$this->userHasPermission('makePublic')) {
            unset($post['public']);
        }
        if (!$this->userHasPermission('makeFeatured')) {
            unset($post['featured']);
        }
        
        return $post;
    }
    
    /**
     * Whether or not the Item has files associated with it.
     * 
     * @return boolean
     */
    public function hasFiles()
    {
        $db = $this->getDb();
        $sql = "
        SELECT COUNT(f.id) 
        FROM $db->File f 
        WHERE f.item_id = ?";
        $count = (int) $db->fetchOne($sql, array((int) $this->id));
        return $count > 0;
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
