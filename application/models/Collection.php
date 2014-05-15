<?php 
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * A collection and its metadata.
 * 
 * @package Omeka\Record
 */
class Collection extends Omeka_Record_AbstractRecord implements Zend_Acl_Resource_Interface
{
    /**
     * Whether or not the collection is publicly accessible.
     *
     * @var bool
     */
    public $public = 0;
    
    /**
     * Whether or not the collection is featured.
     *
     * @var bool
     */
    public $featured = 0;
    
    /**
     * Date the collection was added.
     *
     * @var string
     */
    public $added;
    
    /**
     * Date the collection was last modified.
     *
     * @var string
     */
    public $modified;
    
    /**
     * ID for the User that created this collection.
     *
     * @var int
     */
    public $owner_id = 0;

    /**
     * Related records.
     *
     * @see Omeka_Record_AbstractRecord::__get
     */
    protected $_related = array(
        'Elements' => 'getElements',
        'ElementTexts'=>'getElementText'
    );

    /**
     * Initialize the mixins.
     */
    protected function _initializeMixins()
    {
        $this->_mixins[] = new Mixin_PublicFeatured($this);
        $this->_mixins[] = new Mixin_Owner($this);
        $this->_mixins[] = new Mixin_ElementText($this);
        $this->_mixins[] = new Mixin_Timestamp($this);
        $this->_mixins[] = new Mixin_Search($this);
    }

    /**
     * Get a property about this collection.
     *
     * Valid properties for a Collection include:
     * * (int) public
     * * (int) featured
     * * (string) added
     * * (string) modified
     * * (int) owner_id
     * * (int) total_items
     * 
     * @param string $property The property to get, always lowercase.
     * @return mixed The value of the property
     */
    public function getProperty($property)
    {
        switch ($property) {
            case 'total_items':
                return $this->totalItems();
            default:
                return parent::getProperty($property);
        }
    }
    
    /**
     * Get the total number of items in this collection.
     * 
     * @return int
     */
    public function totalItems()
    {
        return $this->getDb()->getTable('Item')->count(array('collection' => $this->id));
    }
    
    /**
     * Set the user who added the collection.
     * 
     * Note that this is not to be confused with the collection's "contributors".
     * 
     * @param User $user
     */
    public function setAddedBy(User $user)
    {
        if (!$user->exists()) {
            throw new RuntimeException(__("Cannot associate the collection with an unsaved user."));
        }
        $this->owner_id = $user->id;
    }

    /**
     * Required by Zend_Acl_Resource_Interface.
     *
     * Identifies Collection records as relating to the Collections ACL
     * resource.
     *
     * @return string
     */
    public function getResourceId()
    {
        return 'Collections';
    }

    /**
     * Return whether the collection has at least 1 contributor element text.
     *
     * @return bool
     */
    public function hasContributor()
    {
        return $this->hasElementText('Dublin Core', 'Contributor');
    }

    /**
     * Filter the POST data from the form.
     *
     * Converts public/featured flags to booleans.
     * 
     * @param array $post
     * @return array
     */
    protected function filterPostData($post)
    {
        $options = array('inputNamespace'=>'Omeka_Filter');
        
        // User form input does not allow HTML tags or superfluous whitespace
        $filters = array('public'       => 'Boolean',
                         'featured'     => 'Boolean');
            
        $filter = new Zend_Filter_Input($filters, null, $post, $options);
        $post = $filter->getUnescaped();
        
        return $post;
    }
    
    /**
     * All of the custom code for deleting an collection.
     *
     * Delete the element texts for this record.
     *
     * @return void
     */
    protected function _delete()
    {    
        $this->deleteElementTexts();
        $this->_dissociateItems();
    }

    /**
     * Set items attached to this collection back to "no collection."
     */
    protected function _dissociateItems()
    {
        $db = $this->getDb();
        $db->update($db->Item, array('collection_id' => null),
            array('collection_id = ?' => $this->id));
    }

    /**
     * Before-save hook.
     *
     * Fire the before-save element texts code.
     */
    protected function beforeSave($args)
    {
        if ($args['post']) {
            $post = $args['post'];
            $this->beforeSaveElements($post);
        }
    }

    /**
     * After-save hook.
     *
     * Handle public/private status for search.
     */
    protected function afterSave()
    {
        if (!$this->public) {
            $this->setSearchTextPrivate();
        }
    }

    /**
     * Get a representative file for this Collection.
     *
     * @return File|null
     */
    public function getFile()
    {
        $itemTable = $this->getDb()->getTable('Item');
        $itemArray = $itemTable->findBy(array(
            'collection' => $this->id,
            'hasImage' => true,
            'sort_field' => 'featured',
            'sort_dir' => 'd'
        ), 1);
        if ($itemArray) {
            return ($itemArray[0]->getFile());
        } else {
            return null;
        }
    }
}
