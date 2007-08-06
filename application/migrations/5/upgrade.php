<?php 

if(!$this->tableHasColumn('Exhibit','public')) {
	$this->query("
ALTER TABLE `exhibits` ADD `public` TINYINT( 1 ) NULL DEFAULT '0' AFTER `featured` ;

ALTER TABLE `exhibits` ADD INDEX ( `public` ) ;

ALTER TABLE `exhibits` CHANGE `featured` `featured` TINYINT( 1 ) NULL DEFAULT '0';

UPDATE `exhibits` SET public =1;"); 
}


?>
