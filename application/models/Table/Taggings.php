<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * @package Omeka\Db\Table
 */
class Table_Taggings extends Omeka_Db_Table
{
    public function applySearchFilters($select, $params = array())
    {
        $db = $this->getDb();
        if(isset($params['tag'])) {
            $tag = $params['tag'];
            $select->joinInner(array('tags'=>$db->Tag), 'tags.id = taggings.tag_id', array());

            if (is_array($tag)) {
                $wheres = array();
                $names = array();
                foreach ($tag as $t) {
                    $name = ($t instanceof Tag) ? $t->name : $t;
                    $wheres[] = 'tags.name = '.$db->quote($t);
                }
                $select->where( '(' . implode(' OR ', $wheres) . ')' );
            } else {
                $name = ($tag instanceof Tag) ? $tag->name : $tag;
                $select->where('tags.name = ?', $name);
            }
        }

        if (isset($params['record'])) {
            $record = $params['record'];
            $select->where('taggings.relation_id = ?', $record->id);
            $select->where('taggings.type = ?', get_class($record) );
        } else if (isset($params['type'])) {
            $type = $params['type'];
            $select->where('taggings.type = ?', $type);
        }
    }
}
