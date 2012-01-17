<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka_ThemeHelpers
 * @subpackage GeneralHelpers
 */

/**
 * Retrieve the view object.  Should be used only to avoid function scope
 * issues within other theme helper functions.
 *
 * @since 0.10
 * @access private
 * @return Omeka_View
 */
function __v()
{
    return Zend_Registry::get('view');
}

/**
 * Simple math for determining whether a number is odd or even
 *
 * @deprecated since 1.5
 * @return bool
 */
function is_odd($num)
{
    return $num & 1;
}

/**
 * Wrapper for the auto_discovery_link_tags() helper.
 *
 * @since 0.9
 * @uses auto_discovery_link_tags()
 * @return string HTML
 * @deprecated since 1.4
 */
function auto_discovery_link_tag(){
    return auto_discovery_link_tags();
}

/**
 * Output a <link> tag for the RSS feed so the browser can auto-discover the field.
 *
 * @since 1.4
 * @uses items_output_uri()
 * @return string HTML
 */
function auto_discovery_link_tags() {
    $html = '<link rel="alternate" type="application/rss+xml" title="'. __('Omeka RSS Feed') . '" href="'. html_escape(items_output_uri()) .'" />';
    $html .= '<link rel="alternate" type="application/atom+xml" title="'. __('Omeka Atom Feed') .'" href="'. html_escape(items_output_uri('atom')) .'" />';
    return $html;
}

/**
 * Includes a file from the common/ directory, passing variables into that script.
 *
 * @param string $file Filename
 * @param array $vars A keyed array of variables to be extracted into the script
 * @param string $dir Defaults to 'common'
 * @return void
 */
function common($file, $vars = array(), $dir = 'common')
{
    echo __v()->partial($dir . '/' . $file . '.php', $vars);
}

/**
 * Include the header script into the view
 *
 * @see common()
 * @param array Keyed array of variables
 * @param string $file Filename of header script (defaults to 'header')
 * @return void
 */
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
 */
function foot($vars = array(), $file = 'footer') {
    common($file, $vars);
}

/**
 * Retrieve a flashed message from the controller
 *
 * @param boolean $wrap Whether or not to wrap the flashed message in a div
 * with an appropriate class ('success','error','alert')
 * @return string
 */
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
 */
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
 * @param mixed $setCurrentRecordCallback The callback to set the current record
 * @return mixed The current record
 */
function loop_records($recordType, $records, $setCurrentRecordCallback=null)
{
    if (!$records) {
        return false;
    }

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
    if ($lastRecord && array_key_exists($recordType, $lastRecord) && $lastRecord[$recordType]) {
        release_object($lastRecord[$recordType]);
        $lastRecord[$recordType] = null;
    }

    // If we haven't reached the end of the loop, set the current record in the
    // loop and return it. This advances the array cursor so the next loop
    // iteration will get the next record.
    if (list($key, $record) = each($recordLoop[$recordType])) {

        $lastRecord[$recordType] = $record;

        if (is_callable($setCurrentRecordCallback)) {
            call_user_func($setCurrentRecordCallback, $record);
        } else {
            throw new Exception(__('Error: Invalid callback was provided for the loop.'));
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
    $actionName = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
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
    $html = '';

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

function browse_headings($headings) {
    $sortParam = Omeka_Db_Table::SORT_PARAM;
    $sortDirParam = Omeka_Db_Table::SORT_DIR_PARAM;
    $req = Zend_Controller_Front::getInstance()->getRequest();
    $currentSort = trim($req->getParam($sortParam));
    $currentDir = trim($req->getParam($sortDirParam));

    foreach ($headings as $label => $column) {
        if($column) {
            $urlParams = $_GET;
            $urlParams[$sortParam] = $column;
            $class = '';
            if ($currentSort && $currentSort == $column) {
                if ($currentDir && $currentDir == 'd') {
                    $class = 'class="sorting desc"';
                    $urlParams[$sortDirParam] = 'a';
                } else {
                    $class = 'class="sorting asc"';
                    $urlParams[$sortDirParam] = 'd';
                }
            }
            $url = uri(array(), null, $urlParams);
            echo "<th $class scope=\"col\"><a href=\"$url\">$label</a></th>";
        } else {
            echo "<th scope=\"col\">$label</th>";
        }
    }
}

/**
 * Returns a <body> tag with attributes. Attributes
 * can be filtered using the 'body_tag_attributes' filter.
 *
 * @since 1.4
 * @uses _tag_attributes()
 * @return string An HTML <body> tag with attributes and their values.
 */
function body_tag($attributes = array()) {
    $attributes = apply_filters('body_tag_attributes', $attributes);
    if ($attributes = _tag_attributes($attributes)) {
        return "<body ". $attributes . ">\n";
    }
    return "<body>\n";
}
