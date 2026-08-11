<?php
/**
 * Omeka
 *
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Render a draggable list of sort tiles plus an "add tile" control.
 *
 * @package Omeka\View\Helper
 */
class Omeka_View_Helper_SortTilesWidget extends Zend_View_Helper_Abstract
{
    /**
     * @param string $type The browsable type, e.g. 'items' or 'collections'.
     * @param array $tiles List of ['label' => ..., 'field' => ...] tiles.
     * @param string $hiddenElementId Hidden form input to save tile order.
     * @return string HTML output
     */
    public function sortTilesWidget($type, array $tiles, $hiddenElementId)
    {
        $html = '<div class="sort-tiles-widget">';
        $html .= '<ul class="sortable-tiles" data-type="' . html_escape($type)
            . '" data-hidden-input="' . html_escape($hiddenElementId) . '">';
        foreach ($tiles as $tile) {
            $html .= $this->_tileMarkup($tile['label'], $tile['field']);
        }
        $html .= '</ul>';
        $html .= '<div class="add-sort-tile">';
        $html .= '<input type="text" class="new-tile-label" placeholder="' . html_escape(__('Label')) . '">';
        $html .= '<input type="text" class="new-tile-field" placeholder="' . html_escape(__('Dublin Core,Subject or added')) . '">';
        $html .= '<button type="button" class="add-sort-tile-button button small">' . html_escape(__('Add')) . '</button>';
        $html .= '</div>';
        $html .= '<p class="explanation">' . html_escape(__('Field must be a real column name (e.g. "added"), "random", or an "Element Set,Element Name" pair (e.g. "Dublin Core,Subject").')) . '</p>';
        $html .= '</div>';
        return $html;
    }

    protected function _tileMarkup($label, $field)
    {
        return '<li class="sort-tile">'
            . '<span class="tile-handle">&#9776;</span>'
            . '<span class="tile-label">' . html_escape($label) . '</span>'
            . '<input type="hidden" class="tile-field" value="' . html_escape($field) . '">'
            . '<button type="button" class="remove-sort-tile-button" title="' . html_escape(__('Remove')) . '">&times;</button>'
            . '</li>';
    }
}
