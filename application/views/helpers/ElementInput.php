<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2012
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka_ThemeHelpers
 * @subpackage Omeka_View_Helper
 */

/**
 * Generate the form markup for entering one HTML input for an Element.
 *
 * @package Omeka
 */
class Omeka_View_Helper_ElementInput extends Zend_View_Helper_Abstract
{
    /**
     * Element record to display the input for.
     *
     * @var Element
     */
    protected $_element;

    /**
     * Omeka_Record_AbstractRecord to display the input for.
     *
     * @var Omeka_Record_AbstractRecord
     */
    protected $_record;

    /**
     * Display one form input for an Element.
     *
     * @param Element $element The Element to display the input for.
     * @param Omeka_Record_AbstractRecord $record The record to display the 
     * input for.
     * @param int $index The index of this input. (starting at zero).
     * @param string $value The default value of this input.
     * @param bool $isHtml Whether this input's value is HTML.
     * @return string
     */
    public function elementInput(Element $element, Omeka_Record_AbstractRecord $record, $index = 0, $value = '', $isHtml = false)
    {
        $this->_element = $element;
        $this->_record = $record;

        $fieldStem = $this->_getFieldNameStem($index);
        
        $html = '<div class="input-block">'
              . '<div class="input">'
              . $this->_getFormInput($fieldStem, $value)
              . '</div>'
              . $this->_getFormControls()
              . $this->_getHtmlCheckbox($fieldStem, $index, $isHtml)
              . '</div>';

        return $html;
    }

    /**
     * Get the leading part of the "name" element for the input.
     *
     * @param int $index
     * @return string
     */
    protected function _getFieldNameStem($index)
    {
        return "Elements[" . $this->_element->id . "][$index]";
    }

    /**
     * Get the actual HTML input for this Element.
     *
     * @param string $inputNameStem
     * @param string $value
     * @return string
     */
    protected function _getFormInput($inputNameStem, $value)
    {
        // Plugins should apply a filter to this blank HTML in order to display it in a certain way.
        $html = '';

        $filterName = $this->_getPluginFilterForFormInput();

        $html = apply_filters($filterName, $html, $inputNameStem, $value, array(), $this->_record, $this->_element);

        // Short-circuit the default display functions b/c we already have the HTML we need.
        if (!empty($html)) {
            return $html;
        }

        return $this->view->formTextarea(
            $inputNameStem . '[text]',
            $value,
            array('class' => 'textinput', 'rows' => 3, 'cols' => 50));
    }

    /**
     * Get the "name" of the filter that allows plugins to override this form
     * input.
     *
     * @return array
     */
    protected function _getPluginFilterForFormInput()
    {
        return array(
            'Form',
            get_class($this->_record),
            $this->_element->set_name,
            $this->_element->name);
    }

    /**
     * Get the button that will allow a user to remove this form input.
     * The submit input has a class of 'add-element', which is used by the
     * Javascript to do stuff.
     *
     * @return string
     */
    protected function _getFormControls()
    {
        $html = '<div class="controls">'
              . $this->view->formSubmit(null, __('Remove'),
                    array('class' => 'remove-element red button'))
              . '</div>';

        return $html;
    }

    /**
     * Get the HTML checkbox that lets users toggle the editor.
     *
     * @param string $inputNameStem
     * @param int $index
     * @param bool $isHtml
     * @return string
     */
    protected function _getHtmlCheckbox($inputNameStem, $index, $isHtml)
    {
        // Add a checkbox for the 'html' flag (always for any field)
        $html = '<label class="use-html">'
              . __('Use HTML')
              . $this->view->formCheckbox($inputNameStem . '[html]', 1,
                    array('checked' => $isHtml))
              . '</label>';

        $html = apply_filters('element_form_display_html_flag', $html, $this->_element);
        return $html;
    }
}
