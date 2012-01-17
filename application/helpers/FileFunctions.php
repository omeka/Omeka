<?php
/**
 * All File helper functions
 *
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka_ThemeHelpers
 * @subpackage FileHelpers
 */

/**
 * @see display_files()
 * @uses display_files()
 * @param File $file One File record.
 * @param array $props
 * @param array $wrapperAttributes Optional XHTML attributes for the div wrapper
 * for the displayed file.  Defaults to array('class'=>'item-file').
 * @return string HTML
 */
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
 */
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
 * @since 0.10
 * @return File|null
 */
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
 */
function item_file($elementSetName, $elementName = null, $options = array(), $file = null)
{
    if (!$file) {
        $file = get_current_file();
    }
    return __v()->fileMetadata($file, $elementSetName, $elementName, $options);
}

/**
 * @since 0.10
 * @param File
 * @return void
 */
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
 */
function show_file_metadata(array $options = array(), $file = null)
{
    if (!$file) {
        $file = get_current_file();
    }
    return __v()->fileMetadataList($file, $options);
}

/**
 * Returns the most recent files
 *
 * @since 1.1
 * @param integer $num The maximum number of recent files to return
 * @return array
 */
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
 */
function get_files($params = array(), $limit = 10)
{
    return get_db()->getTable('File')->findBy($params, $limit);
}

/**
 * Retrieve the set of files for the current loop.
 *
 * @since 1.1
 * @return array
 */
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
    return loop_records('files', get_files_for_loop(), 'set_current_file');
}

/**
 * Determine whether or not there are any files in the database.
 *
 * @deprecated since 1.5
 * @since 1.1
 * @return boolean
 */
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
