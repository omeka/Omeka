<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Generate the form markup for entering one HTML input for an Element.
 * 
 * @package Omeka\View\Helper
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

        $inputNameStem = "Elements[" . $this->_element->id . "][$index]";
        
        $components = array(
            'input' => $this->_getInputComponent($inputNameStem, $value),
            'form_controls' => $this->_getControlsComponent(),
            'html_checkbox' => $this->_getHtmlCheckboxComponent($inputNameStem, $isHtml),
            'html' => null
        );
        
        $filterName = array('ElementInput',
                            get_class($this->_record),
                            $this->_element->set_name,
                            $this->_element->name);
        
        // Plugins should apply a filter to this HTML in order to display it in a certain way.
        $components = apply_filters($filterName,
                                    $components, 
                                    array('input_name_stem' => $inputNameStem, 
                                          'value' => $value, 
                                          'record' => $this->_record, 
                                          'element' => $this->_element,
                                          'index' => $index,
                                          'is_html' => $isHtml));
                                    
        
        if ($components['html'] !== null) {
            return strval($components['html']);
        }
        
        $html = '<div class="input-block">'
              . '<div class="input">'
              . $components['input']
              . '</div>'
              . $components['form_controls']
              . $components['html_checkbox']
              . '</div>';

        return $html;
    }

    /**
     * Get the actual HTML input for this Element.
     *
     * @param string $inputNameStem
     * @param string $value
     * @return string
     */
    protected function _getInputComponent($inputNameStem, $value)
    {        
        $html = $this->view->formTextarea($inputNameStem . '[text]',
                                          $value,
                                          array('rows' => 3, 
                                                'cols' => 50));
        return $html;
    }

    /**
     * Get the button that will allow a user to remove this form input.
     * The submit input has a class of 'add-element', which is used by the
     * Javascript to do stuff.
     *
     * @return string
     */
    protected function _getControlsComponent()
    {
        $html = '<div class="controls">'
              . $this->view->formSubmit(null, 
                                       __('Remove'),
                                       array('class' => 'remove-element red button'))
              . '</div>';

        return $html;
    }

    /**
     * Get the HTML checkbox that lets users toggle the editor.
     *
     * @param string $inputNameStem
     * @param bool $isHtml
     * @return string
     */
    protected function _getHtmlCheckboxComponent($inputNameStem, $isHtml)
    {
        // Add a checkbox for the 'html' flag (always for any field)
        $html = '<label class="use-html">'
              . __('Use HTML')
              . $this->view->formCheckbox($inputNameStem . '[html]', 1, array(
                'checked' => $isHtml, 'class' => 'use-html-checkbox'))
              . '</label>';

        return $html;
    }
}
