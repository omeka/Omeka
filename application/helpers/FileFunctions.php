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
    $helper = new Omeka_View_Helper_Media;
    $output = '';
    foreach ($files as $file) {
        $output .= $helper->media($file, $props, $wrapperAttributes);
    }
    return $output;
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
        $file = get_current_record('file');
    }
    $helper = new Omeka_View_Helper_FileMetadata;
    return $helper->display($file, $options);
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
    return get_records('File', array('sort_field' => 'added', 'sort_dir' => 'd'), $num);
}
