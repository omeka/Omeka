<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka_ThemeHelpers
 * @subpackage DataRetrievalHelpers
 */

/**
 * Returns the most recent tags.
 *
 * @param integer $limit The maximum number of recent tags to return
 * @return array
 */
function get_recent_tags($limit = 10)
{
    return get_records('Tag', array('sort_field' => 'time', 'sort_dir' => 'd'), $limit);
}

/**
 * Create a tag cloud made of divs that follow the hTagcloud microformat
 *
 * @param Omeka_Record_AbstractRecord|array $recordOrTags The record to retrieve 
 * tags from, or the actual array of tags
 * @param string|null The URI to use in the link for each tag.  If none given,
 *      tags in the cloud will not be given links.
 * @return string HTML for the tag cloud
 */
function tag_cloud($recordOrTags = null, $link = null, $maxClasses = 9, $tagNumber = false, $tagNumberOrder = null)
{
    if (!$recordOrTags) {
        $tags = array();
    } else if (is_string($recordOrTags)) {
        $tags = get_current_record($recordOrTags)->Tags;
    } else if ($recordOrTags instanceof Omeka_Record_AbstractRecord) {
        $tags = $recordOrTags->Tags;
    } else {
        $tags = $recordOrTags;
    }

    if (empty($tags)) {
        return '<p>' . __('No tags are available.') . '</p>';
    }

    //Get the largest value in the tags array
    $largest = 0;
    foreach ($tags as $tag) {
        if($tag["tagCount"] > $largest) {
            $largest = $tag['tagCount'];
        }
    }
    $html = '<div class="hTagcloud">';
    $html .= '<ul class="popularity">';

    if ($largest < $maxClasses) {
        $maxClasses = $largest;
    }

    foreach( $tags as $tag ) {
        $size = (int)(($tag['tagCount'] * $maxClasses) / $largest - 1);
        $class = str_repeat('v', $size) . ($size ? '-' : '') . 'popular';
        $html .= '<li class="' . $class . '">';
        if ($link) {
            $html .= '<a href="' . html_escape(url($link, array('tags' => $tag['name']))) . '">';
        }
        if($tagNumber && $tagNumberOrder == 'before') {
            $html .= ' <span class="count">'.$tag['tagCount'].'</span> ';
        }
        $html .= html_escape($tag['name']);
        if($tagNumber && $tagNumberOrder == 'after') {
            $html .= ' <span class="count">'.$tag['tagCount'].'</span> ';
        }
        if ($link) {
            $html .= '</a>';
        }
        $html .= '</li>' . "\n";
    }
    $html .= '</ul></div>';

    return $html;
}

/**
 * Output a tag string given an Item, Exhibit, or a set of tags.
 *
 * @internal Any record that has the Taggable module can be passed to this function
 * @param Omeka_Record_AbstractRecord|array $recordOrTags The record to retrieve 
 * tags from, or the actual array of tags
 * @param string|null $link The URL to use for links to the tags (if null, tags aren't linked)
 * @param string $delimiter ', ' (comma and whitespace) by default
 * @return string HTML
 */
function tag_string($recordOrTags = null, $link = 'items/browse', $delimiter = null)
{
    // Set the tag_delimiter option if no delimiter was passed.
    if (is_null($delimiter)) {
        $delimiter = get_option('tag_delimiter') . ' ';
    }

    if (!$recordOrTags) {
        $tags = array();
    } else if (is_string($recordOrTags)) {
        $tags = get_current_record($recordOrTags)->Tags;
    } else if ($recordOrTags instanceof Omeka_Record_AbstractRecord) {
        $tags = $recordOrTags->Tags;
    } else {
        $tags = $recordOrTags;
    }

    if (empty($tags)) {
        return '';
    }
    
    $tagStrings = array();
    foreach ($tags as $tag) {
        $name = $tag['name'];
        if (!$link) {
            $tagStrings[] = html_escape($name);
        } else {
            $tagStrings[] = '<a href="' . html_escape(url($link, array('tag' => $name))) . '" rel="tag">' . html_escape($name) . '</a>';
        }
    }
    return join(html_escape($delimiter), $tagStrings);
}
