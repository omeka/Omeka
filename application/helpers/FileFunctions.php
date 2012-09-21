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
 * @uses files_markup()
 * @param File $file One File record.
 * @param array $props
 * @param array $wrapperAttributes Optional XHTML attributes for the div wrapper
 * for the displayed file.  Defaults to array('class'=>'item-file').
 * @return string HTML
 */
function file_markup($file, array $props=array(), $wrapperAttributes = array('class'=>'item-file'))
{
    return files_markup(array($file), $props, $wrapperAttributes);
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
function files_markup($files, array $props = array(), $wrapperAttributes = array('class'=>'item-file'))
{
    $helper = new Omeka_View_Helper_Media;
    $output = '';
    foreach ($files as $file) {
        $output .= $helper->media($file, $props, $wrapperAttributes);
    }
    return $output;
}

/**
 * Retrieve display for ID3 metadata for the current file.
 *
 * @since 2.0
 * @param array $options Optional
 * @param File|null $file Optional
 * @return string|array
 */
function file_id3_metadata(array $options = array(), $file = null)
{
    if (!$file) {
        $file = get_current_record('file');
    }
    return get_view()->fileID3Metadata($file, $options);
}

/**
 * Returns the most recent files
 *
 * @since 1.1
 * @param integer $num The maximum number of recent files to return
 * @return array
 */
function get_recent_files($num = 10)
{
    return get_records('File', array('sort_field' => 'added', 'sort_dir' => 'd'), $num);
}
