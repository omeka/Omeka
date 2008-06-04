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
     * Search query consists of a derived table that is INNER JOIN'ed to
     * the main SQL query.  That derived table is a union of two SELECT
     * queries.  The first query searches the FULLTEXT index on the 
     * items_elements table, and the second query searches the tags table
     * for every word in the search terms and assigns each found result 
     * a rank of '1'. That should make tagged items show up higher on the found
     * results list for a given search.
     *
     * @return void
     **/    
    public function simple($terms)
    {
        $db = $this->getDb();
        $select = $this->getSelect();
        
        /*
        SELECT i.*, s.rank
        FROM items i
        INNER JOIN 
        (
            SELECT i.id as item_id, MATCH (ie.text) AGAINST ('foo bar') as rank
            FROM items i
            INNER JOIN items_elements ie ON ie.item_id = i.id
            WHERE MATCH (ie.text) AGAINST ('foo bar')
            UNION 
            SELECT i.id as item_id, 1 as rank
            FROM items i
            INNER JOIN taggings tg ON (tg.relation_id = i.id AND tg.type = "Item")
            INNER JOIN tags t ON t.id = tg.tag_id
            WHERE (t.name = 'foo' OR t.name = 'bar')
        ) s ON s.item_id = i.id
        */
        
        $searchQuery  = (string) $this->_getElementsQuery($terms) . " UNION ";
        $searchQuery .= (string) $this->_getTagsQuery($terms);
                
        // INNER JOIN to the main SQL query and then ORDER BY rank DESC
        $select->joinInner(array('s'=>new Zend_Db_Expr('('. $searchQuery . ')')), 's.item_id = i.id', array())
            ->order('s.rank DESC'); 
    }
    
    protected function _getElementsQuery($terms)
    {
        $db = $this->getDb();
        $quotedTerms = $db->quote($terms);
                
        // This doesn't really need to use a Select object because this query
        // is not dynamic.  
        $query = "
            SELECT i.id as item_id, MATCH (ie.text) AGAINST ($quotedTerms) as rank
            FROM $db->Item i 
            INNER JOIN $db->ItemsElements ie ON ie.item_id = i.id
            WHERE MATCH (ie.text) AGAINST ($quotedTerms)";
        
        return $query;
    }
    
    protected function _getTagsQuery($terms)
    {
        $db = $this->getDb();
        
        $rank = 1;

        $tagList = preg_split('/\s+/', $terms);
        //Also make sure the tag list contains the whole search string, just in case that is found
        $tagList[] = $terms; 
            
        $select = new Omeka_Db_Select;
        $select->from( array('i'=>$db->Item), array('item_id'=>'i.id', 'rank'=>new Zend_Db_Expr($rank)))
            ->joinInner( array('tg'=>$db->Taggings), 'tg.relation_id = i.id AND tg.type = "Item"', array())
            ->joinInner( array('t'=>$db->Tag), 't.id = tg.tag_id', array());
            
        foreach ($tagList as $tag) {
            $select->orWhere('t.name LIKE ?', $tag);
        }
        
        return $select;
    }
    
}