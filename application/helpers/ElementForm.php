<?php

/**
* 
*/
class Omeka_View_Helper_ElementForm
{
    /**
     * Element record to display the form for.
     *
     * @var Element
     **/
    protected $_element;
    
    protected $_record;
    
    public function elementForm(Element $element, Omeka_Record $record)
    {
        $this->_element = $element;
        
        // This will load all the Elements available for the record and fatal error
        // if $record does not use the ActsAsElementText mixin.
        $record->loadElementsAndTexts();
        $this->_record = $record;
        
        $html = '<div class="field">';
        
        // Put out the label for the field
        $html .= $this->_displayFieldLabel();
        
        $html .= $this->_displayValidationErrors();
        
        $html .= '<div class="inputs">';
        $html .= $this->_displayFormFields();
        $html .= '</div>'; // Close 'inputs' div
        
        $html .= $this->_displayTooltip();
        
        $html .= $this->_displayFormControls();
        
        $html .= '</div>'; // Close 'field' div
        
        return $html;
    }
    
    protected function _getFieldLabel()
    {
        return htmlentities($this->_element['name']);
    }
    
    protected function _getFieldDescription()
    {
        return htmlentities($this->_element['description']);
    }
    
    /**
     * How many form inputs to display for a given element.
     * 
     * @return integer
     **/
    protected function _getFormFieldCount()
    {
        $numFieldValues = count($this->getElementTexts());
        // Should always show at least one field.
        return $numFieldValues ? $numFieldValues : 1;
    }
    
    /**
     * The name of the data type for this element (relates to how the element is displayed).
     * 
     * @return string
     **/
    protected function _getElementDataType()
    {
        return $this->_element['data_type_name'];
    }
    
    /**
     * @todo Make this work with Date fields (and all others).
     * 
     * @param integer
     * @return mixed
     **/
    protected function _getPostValueForField($index)
    {
        $postArray = $_POST['Elements'][$this->_element['id']][$index];
        
        // Flatten this POST array into a string so as to be passed to the necessary helper functions.
        return ActsAsElementText::getTextStringFromFormPost($postArray, $this->_element);
    }
    
    protected function _getHtmlFlagForField($index)
    {
        if (!empty($_POST)) {
            $isHtml = (boolean) $_POST['Elements'][$this->_element['id']][$index]['html'];
        } else {
            $isHtml = (boolean) $this->getElementTexts($index)->html;
        }

        return $isHtml;
    }
    
    /**
     * Retrieve the form value for the field.
     * 
     * @param integer
     * @return string
     **/
    protected function _getValueForField($index)
    {        
        if ($post = $this->_getPostValueForField($index)) {
            return $post;
        } else {
            return $this->getElementTexts($index)->text;
        }
    }
    
    /**
     * If index is not given, return all texts.
     * 
     * @param string
     * @return void
     **/
    public function getElementTexts($index=null)
    {
        $texts = $this->_record->getTextsByElement($this->_element);
        if ($index !== null) {
            return $texts[$index];
        }
        return $texts;
    }
    
    protected function _displayFormFields()
    {        
        $fieldCount = $this->_getFormFieldCount();
        
        $html = '';
                
        for ($i=0; $i < $fieldCount; $i++) { 
            $html .= '<div class="input">';
            
            $fieldStem = $this->_getFieldNameStem($i);
            
            $html .= $this->_displayFormInput($fieldStem, $this->_getValueForField($i));
            
            $html .= $this->_displayHtmlFlag($fieldStem, $i);
                        
            $html .= '</div>';
        }
        
        return $html;
    }
    
    protected function _getFieldNameStem($index)
    {
        return "Elements[" . $this->_element['id'] . "][$index]";
    }
    
    protected function _displayFormInput($inputNameStem, $value, $options=array())
    {
        $fieldDataType = $this->_getElementDataType();
                        
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
            default:
                throw new Exception('Cannot display a form input for "' . 
                $element['name'] . '" if element type name is not given!');
                break;
        }
        
    }
    
    protected function _dateField($inputNameStem, $value, $options = array())
    {
        list($year, $month, $day) = explode('-', $value);

        $html .= '<div class="dateinput">';
    	
    	$html .= $this->view->formText($inputNameStem . '[year]', $year, array('class'=>'textinput', 'size'=>'4'));
    	$html .= $this->view->formText($inputNameStem . '[month]', $month, array('class'=>'textinput', 'size'=>'2'));
    	$html .= $this->view->formText($inputNameStem . '[day]', $day, array('class'=>'textinput', 'size'=>'2'));
    	
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
        
        $html .= '<span>From</span>';
        $html .= $this->_dateField($startStem, $startDate, $options);
        
        $html .= '<span>To</span>';
        $html .= $this->_dateField($endStem, $endDate, $options);
        
        $html .= '</div>';
        
        return $html;
    }
    
    protected function _displayHtmlFlag($inputNameStem, $index)
    {
        $isHtml = $this->_getHtmlFlagForField($index);
        
        // Add a checkbox for the 'html' flag (always for any field)
    	return $this->view->formCheckbox($inputNameStem . '[html]', 1, array('checked'=>$isHtml));
    }
    
    protected function _displayValidationErrors()
    {
        return form_error($this->_element['name']);
    }
    
    protected function _displayTooltip()
    {
        // Tooltips should be in a <span class="tooltip">
        $html .= '<img src="' . img('information.png') . '" />';
    	$html .= '<span class="tooltip">';
    	$html .= $this->_getFieldDescription() .'</span>';
    	
    	return $html;
    }
    
    protected function _displayFieldLabel()
    {
        return '<label>' . $this->_getFieldLabel() . '</label>';
    }
    
    /**
     *   The + button that will allow a user to add another form input.
     *   The name of the submit input is 'add_element_#' and it has a class of 
     *   'add-element', which is used by the Javascript to do stuff. * 
     */
    protected function _displayFormControls()
    {
        // Used by Javascript.
    	$html .= '<div class="controls">';

    	$html .= $this->view->formSubmit('add_element_' . $this->_element['id'], '+', 
    	    array('class'=>'add-element'));

    	$html .= $this->view->formSubmit('remove_element_' . $this->_element['id'], '-', 
    	    array('class'=>'remove-element'));

    	$html .= '</div>'; // Close 'controls' div
    	
    	return $html;        
    }
    
    /**
     * Zend Framework wants this.
     * 
     **/
    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
    }
}
