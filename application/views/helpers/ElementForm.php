<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka_ThemeHelpers
 * @subpackage Omeka_View_Helper
 */

/**
 * Generate the form markup for entering element text metadata.
 *
 * @package Omeka
 */
class Omeka_View_Helper_ElementForm extends Zend_View_Helper_Abstract
{
    /**
     * Element record to display the form for.
     *
     * @var Element
     */
    protected $_element;

    protected $_record;

    public function elementForm(Element $element, Omeka_Record $record,
        $options = array())
    {
        $divWrap = isset($options['divWrap']) ? $options['divWrap'] : true;
        $extraFieldCount = isset($options['extraFieldCount']) ? $options['extraFieldCount'] : null;

        $this->_element = $element;

        // This will load all the Elements available for the record and fatal error
        // if $record does not use the ActsAsElementText mixin.
        $record->loadElementsAndTexts();
        $this->_record = $record;

        $html = $divWrap ? '<div class="field" id="element-' . html_escape($element->id) . '">' : '';

        // Put out the label for the field
        $html .= $this->_displayFieldLabel();

        $html .= '<div class="inputs">';
        $html .= $this->_displayFormFields($extraFieldCount);
        $html .= '</div>'; // Close 'inputs' div

        $html .= $this->view->formSubmit('add_element_' . $this->_element['id'], __('Add Input'),
            array('class'=>'add-element'));

        $html .= $this->_displayTooltip();


        $html .= $divWrap ? '</div>' : ''; // Close 'field' div

        return $html;
    }

    protected function _getFieldLabel()
    {
        return html_escape($this->_element['name']);
    }

    protected function _getFieldDescription()
    {
        return html_escape($this->_element['description']);
    }

    protected function _isPosted()
    {
        $postArray = $this->_getPostArray();
        return !empty($postArray);
    }

    protected function _getPostArray()
    {
        if (array_key_exists('Elements', $_POST)) {
            return $_POST['Elements'][$this->_element['id']];
        } else {
            return array();
        }
    }

    /**
     * How many form inputs to display for a given element.
     *
     * @return integer
     */
    protected function _getFormFieldCount()
    {
        if ($this->_isPosted()) {
            $numFieldValues = count($this->_getPostArray());
        } else {
            $numFieldValues = count($this->getElementTexts());
        }

        // Should always show at least one field.
        return $numFieldValues ? $numFieldValues : 1;
    }

    /**
     * @uses ActsAsElementText::getTextStringFromFormPost()
     * @param integer
     * @return mixed
     */
    protected function _getPostValueForField($index)
    {
        if (!$this->_isPosted()) {
            // Return if there are no posted data.
            return null;
        }

        $postArray = $this->_getPostArray();
        if (!array_key_exists($index, $postArray)) {
            return '';
        }
        $postArray = $postArray[$index];

        // Flatten this POST array into a string so as to be passed to the
        // necessary helper functions.
        return $this->_record->getTextStringFromFormPost($postArray, $this->_element);
    }

    protected function _getHtmlFlagForField($index)
    {
        $isHtml = false;
        if ($this->_isPosted()) {
            $isHtml = (boolean) @$_POST['Elements'][$this->_element['id']][$index]['html'];
        } else {
            $elementText = $this->getElementTexts($index);

            if (isset($elementText)) {
                $isHtml = (boolean) $elementText->html;
            }
        }

        return $isHtml;
    }

    /**
     * Retrieve the form value for the field.
     *
     * @param integer
     * @return string
     */
    protected function _getValueForField($index)
    {
        if ($this->_isPosted()) {
            return $this->_getPostValueForField($index);
        } else {
            $elementText = $this->getElementTexts($index);

            if (isset($elementText)) {
                return $elementText->text;
            } else {
                return null;
            }
        }
    }

    /**
     * If index is not given, return all texts.
     *
     * @param string
     * @return void
     */
    public function getElementTexts($index=null)
    {
        $texts = $this->_record->getTextsByElement($this->_element);
        if ($index !== null) {
            if (array_key_exists($index, $texts)) {
                return $texts[$index];
            } else {
                return null;
            }
        }
        return $texts;
    }

    protected function _displayFormFields($extraFieldCount = null)
    {
        $fieldCount = $this->_getFormFieldCount() + (int) $extraFieldCount;

        $html = '';

        for ($i=0; $i < $fieldCount; $i++) {
            $html .= '<div class="input-block">';

            $fieldStem = $this->_getFieldNameStem($i);

            $html .= '<div class="input">';
            $html .= $this->_displayFormInput($fieldStem, $this->_getValueForField($i));
            $html .= '</div>';

            $html .= $this->_displayFormControls();

            $html .= $this->_displayHtmlFlag($fieldStem, $i);

            $html .= '</div>';
        }

        return $html;
    }

    protected function _getFieldNameStem($index)
    {
        return "Elements[" . $this->_element['id'] . "][$index]";
    }

    protected function _getPluginFilterForFormInput()
    {
        return array(
            'Form',
            get_class($this->_record),
            $this->_element->set_name,
            $this->_element->name);
    }

    protected function _displayFormInput($inputNameStem, $value, $options=array())
    {
        // Plugins should apply a filter to this blank HTML in order to display it in a certain way.
        $html = '';

        $filterName = $this->_getPluginFilterForFormInput();

        $html = apply_filters($filterName, $html, $inputNameStem, $value, $options, $this->_record, $this->_element);

        // Short-circuit the default display functions b/c we already have the HTML we need.
        if (!empty($html)) {
            return $html;
        }

        return $this->view->formTextarea(
            $inputNameStem . '[text]',
            $value,
            array('class' => 'textinput', 'rows' => 3, 'cols' => 50));
    }

    protected function _displayHtmlFlag($inputNameStem, $index)
    {
        $isHtml = $this->_getHtmlFlagForField($index);

        // Add a checkbox for the 'html' flag (always for any field)
        $html = '<label class="use-html">' . __('Use HTML');
        $html .= $this->view->formCheckbox($inputNameStem . '[html]', 1, array('checked'=>$isHtml));
        $html .= '</label>';

        $html = apply_filters('element_form_display_html_flag', $html, $this->_element);
        return $html;
    }

    protected function _displayTooltip()
    {
        // Tooltips should be in a <span class="tooltip">
        $html = '<p class="explanation">';
        $html .= __($this->_getFieldDescription()) .'</p>';

        return $html;
    }

    protected function _displayFieldLabel()
    {
        return '<label>' . __($this->_getFieldLabel()) . '</label>';
    }

    /**
     *   The + button that will allow a user to add another form input.
     *   The name of the submit input is 'add_element_#' and it has a class of
     *   'add-element', which is used by the Javascript to do stuff. *
     */
    protected function _displayFormControls()
    {
        // Used by Javascript.
        $html = '<div class="controls">';

        $html .= $this->view->formSubmit('remove_element_' . $this->_element['id'], __('Remove'),
            array('class'=>'remove-element'));

        $html .= '</div>'; // Close 'controls' div

        return $html;
    }
}
