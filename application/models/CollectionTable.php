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
        
        // We should only paginate if these parameters have been passed (many
        // cases in which pagination is not desirable for collections).
        if(array_key_exists('page', $params) and array_key_exists('per_page', $params)) {
            $page = 1;
            $per_page = $this->getNumRecordsPerPage();
    
            if (isset($params['per_page'])) {
                $per_page = (int) $params['per_page'];
            }
    
            if (isset($params['page'])) {
                $page = (int) $params['page'];
            }
    
            $select->limitPage($page, $per_page);    
        }

        
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
        $options = Omeka_Context::getInstance()->getOptions();
        if (is_admin_theme()) {
            return (int) $options['per_page_admin'];
        } else {
            return (int) $options['per_page_public'];
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
        new CollectionPermissions($select);   
        return $select;
    }
    
    public function findRandomFeatured()
    {
        $select = $this->getSelect()->where("c.featured = 1")->order("RAND()")->limit(1);        
        return $this->fetchObject($select);
    }
}