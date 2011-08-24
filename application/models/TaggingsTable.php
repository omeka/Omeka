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
class TaggingsTable extends Omeka_Db_Table
{
    /**
     * Current options
     *
     * @return void
     */
    public function findBy($options=array(), $for=null, $returnCount=false) 
    {
        $select = new Omeka_Db_Select;
        $db = $this->getDb();
        
        if ($returnCount) {
            $select->from(array('tg'=>$db->Taggings), "COUNT(DISTINCT(tg.id))");
        } else {
            $select->from(array('tg'=>$db->Taggings), "tg.*");
        }
                
        if(isset($options['tag'])) {
            
            $tag = $options['tag'];
            $select->joinInner(array('t'=>$db->Tag), "t.id = tg.tag_id", array());
            
            if (is_array($tag)) {
                $wheres = array();
                $names = array();
                foreach ($tag as $t) {
                    $name = ($t instanceof Tag) ? $t->name : $t;
                    $wheres[] = "t.name = ".$db->quote($t);
                }
                $select->where( "(" . implode(' OR ', $wheres) . ")" );
            } else {
                $name = ($tag instanceof Tag) ? $tag->name : $tag;
                $select->where("t.name = ?", $name);                
            }
        }
        
        if (isset($options['entity']) || isset($options['user'])) {
            
            $select->joinInner(array('e'=>$db->Entity), "e.id = tg.entity_id", array());
            
            if (array_key_exists('entity', $options)) {
                $entity_id = (int) is_numeric($options['entity']) ? $options['entity'] : $options['entity']->id;
                $select->where("e.id = ?", $entity_id);
                
            } else if ($user = $options['user']) {
                
                $select->joinInner(array('u'=>$db->User), "u.entity_id = e.id", array());

                if (is_numeric($user)) {
                    $select->where("u.id = ?", $user);
                } elseif($user instanceof User and !empty($user->id)) {
                    $select->where("u.id = ?", $user->id);
                }
            }
        }
        
        if (isset($options['record'])) {
            $record = $options['record'];
            $select->where("tg.relation_id = ?", $record->id);
            $select->where("tg.type = ?", get_class($record) );
        }
        
        if ($for and !isset($options['record'])) {
            $select->where("tg.type = ?", $for );
        }
                                
        if ($returnCount) {
            return $db->fetchOne($select);
        } else {
            return $this->fetchObjects($select);
        }
    }
}
