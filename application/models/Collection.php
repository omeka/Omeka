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
     * @var boolean Whether or not the collection is publicly accessible.
     */
    public $public = 0;
    
    /**
     * @var boolean Whether or not the collection is featured.
     */
    public $featured = 0;
    
    /**
     * @var string
     */
    public $added;
    
    /**
     * @var string
     */
    public $modified;
    
    /**
     * @var integer
     */
    public $owner_id = 0;
    
    protected $_related = array('Elements'=>'getElements',
                                'ElementTexts'=>'getElementText');
    
    /**
     * Get a property about this collection.
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
     * Determine the total number of items associated with this collection.
     * 
     * @return integer
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
     * Returns whether or not the collection has at least 1 contributor element text
     *
     * @return boolean
     */
    public function hasContributor()
    {
        return $this->hasElementText('Dublin Core', 'Contributor');
    }

    /**
     * Initialize the mixins
     *
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
     * @return void
     */
    protected function _delete()
    {    
        $this->deleteElementTexts();
    }
    
    protected function beforeSave($args)
    {
        if ($args['post']) {
            $post = $args['post'];
            $this->beforeSaveElements($post);
        }
    }
    
    protected function afterSave()
    {
        if (!$this->public) {
            $this->setSearchTextPrivate();
        }
    }
}