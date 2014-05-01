<?php
/**
 * Omeka
 *
 * @copyright Copyright 2007-2014 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * View helper for processing shortcodes in text.
 *
 * @package Omeka\View\Helper
 */
class Omeka_View_Helper_Shortcodes extends Zend_View_Helper_Abstract
{
    /**
     * List of predefined shortcodes.
     *
     * @var array
     */
    protected static $shortcodeCallbacks = array(
        'recent_items' => 'Omeka_View_Helper_Shortcodes::shortcodeRecentItems',
        'featured_item' => 'Omeka_View_Helper_Shortcodes::shortcodeFeaturedItem',
        'items' => 'Omeka_View_Helper_Shortcodes::shortcodeItem',
        );

    /**
     * Add a new shortcode.
     *
     * @param string $shortcodeName Name of the shortcode
     * @param callback $callback Callback function that will return the
     *  shortcode content
     */
    public static function addShortcode($shortcodeName, $callback)
    {
        self::$shortcodeCallbacks[$shortcodeName] = $callback;

    }

    /**
     * Process any shortcodes in the given text.
     *
     * @param string $content
     * @return string
     */
    public function shortcodes($content)
    {
        if (false === strpos($content, '[')) {
            return $content;
        }
        $pattern =
        '/'
        . '\['          // Opening bracket
        . '(\w+)'       // Shortcode name
        . '\s*'         // Ignore whitespace trailing shortcode
        . '([^\]]*)'    // Capture attributes
        . '\]'          // Closing bracket
        . '/s';

        return preg_replace_callback($pattern, array($this, 'handleShortcode'), $content);
    }

    /**
     * Parse a detected shortcode and replace it with its actual content.
     *
     * @param array $matches
     * @return string
     */
    public function handleShortcode($matches)
    {
        $shortcodeName = $matches[1];
        if (!array_key_exists($shortcodeName, self::$shortcodeCallbacks)) {
            return $matches[0];
        }
        $args = $this->parseShortcodeAttributes($matches[2]);

        return call_user_func(self::$shortcodeCallbacks[$shortcodeName], $args, $this->view);
    }

    /**
     * Parse attributes section of a shortcode.
     *
     * @param string $text
     * @return array
     */
    public function parseShortcodeAttributes($text)
    {
        $args = array();
        $pattern =
                        // Start by looking for attribute values in double quotes
        '/(\w+)'        // Attribute key
        . '\s*=\s*'     // Whitespace and =
        . '"([^"]*)"'   // Attrbiute value
        . '(?:\s|$)'    // Space or end of string
        . '|'           // Or look for attribute values in single quotes
        . '(\w+)'       // Attribute key
        . '\s*=\s*'     // Whitespace and =
        . '\'([^\']*)\''// Attribute value
        .'(?:\s|$)'     // Space or end of string
        . '|'           // Or look for attribute values without quotes
        . '(\w+)'       // Attribute key
        . '\s*=\s*'     // Whitespace and =
        . '([^\s\'"]+)' // Attribute value
        . '(?:\s|$)'    // Space or end of string
        . '|'           // Or look for single value
        . '"([^"]*)"'   // Attribute value alone
        . '(?:\s|$)'    // Space or end of string
        . '|'           // Or look for single value
        . '(\S+)'       // Attribute value alone
        . '(?:\s|$)/';  // Space or end of string

        if (preg_match_all($pattern, $text, $match, PREG_SET_ORDER)) {
            foreach ($match as $m) {
                if (!empty($m[1]))
                    $args[strtolower($m[1])] = $m[2];
                elseif (!empty($m[3]))
                    $args[strtolower($m[3])] = $m[4];
                elseif (!empty($m[5]))
                    $args[strtolower($m[5])] = $m[6];
                elseif (isset($m[7]))
                    $args[] = $m[7];
                elseif (isset($m[8]))
                    $args[] = $m[8];
            }
        }
        else{
            $args = ltrim($text);
        }
        return $args;
    }

    /**
     * Shortcode for printing recently added items.
     *
     * @param array $args
     * @return string
     */
    public static function shortcodeRecentItems($args)
    {
        if (!isset($args['num'])) {
            $args['num'] = '5';
        }
        set_loop_records('items', get_recent_items($args['num']));
        if (has_loop_records('items')) {
            $recentItems = '<div id="recent-items">';
            $recentItems .= '<div class="items-list">';
            foreach (loop('items') as $item) {
                $recentItems .= '<div class="item">';
                $recentItems .= '<h3>' . link_to_item() . '</h3>';
                if (metadata('item', 'has thumbnail')) {
                    $recentItems .= '<div class="item-img">' . item_image('square_thumbnail') . '</div>';
                }
                if ($desc = metadata('item', array('Dublin Core', 'Description'), array('snippet'=>150))) {
                    $recentItems .= '<div class="item-description">' . $desc  . link_to_item('see more',(array('class'=>'show'))) . '</div>';
                }
                $recentItems .= '</div><!--end item-->';
            }
            $recentItems .= '</div><!-- end items-list-->';
            $recentItems .= '</div><!--end recent-items-->';
            return $recentItems;
        }
        else return 'No recent items.';
    }

    /**
     * Shortcode for printing featured items.
     *
     * @param array $args
     * @return string
     */
    public static function shortcodeFeaturedItem($args)
    {
        if (!isset($args['num'])) {
            $args['num'] = '1';
        }
        if (!isset($args['has_image'])) {
            $args['has_image'] = null;
        }
        $args['is_featured'] = 1;

        $featuredItem = '<div id="featured-item">';
        $featuredItem .= random_featured_items($args['num'], $args['has_image']);
        $featuredItem .= '</div><!--end featured-item-->';
        return $featuredItem;
    }

    public static function shortcodeItem($args)
    {
        $params = array();

        if (isset($args['is_featured'])) {
            $params['featured'] = $args['is_featured'];
        }

        if (isset($args['has_image'])) {
            $params['hasImage'] = $args['has_image'];
        }

        if (isset($args['collection'])) {
            $params['collection'] = $args['collection'];
        }

        if (isset($args['tags'])) {
            $params['tags'] = $args['tags'];
        }

        if (isset($args['user'])) {
            $params['users'] = $args['user'];
        }

        if (isset($args['ids'])) {
            $params['range'] = $args['ids'];
        }

        if (isset($args['num'])) {
            $limit = $args['num'];
        } else {
            $limit = 10; 
        }

        $items = get_records('Item', $params, $limit);

        $foo = '';
        foreach ($items as $item) {
           $foo .= $item->id;
        }

        return $foo;
    }
}
