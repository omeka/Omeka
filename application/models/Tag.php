<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * A tag and its metadata.
 * 
 * @package Omeka\Record
 */
class Tag extends Omeka_Record_AbstractRecord { 
    
    public $name;
    
    public function __toString() {
        return $this->name;
    }
    
    /**
     * Must also delete the taggings associated with this tag
     *
     * @return void
     */
    protected function _delete()
    {
        $taggings = $this->getDb()
                         ->getTable('RecordsTags')
                         ->findBySql('tag_id = ?', array((int) $this->id));
        
        foreach ($taggings as $tagging) {
            $tagging->delete();
        }
    }
    
    protected function _validate()
    {
        if (trim($this->name) == '') {
            $this->addError('name', __('Tags must be given a name.'));
        }
        
        if (!$this->fieldIsUnique('name')) {
            $this->addError('name', __('That name is already taken for this tag.'));
        }
    }
    
    /**
     * The check for unique tag names must take into account CASE SENSITIVITY, 
     * which is accomplished via COLLATE utf8_bin sql
     *
     * @return bool
     */
    protected function fieldIsUnique($field, $value = null)
    {
        if ($field != 'name') {
            return parent::fieldIsUnique($field, $value);
        } else {
            $db = $this->getDb();
            $sql = "
            SELECT id 
            FROM $db->Tag 
            WHERE name COLLATE utf8_bin LIKE ?";
            $res = $db->query($sql, array($value ? $value : $this->name));
            return (!is_array($id = $res->fetch())) || ($this->exists() and $id['id'] == $this->id);
        }
    }
    
    /**
     * Rename a tag.
     *
     * Any records tagged with the "old" tag will be tagged with each
     * of the tags given in $new_names. The original tag will be
     * deleted (unless it is given as one of the $new_names).
     *
     * @param array $new_names Names of the tags this one should be
     *  renamed to.
     * @return void
     */
    public function rename($new_names) 
    {
        $taggings = $this->getTable('RecordsTags')->findBy(array('tag' => $this->name));
        $keepOldTaggings = false;

        // If the current tag is in the new tag list, we don't need
        // to do anything to it or its taggings.
        if (in_array($this->name, $new_names)) {
            $new_names = array_diff($new_names, array($this->name));
            
            // If the current name was the only new name, stop.
            if (!count($new_names)) {
                return true;
            }

            $keepOldTaggings = true;
        // Otherwise, we need to delete the old tag.
        } else {
            $this->delete();
        }
        
        // Switch the existing taggings to the first of the new names,
        // and create new taggings for the remainder.
        foreach ($new_names as $key => $new_name) {
            $new_tag = $this->getTable()->findOrNew($new_name);
            $new_tag_id = $new_tag->id;
                        
            foreach ($taggings as $tagging) {
                // After the first pass, or if we didn't delete the
                // original tag, operate on new copies of the taggings
                if ($key > 0 || $keepOldTaggings) {
                    $tagging = clone $tagging;
                }
                
                $tagging->tag_id = $new_tag_id;
                
                try {
                    $tagging->save();
                } catch (Zend_Db_Exception $e) {
                    // If we couldn't save, it's because this tagging
                    // already exists, so we should delete it.
                    $tagging->delete();
                }
            }
        }
    }
}
