<?php
/**
 * All File helper functions
 * 
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka_ThemeHelpers
 * @subpackage FileHelpers
 **/
 
 /**
  * Retrieve the web path to a css file.
  *
  * @param string $file Should not include the .css extension
  * @param string $dir Defaults to 'css'
  * @return string
  */
 function css($file, $dir = 'css') 
 {
 	return src($file, $dir, 'css');
 }
 
 /**
  * @see display_files()
  * @uses display_files()
  * @param File $file One File record.
  * @param array $props
  * @param array $wrapperAttributes Optional XHTML attributes for the div wrapper
  * for the displayed file.  Defaults to array('class'=>'item-file').
  * @return string HTML
  **/
 function display_file($file, array $props=array(), $wrapperAttributes = array('class'=>'item-file'))
 {
     return display_files(array($file), $props, $wrapperAttributes);
 }
 
 /**
  * Displays a set of files based on the file's MIME type and any options that are
  * passed.  This is primarily used by other helper functions and will not be used
  * by theme writers in most cases.
  * 
  * @since 0.9
  * @uses Omeka_View_Helper_Media
  * @param array $files An array of File records to display.
  * @param array $props Properties to customize display for different file types.
  * @param array $wrapperAttributes XHTML attributes for the div that wraps each
  * displayed file.  If empty or null, this will not wrap the displayed file in a
  * div.
  * @return string HTML
  **/
 function display_files($files, array $props = array(), $wrapperAttributes = array('class'=>'item-file')) 
 {
     require_once 'Media.php';
     $helper = new Omeka_View_Helper_Media;
     $output = '';
     foreach ($files as $file) {
         $output .= $helper->media($file, $props, $wrapperAttributes);
     }
     return $output;
 } 
 
 /**
  * Retrieve the web path to an image file.
  * 
  * @since 0.9
  * @param string $file Filename, including the extension.
  * @param string $dir Optional Directory within the theme to look for image 
  * files.  Defaults to 'images'.
  * @return string
  */
 function img($file, $dir = 'images') 
 {
 	return src($file, $dir);
 }
 
 /**
  * Echos the web path (that's what's important to the browser)
  * to a javascript file.
  * $dir defaults to 'javascripts'
  * $file should not include the .js extension
  *
  * @param string $file The name of the file, without .js extension.  Specifying 'default' will load 
  * the default javascript files, such as prototype/scriptaculous
  * @param string $dir The directory in which to look for javascript files.  Recommended to leave the default value.
  * @param array $scriptaculousLibraries An array of Scriptaculous libraries, by file name. Default is 'effects' and 'dragdrop'. Works only if 'default' is passed for the first parameter.
  */
 function js($file, $dir = 'javascripts', $scriptaculousLibraries = array('effects', 'dragdrop')) 
 {
     if ($file == 'default') {
         $output  = js('prototype', $dir); //Prototype library loads by default
         $output .= js('prototype-extensions', $dir); //A few custom extensions to the Prototype library
         $output .= js('scriptaculous', $dir, $scriptaculousLibraries);
         $output .= js('search', $dir);

         //Do not try to load 'default.js'
         return $output;
     }
    
    if ('scriptaculous' == $file) {
        $href = src($file, $dir, 'js') . ($scriptaculousLibraries ? '?load=' . implode(',', $scriptaculousLibraries) : '');
    } else {
        $href = src($file, $dir, 'js');
    }
     
 	return '<script type="text/javascript" src="' . html_escape($href) . '" charset="utf-8"></script>'."\n";
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
  * Return the physical path for an asset/resource within the theme (or plugins, shared, etc.)
  *
  * @param string $file
  * @throws Exception
  * @return string
  **/
 function physical_path_to($file)
 {
 	$view = __v();
 	$paths = $view->getAssetPaths();

 	foreach ($paths as $path) {
 	    list($physical, $web) = $path;
 		if(file_exists($physical . DIRECTORY_SEPARATOR . $file)) {
 			return $physical . DIRECTORY_SEPARATOR . $file;
 		}
 	}
 	throw new Exception( "Could not find file '$file'!" );
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
 
 /**
  * Return a valid src attribute value for a given file.  Used primarily
  * by other helper functions.
  *
  *
  * @param string        Filename
  * @param string|null   Directory that the file is contained in (optional) 
  * @param string        File extension (optional)
  * @return string
  **/
 function src($file, $dir=null, $ext = null) 
 {
 	if ($ext !== null) {
 		$file .= '.'.$ext;
 	}
 	if ($dir !== null) {
 		$file = $dir.DIRECTORY_SEPARATOR.$file;
 	}
 	return web_path_to($file);
 }
 
 /**
  * Return the web path for an asset/resource within the theme
  *
  * @param string $file
  * @return string
  **/
 function web_path_to($file)
 {
 	$view = __v();
 	$paths = $view->getAssetPaths();
 	foreach ($paths as $path) {
 	    list($physical, $web) = $path;
 		if(file_exists($physical . DIRECTORY_SEPARATOR . $file)) {
 			return $web . '/' . $file;
 		}
 	}
 	throw new Exception( "Could not find file '$file'!" );
 }
 
 
  /**
  * Returns the most recent files
  * 
  * @since 1.1
  * @param integer $num The maximum number of recent files to return
  * @return array
  **/
 function recent_files($num = 10) 
 {
     return get_files(array('recent'=>true), $num);
 }

 /**
  * @since 1.1
  * @param array $files Set of File records to loop.
  */
 function set_files_for_loop($files)
 {
    __v()->files = $files;
 }

 /**
 * @since 1.1
 * @param array $params
 * @param integer $limit
 * @return array
 **/
 function get_files($params = array(), $limit = 10)
 {
    return get_db()->getTable('File')->findBy($params, $limit);
 }

 /**
 * Retrieve the set of files for the current loop.
 * 
 * @since 1.1
 * @return array
 **/
 function get_files_for_loop()
 {
    return __v()->files;
 }

 /**
 * Loops through files assigned to the view.
 * 
 * @since 1.1
 * @return mixed The current file in the loop.
 */
 function loop_files()
 {
    return loop_records('files', get_files_for_loop());
 }

 /**
 * Determine whether or not there are any files in the database.
 * 
 * @since 1.1
 * @return boolean
 **/
 function has_files()
 {
    return (total_files() > 0);    
 }

 /**
 * @since 1.1
 * @return boolean
 */
 function has_files_for_loop()
 {
    $view = __v();
    return ($view->files and count($view->files));
 }