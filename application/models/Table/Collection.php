<?php 
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @subpackage Models
 */
 
/**
 * @package Omeka
 * @subpackage Models
 * @author CHNM
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Table_Collection extends Omeka_Db_Table
{    
    public function applySearchFilters($select, $params)
    {
        if(array_key_exists('public', $params)) {
            $this->filterByPublic($select, $params['public']);
        }
        
        if(array_key_exists('featured', $params)) {
            $this->filterByFeatured($select, $params['featured']);
        }
    }
    
    protected function _getColumnPairs()
    {
        return array('collections.id', 'collections.name');
    }

    /**
     * Apply permissions checks to all SQL statements retrieving collections from the table
     * 
     * @param string
     * @return void
     */
    public function getSelect()
    {
        $select = parent::getSelect();
        $permissions = new PublicPermissions('Collections');
        $permissions->apply($select, 'collections');
        
        return $select;
    }
    
    public function findRandomFeatured()
    {
        $select = $this->getSelect()->where('collections.featured = 1')->order('RAND()')->limit(1);        
        return $this->fetchObject($select);
    }    
    
    /**
     * Apply a filter to the collections based on whether or not they are public
     * 
     * @param Zend_Db_Select
     * @param boolean Whether or not to retrieve only public collections
     * @return void
     */
    public function filterByPublic($select, $isPublic)
    {         
        $isPublic = (bool) $isPublic; // this makes sure that empty strings and unset parameters are false

        //Force a preview of the public collections
        if ($isPublic) {
            $select->where('collections.public = 1');
        } else {
            $select->where('collections.public = 0');
        }
    }
    
    /**
     * Apply a filter to the collections based on whether or not they are featured
     * 
     * @param Zend_Db_Select
     * @param boolean Whether or not to retrieve only public collections
     * @return void
     */
    public function filterByFeatured($select, $isFeatured)
    {
        $isFeatured = (bool) $isFeatured; // this make sure that empty strings and unset parameters are false
        
        //filter items based on featured (only value of 'true' will return featured collections)
        if ($isFeatured) {
            $select->where('collections.featured = 1');
        } else {
            $select->where('collections.featured = 0');
        }     
    }
}
