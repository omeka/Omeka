<?php
/**
 * All theme File helper functions
 * 
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka_ThemeHelpers
 * @subpackage FileHelpers
 **/
 
 /**
  * Retrieve metadata for a given field in the current file.
  * 
  * @since 1.0
  * @param string $elementSetName
  * @param string $elementName
  * @param array $options
  * @param File|null $file
  * @return mixed
  **/
 function item_file($elementSetName, $elementName = null, $options = array(), $file = null)
 {
     if (!$file) {
         $file = get_current_file();
     }
     return __v()->fileMetadata($file, $elementSetName, $elementName, $options);
 }
 
 /**
  * @since 0.10
  * @return File|null
  **/
 function get_current_file()
 {
     return __v()->file;
 }
 
 /**
  * @since 0.10
  * @param File
  * @return void
  **/
 function set_current_file(File $file)
 {
     __v()->file = $file;
 }
 
 /**
  * Retrieve the set of all metadata for the current file.
  * 
  * @since 1.0
  * @param array $options Optional
  * @param File|null $file Optional
  * @return string|array
  **/
 function show_file_metadata(array $options = array(), $file = null)
 {
     if (!$file) {
         $file = get_current_file();
     }
     return __v()->fileMetadataList($file, $options);
 }