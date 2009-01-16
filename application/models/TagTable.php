<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * @package Omeka
 * @subpackage Models
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class TagTable extends Omeka_Db_Table
{    
    public function findOrNew($name) {
        $db = $this->getDb();
        $sql = "
        SELECT t.* 
        FROM {$db->Tag} t 
        WHERE t.name COLLATE utf8_bin LIKE ? 
        LIMIT 1";
        $tag = $this->fetchObject($sql, array($name));
        
        if (!$tag) {
            $tag = new Tag;
            $tag->name = $name;
        }
        
        return $tag;
    }
    
    /**
     * Filter a SELECT statement based on an Omeka_Record instance
     * 
     * @param Omeka_Db_Select
     * @param Omeka_Record
     * @return void
     **/
    public function filterByRecord($select, $record)
    {
        if ($record->exists()) {
            $record_id = $record->id;
            $select->where("tg.relation_id = ?", $record_id);
            
            if (empty($for)) {
                $select->where("tg.type = ?", get_class($record));
            }
        //A non-persistent record has no tags, so return emptiness
        } else {
            $select->where('t.id = 0');
        }        
    }
    
    /**
     * Filter SELECT statement based on a user or entity ID
     * 
     * @param Omeka_Db_Select
     * @param integer
     * @param boolean
     * @return void
     **/
    public function filterByUserOrEntity($select, $userId, $isUser=true) {
        $db = $this->getDb();
        $select->joinInner( array('e'=>$db->Entity), "e.id = tg.entity_id", array());
        
        if ($isUser) {
            $select->joinInner( array('u'=>$db->User), "u.entity_id = e.id", array());
            $select->where("u.id = ?", $userId);
        } else {
            $select->where("e.id = ?", $userId );
        }
        
    }
    
    /**
     * Adds an ORDER BY clause to the SELECT statment based on the given criteria
     * 
     * @param string
     * @return void
     **/
    public function sortBy($select, $sortCriteria)
    {
        switch ($sortCriteria) {
            case 'recent':
                $select->order('tg.time DESC');
                break;
            case 'alpha':
                $select->order('t.name ASC');
                break;
            case 'most':
                $select->order('tagCount DESC');
                break;
            case 'least':
                $select->order('tagCount ASC');
                break;
            default:
                break;
        }
    }
    
    /**
     * Filter SELECT statement based on the type of tags to view (Item, Exhibit, etc.)
     * 
     * @param Omeka_Db_Select
     * @param string
     * @return void
     **/
    public function filterByTagType($select, $type)
    {
        $db = $this->getDb();
        
        //Showing tags related to items
        if ($type == 'Item') {
            //Join on the items table, add permissions checks for public
            $select->joinInner( array('i'=>$db->Item), "i.id = tg.relation_id AND tg.type = 'Item'", array());
            new ItemPermissions($select);
        } else {
            $select->where("tg.type = ?", (string) $type);
        }
    }
        
    /**
     * Retrieve a certain number of tags
     *
     * @param Omeka_Db_Select 
     * @param array $params
     *         'sort' => 'recent', 'least', 'most', 'alpha'
     *        'limit' => integer
     *         'record' => instanceof Omeka_Record
     *        'entity' => entity_id
     *        'user' => user_id
     * @return void
     **/
    public function applySearchFilters($select, $params=array())
    {        
        $db = $this->getDb();
        
        if ($tagType = $params['type']) {
            $this->filterByTagType($select, $tagType);
        }
        
        //If we only want tags for public items, use one of the ItemTable's filters
        if (isset($params['public']) && $tagType == 'Item') {
            $db->getTable('Item')->filterByPublic($select, isset($params['public']));
        }
        
        if (($record = $params['record']) && ($record instanceof Omeka_Record)) {
            $this->filterByRecord($select, $record);
        }
                
        if(($filterId = $params['user']) || ($filterId = $params['entity'])) {
            $this->filterByUserOrEntity($select, (int) $filterId, !empty($params['user']));
        }

        if ($params['sort']) {
            $this->sortBy($select, (string) $params['sort']);
        }
                        
        $select->group("t.id");
    }
        
    /**
     * @internal SELECT statements should always pull a count of how many times 
     * the tag occurs as a tagCount field in the Tag object.
     * 
     * @return Omeka_Db_Select
     **/    
    public function getSelect()
    {
        $select = new Omeka_Db_Select;
        
        $db = $this->getDb();
        
        $select->from(array('t'=>$db->Tag), array('t.*', 'tagCount'=>'COUNT(t.id)'))
                ->joinInner( array('tg'=>$db->Taggings), "tg.tag_id = t.id", array())
                ->group('t.id');
                
        return $select;
    }
}