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
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Omeka_View_Helper_ElementForm
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

        $html .= $this->_displayValidationErrors();

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
     * The name of the data type for this element (relates to how the element is displayed).
     *
     * @return string
     */
    protected function _getElementDataType()
    {
        return $this->_element['data_type_name'];
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
        $fieldDataType = $this->_getElementDataType();

        // Plugins should apply a filter to this blank HTML in order to display it in a certain way.
        $html = '';

        $filterName = $this->_getPluginFilterForFormInput();

        $html = apply_filters($filterName, $html, $inputNameStem, $value, $options, $this->_record, $this->_element);

        // Short-circuit the default display functions b/c we already have the HTML we need.
        if (!empty($html)) {
            return $html;
        }

        //Create a form input based on the element type name
        switch ($fieldDataType) {

            //Tiny Text => input type="text"
            case 'Tiny Text':
                return $this->view->formTextarea(
                    $inputNameStem . '[text]',
                    $value,
                    array('class'=>'textinput', 'rows'=>2, 'cols'=>50));
                break;
            //Text => textarea
            case 'Text':
                return $this->view->formTextarea(
                    $inputNameStem . '[text]',
                    $value,
                    array('class'=>'textinput', 'rows'=>15, 'cols'=>50));
                break;
            case 'Date':
                return $this->_dateField(
                    $inputNameStem,
                    $value,
                    array());
                break;
            case 'Date Range':
                return $this->_dateRangeField(
                    $inputNameStem,
                    $value,
                    array());
            case 'Integer':
                return $this->view->formText(
                    $inputNameStem . '[text]',
                    $value,
                    array('class' => 'textinput', 'size' => 40));
            case 'Date Time':
                return $this->_dateTimeField(
                    $inputNameStem,
                    $value,
                    array());
            default:
                throw new Exception(__('Cannot display a form input for "%s" if element type name is not given!', $element['name']));
                break;
        }

    }

    // yyyy-mm-dd hh:mm:ss
    protected function _dateTimeField($inputNameStem, $value, $options = array())
    {
        list($date, $time) = explode(' ', $value);
        list($year, $month, $day) = explode('-', $date);
        list($hour, $minute, $second) = explode(':', $time);

        $html = '<div class="dateinput">';

        $html .= $this->view->formText($inputNameStem . '[year]', $year, array('class'=>'textinput', 'size'=>'4')) . '/';
        $html .= $this->view->formText($inputNameStem . '[month]', $month, array('class'=>'textinput', 'size'=>'2')) . '/';
        $html .= $this->view->formText($inputNameStem . '[day]', $day, array('class'=>'textinput', 'size'=>'2'));

        $html .= $this->view->formText($inputNameStem . '[hour]', $hour, array('class'=>'textinput', 'size'=>'2')) . ':';
        $html .= $this->view->formText($inputNameStem . '[minute]', $minute, array('class'=>'textinput', 'size'=>'2')) . ':';
        $html .= $this->view->formText($inputNameStem . '[second]', $second, array('class'=>'textinput', 'size'=>'2'));

        $html .= '<p class="hint">yyyy/mm/dd:hour:minute</p>';


        $html .= '</div>';

        return $html;
    }

    protected function _dateField($inputNameStem, $value, $options = array())
    {
        list($year, $month, $day) = explode('-', $value);

        $html = '<div class="dateinput">';

        $html .= $this->view->formText($inputNameStem . '[year]', $year, array('class'=>'textinput', 'size'=>'4'));
        $html .= $this->view->formText($inputNameStem . '[month]', $month, array('class'=>'textinput', 'size'=>'2'));
        $html .= $this->view->formText($inputNameStem . '[day]', $day, array('class'=>'textinput', 'size'=>'2'));

        $html .= '<p class="hint">yyyy-mm-dd</p>';

        $html .= '</div>';

        return $html;
    }

    protected function _dateRangeField($inputNameStem, $dateValue, $options = array())
    {
        list($startDate, $endDate) = explode(' ', $dateValue);

        $html = '<div class="dates">';

        // The name of the form elements for date ranges should eventually look like:
        // Elements[##][0][start][year], where ## is the element_id
        // Elements[##][0][end][month], etc.
        $startStem = $inputNameStem . '[start]';
        $endStem = $inputNameStem . '[end]';

        $html .= '<span>'. __('From') . '</span>';
        $html .= $this->_dateField($startStem, $startDate, $options);

        $html .= '<span>' . __('To') . '</span>';
        $html .= $this->_dateField($endStem, $endDate, $options);

        $html .= '</div>';

        return $html;
    }

    protected function _displayHtmlFlag($inputNameStem, $index)
    {
        $fieldDataType = $this->_getElementDataType();
        $noFlagTypes = array('Date', 'Date Range', 'Date Time', 'Integer');

        if (in_array($fieldDataType, $noFlagTypes)) {
            return '';
        }

        $isHtml = $this->_getHtmlFlagForField($index);

        // Add a checkbox for the 'html' flag (always for any field)
        $html = '<label class="use-html">' . __('Use HTML');
        $html .= $this->view->formCheckbox($inputNameStem . '[html]', 1, array('checked'=>$isHtml));
        $html .= '</label>';

        $html = apply_filters('element_form_display_html_flag', $html, $this->_element);
        return $html;
    }

    protected function _displayValidationErrors()
    {
        return form_error($this->_element['name']);
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

    /**
     * Zend Framework wants this.
     *
     */
    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
    }
}
