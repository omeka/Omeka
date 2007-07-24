<?php
//Add the slug column to the sections table 
if(!$this->tableHasColumn('sections', 'slug')) {

		$this->query("
	ALTER TABLE `sections` ADD `slug` VARCHAR( 30 ) NOT NULL ;
	ALTER TABLE `sections` ADD INDEX ( `slug` ) ;");	
	
	
	//Now generate slugs for all the slug fields so that it will be working
	
	$titles = $this->query("SELECT id, title FROM sections s");
	
	foreach ($titles as $row) {
		$slug = $row['title'];
		
		$prohibited = array(':', '/', ' ', '.');
		$replace = array_fill(0, count($prohibited), '-');
		$slug = str_replace($prohibited, $replace, strtolower($slug) );
		
		$sql = "UPDATE sections SET slug = ? WHERE id = ?";
		$this->query($sql, array( $slug, $row['id'] ));
	}
	

} 
?>
