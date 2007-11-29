<?php 
$db = get_db();

//Got to make all of the ID fields are unsigned
$db->exec("ALTER TABLE `$db->UsersActivations` CHANGE `id` `id` BIGINT( 20 ) UNSIGNED NOT NULL AUTO_INCREMENT");
$db->exec("ALTER TABLE `$db->User` CHANGE `id` `id` BIGINT( 20 ) UNSIGNED NOT NULL AUTO_INCREMENT");
$db->exec("ALTER TABLE `$db->TypesMetafields` CHANGE `id` `id` BIGINT( 20 ) UNSIGNED NOT NULL AUTO_INCREMENT");   
$db->exec("ALTER TABLE `$db->Type` CHANGE `id` `id` BIGINT( 20 ) UNSIGNED NOT NULL AUTO_INCREMENT"); 
$db->exec("ALTER TABLE `$db->Tag` CHANGE `id` `id` BIGINT( 20 ) UNSIGNED NOT NULL AUTO_INCREMENT"); 
$db->exec("ALTER TABLE `$db->UsersActivations` CHANGE `id` `id` BIGINT( 20 ) UNSIGNED NOT NULL AUTO_INCREMENT"); 
$db->exec("ALTER TABLE `$db->ExhibitSection` CHANGE `id` `id` BIGINT( 20 ) UNSIGNED NOT NULL AUTO_INCREMENT"); 
$db->exec("ALTER TABLE `$db->ExhibitPage` CHANGE `id` `id` BIGINT( 20 ) UNSIGNED NOT NULL AUTO_INCREMENT"); 
$db->exec("ALTER TABLE `$db->Plugin` CHANGE `id` `id` BIGINT( 20 ) UNSIGNED NOT NULL AUTO_INCREMENT");
$db->exec("ALTER TABLE `$db->Option` CHANGE `id` `id` BIGINT( 20 ) UNSIGNED NOT NULL AUTO_INCREMENT");  
$db->exec("ALTER TABLE `$db->Metatext` CHANGE `id` `id` BIGINT( 20 ) UNSIGNED NOT NULL AUTO_INCREMENT"); 
$db->exec("ALTER TABLE `$db->Metafield` CHANGE `id` `id` BIGINT( 20 ) UNSIGNED NOT NULL AUTO_INCREMENT"); 
$db->exec("ALTER TABLE `$db->ExhibitPageEntry` CHANGE `id` `id` BIGINT( 20 ) UNSIGNED NOT NULL AUTO_INCREMENT"); 
$db->exec("ALTER TABLE `$db->UsersActivations` CHANGE `id` `id` BIGINT( 20 ) UNSIGNED NOT NULL AUTO_INCREMENT"); 
$db->exec("ALTER TABLE `$db->Item` CHANGE `id` `id` BIGINT( 20 ) UNSIGNED NOT NULL AUTO_INCREMENT"); 
$db->exec("ALTER TABLE `$db->FilesImages` CHANGE `id` `id` BIGINT( 20 ) UNSIGNED NOT NULL AUTO_INCREMENT"); 
$db->exec("ALTER TABLE `$db->File` CHANGE `id` `id` BIGINT( 20 ) UNSIGNED NOT NULL AUTO_INCREMENT"); 
$db->exec("ALTER TABLE `$db->FileMetaLookup` CHANGE `id` `id` BIGINT( 20 ) UNSIGNED NOT NULL AUTO_INCREMENT"); 
$db->exec("ALTER TABLE `$db->UsersActivations` CHANGE `id` `id` BIGINT( 20 ) UNSIGNED NOT NULL AUTO_INCREMENT"); 
$db->exec("ALTER TABLE `$db->Exhibit` CHANGE `id` `id` BIGINT( 20 ) UNSIGNED NOT NULL AUTO_INCREMENT"); 
$db->exec("ALTER TABLE `$db->UsersActivations` CHANGE `id` `id` BIGINT( 20 ) UNSIGNED NOT NULL AUTO_INCREMENT"); 
$db->exec("ALTER TABLE `$db->EntityRelationships` CHANGE `id` `id` BIGINT( 20 ) UNSIGNED NOT NULL AUTO_INCREMENT");
$db->exec("ALTER TABLE `$db->EntitiesRelations` CHANGE `id` `id` BIGINT( 20 ) UNSIGNED NOT NULL AUTO_INCREMENT");  
$db->exec("ALTER TABLE `$db->Entity` CHANGE `id` `id` BIGINT( 20 ) UNSIGNED NOT NULL AUTO_INCREMENT"); 
$db->exec("ALTER TABLE `$db->Collection` CHANGE `id` `id` BIGINT( 20 ) UNSIGNED NOT NULL AUTO_INCREMENT");  
?>
