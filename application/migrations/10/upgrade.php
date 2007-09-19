<?php 

//check to see if directory has proper permissions
if (is_writable(SQUARE_THUMBNAIL_DIR) == FALSE) {
	echo "remember that you MUST change the permissions of your square_thumbnails directory!  refresh this page after you've done so";
}

$square_thumbnail_constraint = $_POST['square_thumbnail_constraint'];

//set square_thumbnail_constraint
$this->query("INSERT INTO `options` (name, value) VALUES ('square_thumbnail_constraint', '$square_thumbnail_constraint');"); 

?>