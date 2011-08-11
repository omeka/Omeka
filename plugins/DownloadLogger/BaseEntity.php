<?php
/**
 * @version $Id$
 * @copyright Scand Ltd.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @author Scand Ltd.
 **/


abstract class BaseEntity
{
    private $_id;
    
    /**
     * @return the $_id
     */
    public function get_id ()
    {
        return $this->_id;
    }

    /**
     * @param field_type $_id
     * @return BaseEntity
     */
    public function set_id ($_id)
    {
        $this->_id = $_id;
        return $this;
    }
    
    
	public function __construct($src = false)
	{
		if ($src)
		{
			if (is_array($src))
				$this->populateFromArray($src);
			elseif (is_int($src+0))
				$this->populate($src);
		}
	}
	
	/**
     * Retrieve the database object.
     * 
     * @uses Omeka_Core
     * @return Omeka_Db
     */
	public function getDb()
	{
	    if (!$this->_db)
	    {
    	    $core = new Omeka_Core;
            $core->phasedLoading('initializeDb');
            $this->_db = $core->getDb();
	    }
	    return $this->_db;
	    
	}
	
	/**
	 * Populate by id
	 * @param unknown_type $id
	 * @return true|false
	 */
	abstract public function populate($id);
	/**
	 * Save entity
	 */
	abstract public function save();
	
	abstract public function validate();

	/**
	 * Populate enity from array
	 * @param array $data
	 * @return BaseEntity
	 */
   	public function populateFromArray(array $data = array())
	{
    	foreach ($this->getFields() as $k)
    	{
    		if (isset($data[$k]) && method_exists($this,$this->getFieldSetter($k)))
    			$this->{$this->getFieldSetter($k)}($data[$k]);
    	}
		return $this;
	}
	
	/**
	 * Convert enity to array
	 * @return array
	 */
	public function toArray()
	{
		$a = array();
    	$fields = $this->getFields();

    	foreach ($fields as $field) {
      		if (method_exists($this, $this->getFieldGetter($field)))
        		$a [$field] = $this->{$this->getFieldGetter($field)}();
    	}

    	return $a;
	}    
    
	/**
	 * Convert array od BaseEntities to array
	 * @param array $baseEntities
	 * @return array
	 */
	public static function BaseEntities2Array(array $baseEntities)
	{
		$a = array();
		foreach ($baseEntities as $baseEntity)
			$a[] = $baseEntity->toArray();
		return $a;
	}
	
	private function getFields()
	{
		$yield = array();
        foreach ((array)$this as $k => $v) {
        	if (!is_object($v) && !is_array($v))
        	{
        		$k = preg_replace('/^(.+\0)?_/', '', $k);
        		$yield[] = $k;
        	}
        }
        return $yield;
	}
	
	private function getFieldMutator($field)
	{
		$mutator = "_".$field;

		return $mutator;
	}

	private function getFieldSetter($field)
	{
		return "set" . $this->getFieldMutator($field);
	}

	private function getFieldGetter($field)
	{
		return "get" . $this->getFieldMutator($field);
	}
}