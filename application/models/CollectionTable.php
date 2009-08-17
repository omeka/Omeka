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
class CollectionTable extends Omeka_Db_Table
{    
    public function applySearchFilters($select, $params)
    {
        /****************************
         * FIND RECENT COLLECTIONS
         *
         * ORDER BY id DESC works because MyISAM tables always increment IDs for new rows,
         * would not work with InnoDB because it assigns IDs of deleted records
         ****************************/
         
         if ($params['recent'] === true) {             
             $select->order('c.id DESC');
         }        
    }
    
    protected function _getColumnPairs()
    {
        return array('c.id', 'c.name');
    }

    /**
     * Apply permissions checks to all SQL statements retrieving collections from the table
     * 
     * @param string
     * @return void
     **/
    public function getSelect()
    {
        $select = parent::getSelect();
        
        if($acl = Omeka_Context::getInstance()->getAcl()) {
            new CollectionPermissions($select, $acl);
        }
        
        return $select;
    }
    
    public function findRandomFeatured()
    {
        $select = $this->getSelect()->where("c.featured = 1")->order("RAND()")->limit(1);        
        return $this->fetchObject($select);
    }    
}