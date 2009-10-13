<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka_ThemeHelpers
 * @subpackage GeneralHelpers
 **/

/**
 * Retrieve the view object.  Should be used only to avoid function scope
 * issues within other theme helper functions.
 * 
 * @since 0.10
 * @access private
 * @return Omeka_View
 **/
function __v()
{
    return Zend_Registry::get('view');
}

/**
 * Simple math for determining whether a number is odd or even
 *
 * @return bool
 **/
function is_odd($num)
{
	return $num & 1;
}

/**
 * Output a <link> tag for the RSS feed so the browser can auto-discover the field.
 * 
 * @since 0.9
 * @uses items_output_uri()
 * @return string HTML
 **/
function auto_discovery_link_tag(){
	$html = '<link rel="alternate" type="application/rss+xml" title="Omeka RSS Feed" href="'. html_escape(items_output_uri()) .'" />';
	return $html;
}

/**
 * Includes a file from the common/ directory, passing variables into that script.
 * 
 * @param string $file Filename
 * @param array $vars A keyed array of variables to be extracted into the script
 * @param string $dir Defaults to 'common'
 * @return void
 **/
function common($file, $vars = array(), $dir = 'common') 
{
	$path = physical_path_to($dir . DIRECTORY_SEPARATOR . $file . '.php');
	extract($vars);
	include $path;
}

/**
 * Include the header script into the view
 * 
 * @see common()
 * @param array Keyed array of variables
 * @param string $file Filename of header script (defaults to 'header')
 * @return void
 **/
function head($vars = array(), $file = 'header') 
{
	common($file, $vars);
}

/**
 * Include the footer script into the view
 * 
 * @param array Keyed array of variables
 * @param string $file Filename of footer script (defaults to 'footer')
 * @return void
 **/
function foot($vars = array(), $file = 'footer') {
	common($file, $vars);
}

/**
 * Retrieve a flashed message from the controller
 * 
 * @param boolean $wrap Whether or not to wrap the flashed message in a div
 * with an appropriate class ('success','error','alert')
 * @return string
 **/
function flash($wrap=true)
{
	$flash = new Omeka_Controller_Flash;
	
	switch ($flash->getStatus()) {
		case Omeka_Controller_Flash::SUCCESS:
			$wrapClass = 'success';
			break;
		case Omeka_Controller_Flash::VALIDATION_ERROR:
			$wrapClass = 'error';
			break;
		case Omeka_Controller_Flash::GENERAL_ERROR:
			$wrapClass = 'error';
			break;
		case Omeka_Controller_Flash::ALERT:
			$wrapClass = 'alert';
			break;		
		default:
			return;
			break;
	}
	
	return $wrap ? 
		'<div class="' . $wrapClass . '">'.nl2br(html_escape($flash->getMsg())).'</div>' : 
		$flash->getMsg();
}

/**
 * Retrieve the value of a particular site setting.  This can be used to display
 * any option that would be retrieved with get_option().
 *
 * Content for any specific option can be filtered by using a filter named
 * 'display_setting_(option)' where (option) is the name of the option, e.g.
 * 'display_setting_site_title'.
 * 
 * @uses get_option()
 * @since 0.9
 * @return string
 **/
function settings($name) 
{
	$name = apply_filters("display_setting_$name", get_option($name));
	$name = html_escape($name);
	return $name;
}

/**
 * Loops through a specific record set, setting the current record to a 
 * globally accessible scope and returning it.  Records are only valid for
 * the current call to loop_records (i.e., the next call to loop_records()
 * will release the previously-returned item).
 * 
 * @since 0.10
 * @see loop_items()
 * @see loop_files_for_item()
 * @see loop_collections()
 * @param string $recordType The type of record to loop through
 * @param mixed $records The iterable set of records
 * @return mixed The current record
 */
function loop_records($recordType, $records)
{
    // If this is the first call to loop_records(), set a static record loop and 
    // set it to NULL.
    static $recordLoop = null;
    
    // If this is the first call, set an array holding the last-returned
    // record from the loop, for each record type.  Initially set to null.
    static $lastRecord = null;
    
    // If the record type index does not exist, set it with the provided 
    // records. We do this so multiple record types can coexist.
    if (!isset($recordLoop[$recordType])) {
        $recordLoop[$recordType] = $records;
    }
    
    // If there is a previously-returned record from this loop, release the
    // object before returning the next record.
    if ($lastRecord[$recordType]) {
        release_object($lastRecord[$recordType]);
        $lastRecord[$recordType] = null;
    }
    
    // If we haven't reached the end of the loop, set the current record in the 
    // loop and return it. This advances the array cursor so the next loop 
    // iteration will get the next record.
    if (list($key, $record) = each($recordLoop[$recordType])) {
        
        $lastRecord[$recordType] = $record;
        
        // Set the current records, depending on the record type.
        switch ($recordType) {
            case 'items':
                set_current_item($record);
                break;
			case 'files':
				set_current_file($record);
				break;
            case 'files_for_item':
                set_current_file($record);
                break;
            case 'collections':
                set_current_collection($record);
                break;
            case 'item_types':
                set_current_item_type($record);
                break;
            default:
                throw new Exception('Error: Invalid record type was provided for the loop.');
                break;
        }
        
        return $record;
    }
    
    // Reset the particular record loop if the loop has finished (so we can run 
    // it again if necessary). Return false to indicate the end of the loop.
    unset($recordLoop[$recordType]);
    return false;
}

/**
 * Get all output formats available in the current action.
 * 
 * @return array A sorted list of contexts.
 */
function current_action_contexts()
{
    $actionName = Omeka_Context::getInstance()->getRequest()->getActionName();
    $contexts = Zend_Controller_Action_HelperBroker::getStaticHelper('contextSwitch')->getActionContexts($actionName);
    sort($contexts);
    return $contexts;
}

/**
 * Builds an HTML list containing all available output format contexts for the 
 * current action.
 * 
 * @param bool True = unordered list; False = use delimiter
 * @param string If the first argument is false, use this as a delimiter.
 * @return string HTML
 */
function output_format_list($list = true, $delimiter = ' | ')
{
    $actionContexts = current_action_contexts();
    
    // Do not display the list if there are no output formats available in the 
    // current action.
    if (empty($actionContexts)) {
        return false;
    }
    
    // Unordered list format.
    if ($list) {
        $html .= '<ul id="output-format-list">';
        foreach ($actionContexts as $key => $actionContext) {
            $query = $_GET;
            $query['output'] = $actionContext;
            $html .= '<li><a href="' . html_escape(uri() . '?' . http_build_query($query)) . '">' . $actionContext . '</a></li>';
        }
        $html .= '</ul>';
    
    // Delimited format.
    } else {
        $html .= '<p id="output-format-list">';
        foreach ($actionContexts as $key => $actionContext) {
            $query = $_GET;
            $query['output'] = $actionContext;
            $html .= '<a href="' . html_escape(uri() . '?' . http_build_query($query)) . '">' . $actionContext . '</a>';
            $html .= (count($actionContexts) - 1) == $key ? '' : $delimiter;
        }
        $html .= '</p>';
    }
    
    return $html;
}
