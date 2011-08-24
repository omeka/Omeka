<?php 
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * @package Omeka
 * @subpackage Mixins
 * @author CHNM
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Orderable extends Omeka_Record_Mixin
{
    public function __construct($record, $childClass, $childFk, $childPluralized)
    {
        $this->record = $record;
        $this->childClass = $childClass;
        $this->childFk = $childFk;
        $this->pluralized = $childPluralized;
    }
    
    public function loadOrderedChildren()
    {
        $id = (int) $this->record->id;
        $db = $this->getDb();
        $target = $this->childClass;
                
        $sql = "
        SELECT s.* 
        FROM {$db->$target} s 
        WHERE s.{$this->childFk} = $id 
        ORDER BY s.`order` ASC";
        
        $children = $this->getTable($target)->fetchObjects($sql);
        
        //Now index them according to their order
        $indexed = array();

        foreach ($children as $child) {
            // The order could be thrown out of sync by invalid values being stored, 
            // so this will just append to the array if the index is already taken.
            if (($order = (int)$child->order) and !array_key_exists($order, $indexed)) {
                $indexed[$order] = $child;
            } else {
                $indexed[] = $child;
            }
        }
        return $indexed;
    }
    
    public function afterSaveForm($post)
    {
        $form = $post[$this->pluralized];
        
        if (!empty($form)) {
        
            $children = $this->loadOrderedChildren();
            
            //Change the order of the sections
            foreach ($form as $key => $entry) {
                $child = $children[$key];
                $child->order = (int)$entry['order'];                
                $child->save();
            }
        }                
    }
    
    /**
     * This will realign the child nodes in ascending natural order after one is removed
     *
     * @param int
     * @return void
     */
    public function reorderChildren()
    {        
        //Retrieve all section IDs in ascending order, then update 
        $db = $this->getDb();
        
        $target = $this->childClass;
        
        $table = $db->$target;
        $parentId = $this->record->id;
        
        //I found this hot solution on the comments for this page:
        //http://dev.mysql.com/doc/refman/5.0/en/update.html
        $db->query("SET @pos=0;");
        $db->query(
        "UPDATE $table s 
        SET s.`order` = (SELECT @pos := @pos + 1) 
            WHERE s.{$this->childFk} = ? 
            ORDER BY s.`order` ASC;", 
            array($parentId));
    }
    
    public function addChild(Omeka_Record $child)
    {
        if (!$this->record->exists()) {
            throw new Omeka_Record_Exception(__('Cannot add a child to a record that does not exist yet!'));
        }
        
        if (!($child instanceof $this->childClass)) {
            throw new Omeka_Record_Exception(__('Child must be an instance of "%s"', $this->childClass));
        }
        
        $fk = $this->childFk;
        
        $child->$fk = $this->record->id;
        
        $new_order = $this->getChildCount() + 1;
        
        $child->order = $new_order;
        
        return $child;
    }
    
    public function getChildCount()
    {
        $db = $this->getDb();
        
        $target = $this->childClass;
        
        $sql = "
        SELECT COUNT(*) 
        FROM {$db->$target} 
        WHERE $this->childFk = ?";
        return $db->fetchOne($sql, array($this->record->id));
    }
}
