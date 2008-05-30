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
class CollectionTable extends Omeka_Db_Table
{    
    public function applySearchFilters($select, $params)
    {
        /*****************************
         * PAGINATION
         *****************************/
        $page = 1;
        $per_page = $this->getNumRecordsPerPage();
        
        if (isset($params['per_page'])) {
            $per_page = (int) $params['per_page'];
        }
        
        if (isset($params['page'])) {
            $page = (int) $params['page'];
        }
        
        $select->limitPage($page, $per_page);
        
        /****************************
         * END PAGINATION
         ****************************/
        
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
    
    protected function getNumRecordsPerPage()
    {
        $config_ini = Omeka_Context::getInstance()->getConfig('basic');
        $per_page = (int) $config_ini->pagination->per_page;
        return $per_page;
    }
    
    public function findAllForSelectForm()
    {
        $select = $this->getSelect();
        $select->reset('columns');
        $select->from(array(), array('c.id', 'c.name'));
        
        $pairs = $this->getDb()->fetchPairs($select);
        return $pairs;
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
        new CollectionPermissions($select);   
        return $select;
    }
    
    public function findRandomFeatured()
    {
        $select = $this->getSelect()->where("c.featured = 1")->order("RAND()")->limit(1);        
        return $this->fetchObject($select);
    }
}