<?php 
$this->query("ALTER TABLE `types_metafields` ADD UNIQUE `type_metafield` ( `type_id` , `metafield_id` );"); 
?>
