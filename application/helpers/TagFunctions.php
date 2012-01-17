<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka_ThemeHelpers
 * @subpackage DataRetrievalHelpers
 */

/**
 * @since 0.10
 * @see TagTable::applySearchFilters() for params
 * @param array $params
 * @param integer $limit
 * @return array
 */
function get_tags($params = array(), $limit = 10)
{
    return get_db()->getTable('Tag')->findBy($params, $limit);
}

/**
 * Returns the total number of tags
 *
 * @return integer
 */
function total_tags()
{
    return get_db()->getTable('Tag')->count();
}

/**
 * Returns the most recent tags.
 *
 * @param integer $limit The maximum number of recent tags to return
 * @return array
 */
function recent_tags($limit = 10)
{
    return get_tags(array('recent'=>true), $limit);
}

/**
 * Return the tags belonging to a particular user.
 *
 * @param Item $item
 * @return array An array of tag objects.
 */
function current_user_tags(Item $item)
{
    $user = current_user();
    if (!$item->exists()) {
        return false;
    }
    return get_tags(array('user'=>$user->id, 'record'=>$item, 'sort'=>array('alpha')));
}

/**
 * Retrieve a tag cloud of all the tags for the current item.
 *
 * @since 0.10
 * @see item_tags_as_string()
 * @param string|null $order Options include 'recent', 'most', 'least', 'alpha'.
 * If null, the tags will display in the order they were added to the database.
 * @param boolean $tagsAreLinked Optional Whether or not to make each tag a link
 * to browse all the items with that tag.  True by default.
 * @param Item|null $item Check for this specific item record (current item if null).
 * @param int|null $limit The maximum number of tags to return (get all of the tags if null)
 * @return string
 */
function item_tags_as_cloud($order = 'alpha', $tagsAreLinked = true, $item=null, $limit=null)
{
    if (!$item) {
        $item = get_current_item();
    }
    $tags = get_tags(array('sort'=>$order, 'record'=>$item), $limit);
    $urlToLinkTo = ($tagsAreLinked) ? uri('items/browse/tag/') : null;
    return tag_cloud($tags, $urlToLinkTo);
}

/**
 * Output the tags for the current item as a string.
 *
 * @todo Should this take a set of parameters instead of $order?  That would be
 * good for limiting the # of tags returned by the query.
 * @since 0.10
 * @see item_tags_as_cloud()
 * @param string $delimiter String that separates each tag.  Default is a comma
 * and space.
 * @param string|null $order Options include 'recent', 'most', 'least', 'alpha'.
 * If null, the tags will display in the order they were added to the database.
 * @param boolean $tagsAreLinked If tags should be linked or just represented as
 * text.  Default is true.
 * @param Item|null $item Check for this specific item record (current item if null).
 * @param int|null $limit The maximum number of tags to return (get all of the tags if null)
 * @return string HTML
 */
function item_tags_as_string($delimiter = null, $order = 'alpha',  $tagsAreLinked = true, $item=null, $limit=null)
{
    // Set the tag_delimiter option if no delimiter was passed.
    if (is_null($delimiter)) {
        $delimiter = get_option('tag_delimiter') . ' ';
    }

    if (!$item) {
        $item = get_current_item();
    }
    $tags = get_tags(array('sort'=>$order, 'record'=>$item), $limit);
    $urlToLinkTo = ($tagsAreLinked) ? uri('items/browse/tag/') : null;
    return tag_string($tags, $urlToLinkTo, $delimiter);
}

/**
 * Create a tag cloud made of divs that follow the hTagcloud microformat
 *
 * @param Omeka_Record|array $recordOrTags The record to retrieve tags from, or the actual array of tags
 * @param string|null The URI to use in the link for each tag.  If none given,
 *      tags in the cloud will not be given links.
 * @return string HTML for the tag cloud
 */
function tag_cloud($recordOrTags = null, $link = null, $maxClasses = 9)
{
    if (!$recordOrTags) {
        $recordOrTags = array();
    }

    if ($recordOrTags instanceof Omeka_Record) {
        $tags = $recordOrTags->Tags;
    } else {
        $tags = $recordOrTags;
    }

    if (empty($tags)) {
        $html = '<p>'. __('No tags are available.') .'</p>';
        return $html;
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
            $html .= '<a href="' . html_escape($link . '?tags=' . urlencode($tag['name'])) . '">';
        }
        $html .= html_escape($tag['name']);
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
 * @param Omeka_Record|array $recordOrTags The record to retrieve tags from, or the actual array of tags
 * @param string|null $link The URL to use for links to the tags (if null, tags aren't linked)
 * @param string $delimiter ', ' (comma and whitespace) by default
 * @return string HTML
 */
function tag_string($recordOrTags = null, $link=null, $delimiter=null)
{
    // Set the tag_delimiter option if no delimiter was passed.
    if (is_null($delimiter)) {
        $delimiter = get_option('tag_delimiter') . ' ';
    }

    if (!$recordOrTags) {
        $recordOrTags = array();
    }

    if ($recordOrTags instanceof Omeka_Record) {
        $tags = $recordOrTags->Tags;
    } else {
        $tags = $recordOrTags;
    }

    $tagString = '';
    if (!empty($tags)) {
        $tagStrings = array();
        foreach ($tags as $key=>$tag) {
            if (!$link) {
                $tagStrings[$key] = html_escape($tag['name']);
            } else {
                $tagStrings[$key] = '<a href="' . html_escape($link.urlencode($tag['name'])) . '" rel="tag">'.html_escape($tag['name']).'</a>';
            }
        }
        $tagString = join(html_escape($delimiter),$tagStrings);
    }
    return $tagString;
}
