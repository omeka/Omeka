<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Adds default behavior associated with the 'public' and 'featured' flags.
 * 
 * @package Omeka\Record\Mixin
 */
class Mixin_PublicFeatured extends Omeka_Record_Mixin_AbstractMixin
{
    private $_wasPublic;
    private $_wasFeatured;
    
    /**
     * Constructor
     * 
     * @param Omeka_Record_AbstractRecord $record The underlying record
     */
    public function __construct($record)
    {
        parent::__construct($record);
        $this->_wasPublic = $this->isPublic();
        $this->_wasFeatured = $this->isFeatured();
    }
    
    /**
     * Returns whether the record is public or not.
     *
     * @return boolean
     */
    public function isPublic()
    {
        $this->setPublic($this->_record->public); // normalize public
        return (boolean)$this->_record->public;
    }
    
    /**
     * Sets whether the record is public or not.
     *
     * @param boolean $flag Whether the record is public or not
     */
    public function setPublic($flag)
    {
        $filter = new Omeka_Filter_Boolean;
        $this->_record->public = $filter->filter($flag);
    }
    
    /**
     * Returns whether the record is featured or not.
     *
     * @return boolean
     */
    public function isFeatured()
    {
        $this->setFeatured($this->_record->featured); // normalize featured
        return (boolean)$this->_record->featured;        
    }
    
    /**
     * Sets whether the record is featured or not.
     *
     * @param boolean $flag Whether the record is featured or not
     */
    public function setFeatured($flag)
    {
        $filter = new Omeka_Filter_Boolean;
        $this->_record->featured = $filter->filter($flag);
    }
    
    public function beforeSave($args)
    {
        $this->setPublic($this->_record->public);
        $this->setFeatured($this->_record->featured);
    }
    
    public function afterSave($args)
    {
        if ($this->isPublic() != $this->_wasPublic) {
            $this->_fireHook('public', $this->isPublic());
        }
    
        if ($this->isFeatured() != $this->_wasFeatured) {
            $this->_fireHook('featured', $this->isFeatured());
        }
        
        $this->_wasPublic = $this->isPublic();
        $this->_wasFeatured = $this->isFeatured();
    }
    
    /**
     * Fires a hooks like 'make_item_public', 'make_collection_not_featured', etc.
     * 
     * @param string $state Currently, 'public' or 'featured'
     * @param boolean $flag
     */
    protected function _fireHook($state, $flag)
    {
        $hookName = $this->_getHookName($state, $flag);
        fire_plugin_hook($hookName, array('record' => $this->_record));
    }
    
    /**
     * Retrieve formatted hooks like 'make_item_public', 'make_collection_not_featured', etc.
     * 
     * @param string $state Currently, 'public' or 'featured'
     * @param boolean $flag
     * @return string The hook name
     */
    protected function _getHookName($state, $flag)
    {
        // e.g., 'item'
        $modelNameForHook = strtolower(get_class($this->_record));
        $action = ($flag ? '' : 'not_') . $state;
        return "make_{$modelNameForHook}_{$action}";
    }
}
