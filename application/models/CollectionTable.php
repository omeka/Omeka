<?php 
/**
* @todo Maybe refactor these Omeka_Table classes to automatically check 
* for a permissions class and load that w/o having to manually override the methods
*/
class CollectionTable extends Omeka_Table
{
	public function findAll()
	{
		$db = get_db();
		
		$select = new Omeka_Select;
		
		$select->from("$db->Collection c");
		
		new CollectionPermissions($select);
		
		return $this->fetchObjects($select);
	}
	
	/**
	 * @internal There is a lot of duplication between this and the ItemTable::findBy() 
	 * method, so maybe this stuff should either be super-classed with model-specific behavior
	 * provided by a template in a subclass, or it should be separated into other classes 
	 * that get referenced by these findBy() methods.
	 *
	 * @return array|false
	 **/
	public function findBy($params = array())
	{
	    $select = $this->getSelectSql();
	   
	    /*****************************
	     * PAGINATION
	     *****************************/
	    $page = 1;
	    $per_page = $this->getNumRecordsPerPage();
	    
	    if(isset($params['per_page'])) {
	        $per_page = (int) $params['per_page'];
	    }
	    
	    if(isset($params['page'])) {
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
         
         if($params['recent'] === true) {             
             $select->order('c.id DESC');
         }
        
// echo $select;exit;       
	    return $this->fetchObjects($select);
	}
	
	protected function getNumRecordsPerPage()
	{
	    $config_ini = Zend_Registry::get('config_ini');
		$per_page = (int) $config_ini->pagination->per_page;
		
		return $per_page;
	}
	
	protected function getSelectSql()
	{
        $db = get_db();
		$select = new Omeka_Select;	 
		$select->from("$db->Collection c", 'c.*');
		new CollectionPermissions($select);   
		return $select;
	}
	
	public function count()
	{
		$db = get_db();
		
		$select = new Omeka_Select;
		
		$select->from("$db->Collection c", "COUNT(DISTINCT(c.id))");
		
		new CollectionPermissions($select);
		
		return $db->fetchOne($select);
	}
	
	public function findRandomFeatured()
	{
	    $db = get_db();
	    
	    $select = new Omeka_Select;
	    
	    $select->from("$db->Collection c")->where("c.featured = 1")->order("RAND()")->limit(1);
	    
	    return $this->fetchObjects($select, array(), true);
	}
}
 
?>
