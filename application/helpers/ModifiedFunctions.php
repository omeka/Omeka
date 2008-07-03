<?php
/**
 * This is where theme helpers with modified behavior or function signatures
 *  should go. Keep in mind that these functions have not been deprecated, but
 *  their behavior is different than in prior versions of the software.
 *
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * @since 7/3/08 This will retrieve featured items with or without images by
 *  default. The prior behavior was to retrieve only items with images by
 *  default.
 * @param string $hasImage 
 * @return Item
 */
function random_featured_item($hasImage=false) {
	return get_db()->getTable('Item')->findRandomFeatured($hasImage);
}