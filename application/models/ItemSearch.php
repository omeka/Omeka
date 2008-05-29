<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * @package Omeka
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class ItemSearch
{
    protected $_select;
    
    /**
     * Constructor.  Adds a SQL_CALC_FOUND_ROWS column to the sql statement
     * 
     * @param Zend_Db_Select
     * @return void
     **/
    public function __construct($select)
    {   
        $this->_select = $select;
    }
    
    public function getSelect()
    {
        return $this->_select;
    }
    
    public function getDb()
    {
        return Omeka_Context::getInstance()->getDb();
    }
    
    /**
     * The trail of this function:
     *     items_search_form() form helper  --> ItemsController::browseAction()  --> ItemTable::findBy() --> here
     *
     * @return void
     **/
    public function advanced($advanced)
    {
        $db = $this->getDb();
        
        $select = $this->getSelect();
        
        $metafields = array();
        
        foreach ($advanced as $k => $v) {
            
            $field = $v['field'];
            $type = $v['type'];
            $value = $v['terms'];
            
            //Determine what the SQL clause should look like
            switch ($type) {
                case 'contains':
                    $predicate = "LIKE " . $db->quote('%'.$value .'%');
                    break;
                case 'does not contain':
                    $predicate = "NOT LIKE " . $db->quote('%'.$value .'%');
                    break;
                case 'is empty':    
                    $predicate = "= ''";
                    break;
                case 'is not empty':
                    $predicate = "!= ''";
                    break;
                default:
                    throw new Exception( 'Invalid search type given!' );
                    break;
            }
            
            // Strip out the prefix to figure out what table it comin from
            $field_a = explode('_', $field);
            $prefix = array_shift($field_a);
            $field = implode('_', $field_a);
            
            // Process the joins differently depending on what table it needs
            switch ($prefix) {
                case 'item':
                    // We don't need any joins because we are already searching 
                    // the items table
                    if (!$db->getTable('Item')->hasColumn($field)) {
                        throw new Exception( 'Invalid field given!' );
                    }
                    
                    //We're good, so start building the WHERE clause
                    $where = '(i.' . $field . ' ' . $predicate . ')';
                    
                    $select->where($where);
                    
                    break;
                case 'metafield':
                    // Ugh, the Metafields query needs to be dealt with separately 
                    // because just tacking on multiple metafields will not 
                    // return correct results
                    
                    //We need to join on the metafields and metatext tables
                    $select->joinInner(array('mt'=>"$db->Metatext"), 'mt.item_id = i.id', array());
                    $select->joinInner(array('m'=>"$db->Metafield"), 'm.id = mt.metafield_id', array());

                    //Start building the where clause
                    $where = "(m.name = ". $db->quote($field) . " AND mt.text $predicate)";

                    $metafields[] = $where;

                    break;
                default:
                    throw new Exception( 'Search failed!' );
                    break;
            }    

            // Build the metafields WHERE clause
            // Should look something like the query below
            /*
            mt.id IN 
            (
            SELECT mt.id 
            FROM metatext mt 
            INNER JOIN metafields m ON m.id = mt.metafield_id
            WHERE 
                (m.name = 'Process Edit' AND mt.text != '') 
            OR 
                (m.name = 'Process Review' AND mt.text = '')
            )
                }
            }
            */
            
            if (count($metafields)) {
                $subQuery = new Omeka_Db_Select;
                $subQuery->from(array('mt'=>"$db->Metatext"), array('mt.id'))
                                ->joinInner(array('m'=>"$db->Metafield"), 'm.id = mt.metafield_id', array())
                                ->where(join(' OR ', $metafields));
                $select->where('mt.id IN ('. $subQuery->__toString().')');            
            }
        }
    }
    
    /**
     * Search through the items and metatext table via fulltext, store results in a temporary table
     * Then search the tags table for atomized search terms (split via whitespace) and store results in the temp table
     * then join the main query to that temp table and order it by relevance values retrieved from the search
     *
     * @return void
     **/    
    public function simple($terms)
    {
        $db = $this->getDb();
        $select = $this->getSelect();
        
        // Create a temporary search table (won't last beyond the current request)
        $tempTable = "{$db->prefix}temp_search";
        $db->exec("
            CREATE TEMPORARY TABLE IF NOT EXISTS $tempTable (
                item_id BIGINT UNIQUE, 
                rank FLOAT(10) DEFAULT 1, 
                PRIMARY KEY(item_id)
            )");
        
        // Search the metatext table
        $mSelect = new Omeka_Db_Select;
        $mSearchClause = "MATCH (m.text) AGAINST (".$db->quote($terms).")";
        
        $mSelect->from( array('m'=>"$db->Metatext"), "m.item_id, $mSearchClause as rank");
        
        $mSelect->where($mSearchClause);
        //echo $mSelect;
        
        //Put those results in the temp table
        $insert = "REPLACE INTO $tempTable (item_id, rank) ".$mSelect->__toString();
        $db->exec($insert);
        
        //Search the items table
        $iSearchClause = "
        MATCH (
            i.title, 
            i.publisher, 
            i.language, 
            i.relation, 
            i.spatial_coverage, 
            i.rights, 
            i.description, 
            i.source, 
            i.subject, 
            i.creator, 
            i.additional_creator, 
            i.contributor, 
            i.format,
            i.rights_holder, 
            i.provenance, 
            i.citation
        ) AGAINST (".$db->quote($terms).")";
        
        $itemSelect = new Omeka_Db_Select;
        $itemSelect->from(array('i'=>"$db->Item"), 
                          array('item_id'=>'i.id', 'rank'=>$iSearchClause));
                    
        $itemSelect->where($iSearchClause);
        
        //Grab those results, place in the temp table        
        $insert = "
        REPLACE INTO $tempTable (
            item_id, 
            rank
        ) ".$itemSelect->__toString();
        
        $db->exec($insert);        
        
        //Start pulling in search data for the tags
        
        $tagSearchList = preg_split('/\s+/', $terms);
        //Also make sure the tag list contains the whole search string, just in case that is found
        $tagSearchList[] = $terms;
        
        $tagSelect = new Omeka_Db_Select;
        $tagSelect->from(array('t'=>"$db->Tag"), array('item_id'=>'i.id'));
        $tagSelect->joinInner(array('tg'=>"$db->Taggings"), "tg.tag_id = t.id", array());
        $tagSelect->joinInner(array('i'=>"$db->Item"), "(i.id = tg.relation_id AND tg.type = 'Item')", array());
        
        foreach ($tagSearchList as $tag) {
            $tagSelect->orWhere("t.name LIKE ?", $tag);
        }
        $db->exec("REPLACE INTO $tempTable (item_id) " . $tagSelect->__toString());
        
        //Now add a join to the main SELECT SQL statement and sort the results by relevance ranking        
        $select->joinInner(array('ts'=>$tempTable), 'ts.item_id = i.id', array());
        $select->order('ts.rank DESC');
    }
    
    /**
     * Remove the temporary search table
     *
     * @return void
     **/
    private function clearSearch()
    {
        $this->getDb()->query("DROP TABLE IF EXISTS {$db->prefix}temp_search");
    }
    
    public function __destruct()
    {
        $this->clearSearch();
    }
    
}