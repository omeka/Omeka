<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

require_once 'Collection.php';
require_once 'ItemType.php';
require_once 'User.php';
require_once 'File.php';
require_once 'Tag.php';
require_once 'Taggable.php';
require_once 'Taggings.php';
require_once 'Element.php';
require_once 'Relatable.php';
require_once 'ItemTable.php';
require_once 'ItemPermissions.php';    

/**
 * @package Omeka
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class Item extends Omeka_Record
{        
    public $item_type_id;
    public $collection_id;
    public $featured = 0;
    public $public = 0;    
        
    protected $_related = array('Collection'=>'getCollection', 
                                'TypeMetadata'=>'getTypeMetadata', 
                                'Type'=>'getItemType',
                                'Tags'=>'getTags',
                                'Files'=>'getFiles',
                                'Elements'=>'getElements',
                                'ItemTypeElements'=>'getItemTypeElements',
                                'ItemsElements'=>'getItemsElements');
    
    protected function construct()
    {
        $this->_mixins[] = new Taggable($this);
        $this->_mixins[] = new Relatable($this);
    }
    
    // Accessor methods
        
    /**
     * @return null|Collection
     **/
    public function getCollection()
    {
        $lk_id = (int) $this->collection_id;
        return $this->getTable('Collection')->find($lk_id);            
    }
    
    /**
     * Retrieve the ItemType record associated with this Item.
     * 
     * @return ItemType|null
     **/
    public function getItemType()
    {
        $itemType = $this->getTable('ItemType')->find($this->item_type_id);
        return $itemType;
    }
    
    /**
     * Retrieve the set of File records associated with this Item.
     * 
     * @return array
     **/
    public function getFiles()
    {
        return $this->getTable('File')->findByItem($this->id);
    }
    
    /**
     * Retrieve the set of Element records associated with this Item.
     * 
     * @return array
     **/
    public function getElements()
    {
        return $this->getTable('Element')->findByItem($this->id);
    }
    
    /**
     * Retrieve all the ItemsElements records associated with the item.
     * 
     * @return array Set of ItemsElements records
     **/
    public function getItemsElements()
    {
        return $this->getTable('ItemsElements')->findByItem($this->id);
    }
    
    /**
     * Retrieve a set of elements associated with the item type of the item.
     *
     * Each one of the Element records that is retrieved should contain all the 
     * element text values associated with it.
     *
     * @see item_type_elements_form()
     * @uses ElementTable::findByItemType()
     * @return array Element records that are associated with the item type of
     * the item.  This array will be empty if the item does not have an 
     * associated type.
     **/    
    public function getItemTypeElements()
    {    
        /* My hope is that this will retrieve a set of elements, where each
        element contains an array of all the values for that element */
        $elements = $this->getTable('Element')->findByItemType($this->item_type_id);
            
        return $this->getTable('Element')->assignTextToElements($elements, $this->ItemsElements);

        return $elements;
    }
    
    /**
     * Retrieve the User record that represents the creator of this Item.
     * 
     * @return User
     **/
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
     * Stop the form submission if we are using the non-JS form to change the type or add files
     *
     * Also, do not allow people to change the public/featured status of an item unless they got permission
     *
     * @return void
     **/
    protected function beforeSaveForm(&$post)
    {
        $this->_beforeSaveElements($post);
        
        if (!empty($post['change_type'])) {
            return false;
        }
        if (!empty($post['add_more_files'])) {
            return false;
        }
        if (!$this->userHasPermission('makePublic')) {
            unset($post['public']);
        }
        if (!$this->userHasPermission('makeFeatured')) {
            unset($post['featured']);
        }
    }
    
    /**
     * @see Item::beforeSaveForm()
     * 
     * @param string
     * @return void
     **/
    protected function _beforeSaveElements(&$post)
    {
        $this->_elementsToSave = $this->_getElementsFromPost($post);
        $this->_validateElements($this->_elementsToSave);        
    }
    
    /**
     * For now, only trim the text in each one. 
     * 
     * @todo Cast all of the text fields to strings to prevent injection of
     * badness.
     * @see Item::_getElementsFromPost()
     * @param ArrayObject
     * @return void
     **/
    protected function _filterInputForElements(&$post)
    {        
        // Trim the text for all the POST'ed elements
        foreach ($post['Elements'] as $key => $element) {
            $post['Elements'][$key] = array_map('trim', $element);
        }
    }
    
    /**
     * The POST should have a key called "Elements" that contains an array
     * that is keyed to an element's ID.  That array should contain all the 
     * text values for that element. For example:
     *
     *      * Elements:
     *          * 1:
     *              * 0: 'Foobar'
     *              * 1: 'Baz'
     * 
     * @todo May want to throw an Exception if an element in the POST doesn't
     * actually exist.
     * @param array
     * @return array Set of Element records.
     **/
    protected function _getElementsFromPost($post)
    {
        $elementPost = $post['Elements'];
        $elements = array();
        $table = $this->getDb()->getTable('Element');
        foreach ($elementPost as $id => $texts) {
            $element = $table->find((int) $id);
            if($element instanceof Element) {
                foreach ($texts as $key => $text) {
                    $textRecord = new ItemsElements;
                    $textRecord->text = $text;
                    $element->addText($textRecord);
                }
                $elements[] = $element;
            }
        }
        
        return $elements;
    }
    
    /**
     * Validate all the elements one by one.  This is potentially a lot slower
     * than batch processing the form, but it gives the added bonus of being 
     * able to encapsulate the logic for validation of Elements.
     * 
     * @see Item::_beforeSaveElements()
     * @param array Set of Element records.
     * @return void
     **/
    protected function _validateElements($elements)
    {
        foreach ($elements as $key => $element) {
            if(!$element->isValid()) {
                $this->addErrorsFrom($element);
            }
        }
    }
    
    /**
     * @deprecated
     * @return void
     **/
    private function deleteFiles($ids = null) 
    {
        if (!is_array($ids)) {
            return false;
        }
        
        // Retrieve file objects so that we have the benefit of the plugin hooks
        // Oops, this will allow for deleting files from other items (bug!)
        foreach ($ids as $file_id) {
            $file = $this->getTable('File')->find($file_id);
            $file->delete();
        }        
    }
    
    /**
     * Remove a specific tag from any user.  This corresponds to a 'remove_tag'
     * form input that contains the ID of the tag to delete.
     *
     * @since 6/10/08 'remove_item_tag' hook should pass the Item object, not
     * the User object. 
     * @param integer
     * @return void
     **/
    protected function _removeTagByForm($tagId)
    {        
        // Only proceed if the user has permission to untag other users
        if ($this->userHasPermission('untagOthers')) {
            
            // Find the tag instance we want to delete
            $tagToDelete = $this->getTable('Tag')->find($tagId);
            $user = Omeka_Context::getInstance()->getCurrentUser();
            
            if ($tagToDelete) {
                // The remove_item_tag hook is passed the name of the tag as 
                // well as the Item record
                fire_plugin_hook('remove_item_tag',  $tagToDelete->name, $item);
                
                //Delete all instances of this tag for this Item
                $this->deleteTags($tagToDelete, null, true);
            }            
        }
    }
    
    /**
     * @uses Taggable::applyTagString()
     * 
     * @param ArrayObject
     * @return void
     **/
    protected function _modifyTagsByForm($post)
    {
        // Change the tags (remove some, add some)
        if (array_key_exists('tags', $post)) {
            $user = Omeka_Context::getInstance()->getCurrentUser();
            $entity = $user->Entity;
            if ($entity) {
                $this->applyTagString($post['tags'], $entity);
            }
        }        
    }
    
    /**
     * Fire a plugin hook if the Item has had it's status changed to 'public'.
     * 
     * @todo All special hooks that fire after a form has been saved should go
     * here.  For example, 'make_item_featured', etc.
     * @param ArrayObject
     * @return void
     **/
    protected function _pluginHooksAfterSaveForm($post)
    {
        // Fire a plugin hook specifically for items that have had their 
        // 'public' status changed
        if (isset($post['public']) && ($this->public == '1')) {
            fire_plugin_hook('make_item_public', $this);
        }        
    }
    
    /**
     * Save all metadata for the item that has been received through the form.
     *
     * All of these have to run after the Item has been saved, because all 
     * require that the Item is already persistent in the database.
     * 
     * @return void
     **/
    public function afterSaveForm($post)
    {
        $this->_saveFiles();
        
        $this->saveElementText($this->_elementsToSave);
        
        // Remove a single tag based on the form submission.
        $this->_removeTagByForm((int) $post['remove_tag']);
        
        // Delete files that have been designated by passing an array of IDs 
        // through the form.
        $this->deleteFiles($post['delete_files']);
        
        $this->_modifyTagsByForm($post);
                
        $this->_pluginHooksAfterSaveForm($post);
    }
        
    /**
     * All of the custom code for deleting an item.
     *
     * @return void
     **/
    protected function _delete()
    {    
        $this->_deleteFiles();
        $this->_deleteElementText();
    }
    
    /**
     * @todo Combine this with Item::deleteFiles() in such a way that it can
     * be used to delete files based on a form submission OR all files when
     * the item itself is deleted.
     * @see Item::deleteFiles()
     * @param string
     * @return void
     **/
    protected function _deleteFiles()
    {        
        foreach ($this->Files as $file) {
            $file->delete();
        }        
    }
    
    /**
     * Delete all element text associated with this Item.
     * 
     * @see Item::_delete()
     * @todo Not implemented yet.
     * @return void
     **/
    protected function _deleteElementText()
    {
        
    }
    
    /**
     * Save a set of elements text.
     * 
     * @see Item::afterSaveForm()
     * @uses Element::saveTextFor()
     * @param array Set of Element records
     * @return void
     **/
    public function saveElementText($elements)
    {
        foreach ($elements as $index => $element) {
            $element->saveTextFor($this);
        }
    }
    
    /**
     * Iterate through the $_FILES array for files that have been uploaded
     * to Omeka and attach each of those files to this Item.
     * 
     * @param string
     * @return void
     * @throws Omeka_Upload_Exception
     **/
    private function _saveFiles()
    {
        
        if (!empty($_FILES["file"]['name'][0])) {            
            
            File::handleUploadErrors('file');
            //Handle the file uploads
            foreach( $_FILES['file']['error'] as $key => $error ) { 
                try {
                    $file = new File();
                    $file->upload('file', $key);
                    $file->item_id = $this->id;
                    $file->save();
                    fire_plugin_hook('after_upload_file', $file, $this);
                } catch(Exception $e) {
                    if (!$file->exists()) {
                        $file->unlinkFile();
                    }
                    throw $e;
                }
            }
        }
    }
    
    /**
     * Filter input from form submissions.  
     * 
     * @param array Dirty array.
     * @return array Clean array.
     **/
    protected function filterInput($input)
    {
        $options = array('inputNamespace'=>'Omeka_Filter');
        
        $filters = array(                         
                         // Foreign keys
                         'type_id'       => 'ForeignKey',
                         'collection_id' => 'ForeignKey',
                         
                         // Booleans
                         'public'   =>'Boolean',
                         'featured' =>'Boolean');
            
        $filter = new Zend_Filter_Input($filters, null, $input, $options);

        $clean = $filter->getUnescaped();
        
        //Now handle proper parsing of the date fields
        
        // I couldn't get this to jive with Zend's thing so screw them
        $dateFilter = new Omeka_Filter_Date;
        
        if ($clean['date_year']) {
            $clean['date'] = $dateFilter->filter($clean['date_year'], 
                                                 $clean['date_month'], 
                                                 $clean['date_day']);
        }
        
        if ($clean['coverage_start_year']) {
            $clean['temporal_coverage_start'] = $dateFilter->filter($clean['coverage_start_year'], 
                                                                    $clean['coverage_start_month'], 
                                                                    $clean['coverage_start_day']);
        }
        
        if ($clean['coverage_end_year']) {
            $clean['temporal_coverage_end'] = $dateFilter->filter($clean['coverage_end_year'], 
                                                                  $clean['coverage_end_month'], 
                                                                  $clean['coverage_end_day']);            
        }
        
        $this->_filterInputForElements($clean);
                
        // Now, happy shiny user input
        return $clean;        
    }
    
    /**
     * Whether or not the Item has files associated with it.
     * 
     * @return boolean
     **/
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
     * Easy facade for the Item class so that it almost acts like an iterator.
     *
     * @return Item|false
     **/
    public function previous()
    {
        return $this->getDb()->getTable('Item')->findPrevious($this);
    }
    
    /**
     * Retrieve the Item that is next in the database after this Item.
     * 
     * @return Item|false
     **/
    public function next()
    {
        return $this->getDb()->getTable('Item')->findNext($this);
    }
    
    //Everything past this is elements of the old code that may be changed or deprecated
        
    /**
     * Whether or not the Item has a File with derivative images (like thumbnails).
     * 
     * @return boolean
     **/
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
}