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
class Table_RecordsTags extends Omeka_Db_Table
{
    public function applySearchFilters($select, $params = [])
    {
        $db = $this->getDb();
        if (isset($params['tag'])) {
            $tag = $params['tag'];
            $select->joinInner(['tags' => $db->Tag], 'tags.id = records_tags.tag_id', []);

            if (is_array($tag)) {
                $wheres = [];
                $names = [];
                foreach ($tag as $t) {
                    $name = ($t instanceof Tag) ? $t->name : $t;
                    $wheres[] = 'tags.name = '.$db->quote($t);
                }
                $select->where('(' . implode(' OR ', $wheres) . ')');
            } else {
                $name = ($tag instanceof Tag) ? $tag->name : $tag;
                $select->where('tags.name = ?', $name);
            }
        }

        if (isset($params['record'])) {
            $record = $params['record'];
            $select->where('records_tags.record_id = ?', $record->id);
            $select->where('records_tags.record_type = ?', get_class($record));
        } elseif (isset($params['type'])) {
            $type = $params['type'];
            $select->where('records_tags.record_type = ?', $type);
        }
    }

    public function findForRecordAndTag($record, $tag)
    {
        $select = $this->getSelectForFindBy(['record' => $record, 'tag' => $tag]);
        return $this->fetchObject($select);
    }
}
