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
        $html .= '<div class="two columns alpha">';
        $html .= $this->_displayFieldLabel();
        $html .= $this->view->formSubmit('add_element_' . $this->_element['id'], __('Add Input'),
            array('class'=>'add-element'));
        
        $html .= '</div>'; // Close 'inputs' div

        $html .= '<div class="inputs five columns omega">';
        $html .= $this->_displayFormFields($extraFieldCount);
        $html .= $this->_displayTooltip();

        $html .= '</div>'; // Close 'inputs' div

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
    
    protected function _getFieldComment()
    {
        return html_escape($this->_element['comment']);
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
        $texts = $this->_record->getElementTextsByRecord($this->_element);
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
            $html .= $this->view->elementInput(
                $this->_element, $this->_record, $i,
                $this->_getValueForField($i), $this->_getHtmlFlagForField($i));
        }

        return $html;
    }

    protected function _displayTooltip()
    {
        // Tooltips should be in a <span class="tooltip">
        $html = '<p class="explanation">';
        $html .= __($this->_getFieldDescription()) .'</p>';
        $html .= '<p class="explanation">';
        $html .= $this->_getFieldComment() .'</p>';
        return $html;
    }

    protected function _displayFieldLabel()
    {
        return '<label>' . __($this->_getFieldLabel()) . '</label>';
    }
}
