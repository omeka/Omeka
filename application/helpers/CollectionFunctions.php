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
 * Determine whether or not the collection has any collectors associated with it.
 *
 * @since 0.10
 * @return boolean
 */
function collection_has_collectors()
{
    return get_current_record('collection')->hasCollectors();
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
        if ($collectionDescription = metadata($featuredCollection, 'Description', array('snippet'=>150))) {
            $html .= '<p class="collection-description">' . $collectionDescription . '</p>';
        }

    } else {
        $html .= '<p>' . __('No featured collections are available.') . '</p>';
    }
    return $html;
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
        $item = get_current_record('item');
    }
    return $item->Collection;
}

/**
 * Retrieve the total number of items in the current collection.
 *
 * @since 0.10
 * @return integer
 */
function total_items_in_collection()
{
    return get_current_record('collection')->totalItems();
}

/**
 * Returns the most recent collections
 *
 * @param integer $num The maximum number of recent collections to return
 * @return array
 */
function recent_collections($num = 10)
{
    return get_records('Collection', array('sort_field' => 'added', 'sort_dir' => 'd'), $num);
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
