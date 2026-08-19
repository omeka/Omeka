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
     * @param string $currentDir Current default direction, 'a' or 'd'.
     * @param string $currentField Current default sort field.
     * @return string HTML output
     */
    public function sortTilesWidget($type, $legend, array $tiles, $currentDir = 'd', $currentField = '')
    {
        $hiddenElementId = html_escape($type) . '_sort_options';
        $html = '<fieldset class="sort-tiles-widget">';
        $html .= '<legend>' . html_escape($legend) . '</legend>';
        $html .= '<input type="hidden" name="' . $hiddenElementId . '" id="' . $hiddenElementId
            . '" value="' . html_escape(json_encode($tiles)) . '">';
        $html .= '<ul class="sortable sort-tiles" data-type="' . html_escape($type)
            . '" data-hidden-input="' . $hiddenElementId . '">';
        foreach ($tiles as $index => $tile) {
            $html .= $this->tileMarkup($type, $index, $tile['label'], $tile['field']);
        }
        $addToggleId = 'add-tile-toggle-' . html_escape($type);
        $addContentsId = 'add-tile-contents-' . html_escape($type);
        $html .= '<li class="add-tile-row">';
        $html .= '<div class="sortable-item drawer">';
        $html .= '<span class="drawer-name" id="' . $addToggleId . '-name">' . html_escape(__('Add Sort Option')) . '</span>';
        $html .= '<button type="button" id="' . $addToggleId . '" class="drawer-toggle" data-action-selector="opened" aria-expanded="false" aria-controls="' . $addContentsId . '" aria-labelledby="' . $addToggleId . ' ' . $addToggleId . '-name" title="' . html_escape(__('Show Options')) . '"><span class="icon" aria-hidden="true"></span></button>';
        $html .= '</div>';
        $html .= '<div class="drawer-contents" id="' . $addContentsId . '">';
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

    /**
     * Render a single sort tile's markup. Public so AppearanceController's
     * add-sort-tile action can render an identical tile server-side for
     * AJAX insertion, instead of duplicating this structure in JavaScript.
     *
     * @param string $type The browsable type, e.g. 'items' or 'collections'.
     * @param int $index Position among this widget's tiles, for unique ids.
     * @param string $label
     * @param string $field
     * @return string HTML output
     */
    public function tileMarkup($type, $index, $label, $field)
    {
        $nameId = html_escape($type) . '-tile-' . (int) $index . '-name';
        $undoId = html_escape($type) . '-tile-' . (int) $index . '-undo';
        $deleteId = html_escape($type) . '-tile-' . (int) $index . '-delete';
        return '<li class="element">'
            . '<div class="sortable-item drawer">'
            . '<span class="move icon" title="' . html_escape(__('Move')) . '" aria-label="' . html_escape(__('Move')) . '" aria-labelledby="' . $nameId . '"></span>'
            . '<span class="drawer-name tile-label" id="' . $nameId . '">' . html_escape($label) . '</span>'
            . '<input type="hidden" class="tile-field" value="' . html_escape($field) . '">'
            . '<button type="button" id="' . $undoId . '" class="undo-delete" data-action-selector="deleted" title="' . html_escape(__('Undo')) . '" aria-label="' . html_escape(__('Undo')) . ' ' . html_escape(__('Remove')) . '" aria-labelledby="' . $undoId . ' ' . $nameId . '"><span class="icon" aria-hidden="true"></span></button>'
            . '<button type="button" id="' . $deleteId . '" class="delete-drawer" data-action-selector="deleted" title="' . html_escape(__('Remove')) . '" aria-label="' . html_escape(__('Remove')) . '" aria-labelledby="' . $deleteId . ' ' . $nameId . '"><span class="icon" aria-hidden="true"></span></button>'
            . '</div>'
            . '</li>';
    }
}
