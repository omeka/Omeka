<?php

/**
 * Special Plugin finder (to generate the correct plugins from the db)
 *
 * @package Sitebuilder
 * @author Kris Kelly
 **/
class PluginTable extends Doctrine_Table
{
	public function createByName($array, $name) {
		$this->setData($array);
		$record = new $name($this, false);
		$this->setData(array());
		return $record;
	}
} // END class PluginTable extends Doctrine_Table

?>