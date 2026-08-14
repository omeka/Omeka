<?php
/**
 * Omeka
 *
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Render a draggable list of sort tiles plus an "add tile" row.
 *
 * @package Omeka\View\Helper
 */
class Omeka_View_Helper_SortTilesWidget extends Zend_View_Helper_Abstract
{
    /**
     * @param string $type The browsable type, e.g. 'items' or 'collections'.
     * @param string $legend Fieldset legend text, e.g. "Items Sort Options".
     * @param array $tiles List of ['label' => ..., 'field' => ...] tiles.
     * @param string $hiddenElementId The id of the hidden form input this
     *  widget's tiles should be serialized into on submit.
     * @param string $currentDir Current default direction, 'a' or 'd'.
     * @param string $currentField Current default sort field.
     * @return string HTML output
     */
    public function sortTilesWidget($type, $legend, array $tiles, $hiddenElementId, $currentDir = 'd', $currentField = '')
    {
        $html = '<fieldset class="sort-tiles-widget">';
        $html .= '<legend>' . html_escape($legend) . '</legend>';
        $html .= '<ul class="sortable sort-tiles" data-type="' . html_escape($type)
            . '" data-hidden-input="' . html_escape($hiddenElementId) . '">';
        foreach ($tiles as $tile) {
            $html .= $this->_tileMarkup($tile['label'], $tile['field']);
        }
        $html .= '<li class="add-tile-row">';
        $html .= '<div class="add-new">' . html_escape(__('Add Sort Option')) . '</div>';
        $html .= '<div class="drawer-contents opened">';
        $html .= '<label for="new-tile-field-' . html_escape($type) . '">' . html_escape(__('Field')) . '</label>';
        $html .= '<input type="text" class="new-tile-field textinput" size="30" id="new-tile-field-' . html_escape($type)
            . '" placeholder="' . html_escape(__('Dublin Core,Subject')) . '">';
        $html .= '<p class="explanation">' . html_escape(__('Enter an "Element Set,Element Name" pair (e.g. "Dublin Core,Subject") to sort by that field, or type "modified" or "added" to sort by date. The label is generated automatically.')) . '</p>';
        $html .= '<button type="button" class="add-sort-tile-button">' . html_escape(__('Add')) . '</button>';
        $html .= '</div>';
        $html .= '</li>';
        $html .= '</ul>';
        $fieldId = html_escape($type) . '_sort_default_field';
        $html .= '<div class="field">';
        $html .= '<div id="' . $fieldId . '-label" class="two columns alpha">';
        $html .= '<label for="' . $fieldId . '">' . html_escape(__('Default Sort Field')) . '</label>';
        $html .= '</div>';
        $html .= '<div class="inputs five columns omega">';
        $html .= '<select name="' . $fieldId . '" id="' . $fieldId . '" class="default-field-select">';
        foreach ($tiles as $tile) {
            $html .= '<option value="' . html_escape($tile['field']) . '"'
                . ($currentField === $tile['field'] ? ' selected="selected"' : '') . '>'
                . html_escape($tile['label']) . '</option>';
        }
        $html .= '</select>';
        $html .= '</div>';
        $html .= '</div>';

        $dirId = html_escape($type) . '_sort_default_dir';
        $html .= '<div class="field">';
        $html .= '<div id="' . $dirId . '-label" class="two columns alpha">';
        $html .= '<label for="' . $dirId . '">' . html_escape(__('Default Sort Direction')) . '</label>';
        $html .= '</div>';
        $html .= '<div class="inputs five columns omega">';
        $html .= '<select name="' . $dirId . '" id="' . $dirId . '">';
        $html .= '<option value="a"' . ($currentDir === 'a' ? ' selected="selected"' : '') . '>' . html_escape(__('Ascending')) . '</option>';
        $html .= '<option value="d"' . ($currentDir === 'd' ? ' selected="selected"' : '') . '>' . html_escape(__('Descending')) . '</option>';
        $html .= '</select>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</fieldset>';
        return $html;
    }

    protected function _tileMarkup($label, $field)
    {
        return '<li class="element">'
            . '<div class="sortable-item drawer">'
            . '<span class="move icon" title="' . html_escape(__('Move')) . '" aria-label="' . html_escape(__('Move')) . '"></span>'
            . '<span class="drawer-name tile-label">' . html_escape($label) . '</span>'
            . '<input type="hidden" class="tile-field" value="' . html_escape($field) . '">'
            . '<button type="button" class="delete-drawer" title="' . html_escape(__('Remove')) . '"><span class="icon" aria-hidden="true"></span></button>'
            . '</div>'
            . '</li>';
    }
}
