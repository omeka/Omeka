<?php
/**
 * All theme Collection helper functions
 *
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka_ThemeHelpers
 * @subpackage CollectionHelpers
 */

/**
 * This is a similar interface to item(), except for accessing metadata about collections.
 *
 * As of the date of writing, it is greatly simplified in comparison to item(),
 * mostly because collections do not (and may not ever) utilize the 'elements'
 * metadata schema.
 *
 * @since 0.10
 * @see item()
 * @param string
 * @param array $options
 * @param Collection|null $collection Check for this specific collection record (current collection if null).
 * @return string|array
 */
function collection($fieldName, $options=array(), $collection=null)
{
    if (!$collection) {
        $collection = get_current_collection();
    }

    // Retrieve the data to display.
    switch (strtolower($fieldName)) {
        case 'id':
            $text = $collection->id;
            break;
        case 'name':
            $text = $collection->name;
            break;
        case 'description':
            $text = $collection->description;
            break;
        case 'public':
            $text = $collection->public;
            break;
        case 'featured':
            $text = $collection->featured;
            break;
        case 'date added':
            $text = $collection->added;
            break;
        case 'date modified':
            $text = $collection->modified;
            break;
        case 'collectors': // The names of collectors
            $text = $collection->getCollectors();
            break;
        default:
            throw new Exception(__('%s does not exist for collections!', $fieldName));
            break;
    }

    // Apply any options to it.
    if (isset($options['snippet'])) {
        $text = snippet($text, 0, (int)$options['snippet']);
    }

    // Escape it for display as HTML.
    if (!is_array($text)) {
        $text = html_escape($text);
    } else {
        $text = array_map('html_escape', $text);
    }

    // Return the join'd text
    if (isset($options['delimiter'])) {
        $text = join((string) $options['delimiter'], (array) $text);
    }
    return $text;
}

/**
 * Determine whether or not the collection has any collectors associated with it.
 *
 * @since 0.10
 * @return boolean
 */
function collection_has_collectors()
{
    return get_current_collection()->hasCollectors();
}

/**
 * Determine whether or not the current collection is featured.
 *
 * @since 0.10
 * @return boolean
 */
function collection_is_featured()
{
    return get_current_collection()->featured;
}

/**
 * Determine whether or not the current collection is public.
 *
 * @since 0.10
 * @return boolean
 */
function collection_is_public()
{
    return get_current_collection()->public;
}

/**
 * Returns the HTML markup for displaying a random featured collection.
 *
 * @since 0.10
 * @return string
 */
function display_random_featured_collection()
{
    $featuredCollection = random_featured_collection();
    $html = '<h2>' . __('Featured Collection') . '</h2>';
    if ($featuredCollection) {
        $html .= '<h3>' . link_to_collection($collectionTitle, array(), 'show', $featuredCollection) . '</h3>';
        if ($collectionDescription = collection('Description', array('snippet'=>150), $featuredCollection)) {
            $html .= '<p class="collection-description">' . $collectionDescription . '</p>';
        }

    } else {
        $html .= '<p>' . __('No featured collections are available.') . '</p>';
    }
    return $html;
}

/**
 * @since 0.10
 * @see get_item_by_id()
 * @param integer
 * @return Collection|null
 */
function get_collection_by_id($collectionId)
{
    return get_db()->getTable('Collection')->find($collectionId);
}

/**
 * Retrieve the Collection object for the current item.
 *
 * @since 0.10
 * @param Item|null Check for this specific item record (current item if null).
 * @internal This is meant to be a simple facade for access to the Collection
 * record.  Ideally theme writers won't have to interact with the actual object.
 * @access private
 * @return Collection
 */
function get_collection_for_item($item=null)
{
    if (!$item) {
        $item = get_current_item();
    }
    return $item->Collection;
}

/**
 * @since 0.10
 * @param array $params
 * @param integer $limit
 * @return array
 */
function get_collections($params = array(), $limit = 10)
{
    return get_db()->getTable('Collection')->findBy($params, $limit);
}

/**
 * Retrieve the set of collections that are being looped.
 *
 * @since 0.10
 * @return array
 */
function get_collections_for_loop()
{
    return __v()->collections;
}

/**
 * @since 0.10
 * @return Collection|null
 */
function get_current_collection()
{
    return __v()->collection;
}

/**
 * Determine whether or not there are any collections in the database.
 *
 * @deprecated since 1.5
 * @since 0.10
 * @return boolean
 */
function has_collections()
{
    return (total_collections() > 0);
}

/**
 * Determine whether there are any collections to loop through.
 *
 * @since 1.0
 * @see has_items_for_loop()
 * @return boolean
 */
function has_collections_for_loop()
{
    $view = __v();
    return $view->collections && count($view->collections);
}

/**
 * Loops through collections assigned to the current view.
 *
 * @since 0.10
 * @return mixed The current collection in the loop.
 */
function loop_collections()
{
    return loop_records('collections', get_collections_for_loop(), 'set_current_collection');
}

/**
 * Retrieve and loop through a subset of items in the collection.
 *
 * @since 0.10
 * @param integer $num
 * @param array $options Optional
 * @return Item|null
 */
function loop_items_in_collection($num = 10, $options = array())
{
    // Cache this so we don't end up calling the DB query over and over again
    // inside the loop.
    static $loopIsRun = false;

    if (!$loopIsRun) {
        // Retrieve a limited # of items based on the collection given.
        $items = get_items(array('collection'=>get_current_collection()->id), $num);
        set_items_for_loop($items);
        $loopIsRun = true;
    }

    $item = loop_items();
    if (!$item) {
        $loopIsRun = false;
    }
    return $item;
}

/**
 * @since 0.10
 * @param array $collections Set of Collection records to loop.
 * @return void
 */
function set_collections_for_loop($collections)
{
    __v()->collections = $collections;
}

/**
 * @since 0.10
 * @param Collection
 * @return void
 */
function set_current_collection($collection)
{
    __v()->collection = $collection;
}

/**
 * Retrieve the total number of items in the current collection.
 *
 * @since 0.10
 * @return integer
 */
function total_items_in_collection()
{
    return get_current_collection()->totalItems();
}

/**
 * Returns the most recent collections
 *
 * @param integer $num The maximum number of recent collections to return
 * @return array
 */
function recent_collections($num = 10)
{
    return get_collections(array('recent'=>true), $num);
}

/**
 * Returns a random featured collection.
 *
 * @return Collection
 */
function random_featured_collection()
{
    return get_db()->getTable('Collection')->findRandomFeatured();
}

/**
 * Returns the total number of collection
 *
 * @return integer
 */
function total_collections()
{
    return get_db()->getTable('Collection')->count();
}
