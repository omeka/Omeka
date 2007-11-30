<?php
require_once 'Entity.php';
/**
 * Institution
 * @package: Omeka
 */
class Institution extends Entity
{
	public function beforeSave()
	{
		$this->type = "Institution";
	}
	
	public function getName()
	{
		return $this->institution;
	}
}

?>