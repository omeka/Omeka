<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * @package Omeka
 * @subpackage Models
 * @author CHNM
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Tag extends Omeka_Record { 
    
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
                         ->getTable('Taggings')
                         ->findBySql('tag_id = ?', array((int) $this->id));
        
        foreach ($taggings as $tagging) {
            $tagging->delete();
        }
    }
    
    /**
     * Delete only the taggings entries for this specific tag/entity combination
     *
     * @return void
     */
    protected function deleteForEntity(Entity $entity)
    {
        $taggings = $this->getDb()
                         ->getTable('Taggings')
                         ->findBySql('entity_id = ? AND tag_id = ?', 
                                     array( (int)$entity->id, (int) $this->id));
        
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
     * Rename all the instances of a tag
     * 1) Find a set of all the joins that need to be updated
     * 2) Ignore the original tag if included in the list of new tags
     * 3) Loop through the new tags, loop through the joins and create a new one for each new tag
     * @return void
     */
    public function rename($new_names, $user_id = null) 
    {
        $joins = array();
        
        $find_criteria = array('tag'=>$this->name);
        
        if ($user_id) {
            $find_criteria['user'] = (int) $user_id;
        }
        
        $taggings = $this->getTable('Taggings')->findBy($find_criteria);

        if (in_array($this->name, $new_names)) {

            //Remove the original name from the list
            $new_names = array_diff($new_names,array($this->name));
            //Ignore the existing joins
            
            //If there are no new names left, finish
            if (!count($new_names)) {
                return true;
            }
            
        } else {
            //Otherwise take the first name and use it to update the existing joins
            $new_name = array_shift($new_names);
                
            //Find the tag or make a new one
            $new_tag = $this->getTable()->findOrNew($new_name);
            $new_tag->forceSave();
            
            $new_tag_id = $new_tag->id;
                    
            //Update all the existing joins
            foreach ($taggings as $key => $tagging) {
                $tagging->tag_id = $new_tag_id;
            
                //If saving doesn't work, its because of unique constraint violations
                //So we should delete the join because it already exists
                try {
                    $tagging->save();
                } catch (Zend_Db_Exception $e) {
                    $tagging->delete();
                }
            }
        }
        
        //Create new joins for the newly entered tags (if applicable)
        foreach ($new_names as $k => $new_name) {
            $new_tag = $this->getTable()->findOrNew($new_name);
            $new_tag->forceSave();
            
            $new_tag_id = $new_tag->id;
                        
            foreach ($taggings as $tagging) {
                //clone the existing join
                $new_tagging = clone $tagging;
                
                //set the tag_id for the cloned join
                
                $new_tagging->tag_id = $new_tag_id; 
                
                //save the new join
                $new_tagging->save();
            }
        }
    }
}
