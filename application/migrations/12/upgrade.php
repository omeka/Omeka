<?php 
get_db()->query("ALTER TABLE `files_videos` CHANGE `bitrate` `bitrate` int(10) unsigned default NULL"); 
?>

