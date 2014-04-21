<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * A Zend_Form subclass that sets up forms to be properly displayed in Omeka.
 * 
 * @package Omeka\Form
 */
class Omeka_Form extends Zend_Form
{
    /**
     * Class name of Omeka DisplayGroup subclass.
     *
     * @var string
     */
    protected $_defaultDisplayGroupClass = 'Omeka_Form_DisplayGroup';
    
    /**
     * Whether or not to automatically apply Omeka-specific decorators
     * and styling information to form elements prior to rendering.
     *
     * @var boolean
     */
    protected $_autoApplyOmekaStyles = true;
    
    /**
     * Set up Omeka-specific form elements and decorators.
     *
     * @return void
     */
    public function init()
    {
        $this->addElementPrefixPath('Omeka_', 'Omeka/');
        $this->addPrefixPath('Omeka_Form_Element', 'Omeka/Form/Element/', 'element');
        
        // set the default element decorators
        $this->setElementDecorators($this->getDefaultElementDecorators());
    }
    
    /**
     * Set up base form decorators.
     *
     * @return void
     */
    public function loadDefaultDecorators()
    {
        $this->setDecorators(array(
            'FormElements',
            'Form'
        ));    
    }
    
    /**
     * Return default decorators for form elements.
     *
     * Makes form output conform to Omeka conventions.
     *
     * @return array
     */
    public function getDefaultElementDecorators()
    {
        // <div class="field">
        //     <label for="whatever">Label Name</label>
        // 
        //     <div class="inputs">
        //         <input name="whatever" type="text" />
        //         <ul class="errors">
        //             <li>Here's your error</li>
        //         </ul>
        //     </div>
        // 
        //     <p class="explanation">Here's the explanation</p>
        // 
        // </div>
        // <div>
        // <input type="submit" />
        // </div>
            
        return array(
                        array('Description', array('tag' => 'p', 'class' => 'explanation', 'escape'=>false)), 
                        'ViewHelper', 
                        array('Errors', array('class' => 'error')),
                        array(array('InputsTag' => 'HtmlTag'), array('tag' => 'div', 'class' => 'inputs five columns omega')), 
                        array('Label', array('tag' => 'div', 'tagClass' => 'two columns alpha')),
                        array(array('FieldTag' => 'HtmlTag'), array('tag' => 'div', 'class' => 'field'))
                    );
    }
    
    /**
     * Configure element styles / decorators based on the type of element.
     * 
     * This may be called after elements to the form, as the decorator 
     * configuration in init() runs before elements can be added.
     *
     * @return void
     */
    public function applyOmekaStyles()
    {
        foreach ($this->getElements() as $element) {
            if ($element instanceof Zend_Form_Element_Submit) {
                // All submit form elements should be wrapped in a div with 
                // no class.
                $element->setDecorators(array(
                    'ViewHelper', 
                    array('HtmlTag', array('tag' => 'div'))));
            } else if ($element instanceof Zend_Form_Element_File) {
                // Zend modifies the decorator order so we have to switch it
                // back here. The File decorator is first, we want it second.
                $decorators = $element->getDecorators();
                $decorators = array_slice($decorators, 1, 1, true)
                            + array_slice($decorators, 0, 1, true)
                            + array_slice($decorators, 2, null, true);
                $element->setDecorators($decorators);
            } else if($element instanceof Zend_Form_Element_Radio) {
                // Radio buttons must have a 'radio' class on the div wrapper.
                $element->getDecorator('InputsTag')->setOption('class', 'inputs radio');
                $element->setSeparator('');
            } else if ($element instanceof Zend_Form_Element_Hidden 
                    || $element instanceof Zend_Form_Element_Hash) {
                $element->setDecorators(array('ViewHelper'));
            }
        }
    }
    
    /**
     * Retrieve all of the form error messages as a nicely formatted string.  
     * 
     * Useful for displaying all form errors at the top of a form, or for flashing
     * form errors after redirects.
     * 
     * @since 1.2
     * @param string $messageDelimiter The string to display between different
     * error messages for an element.
     * @param string $elementDelimiter The string to display between different
     * elements.
     * @return string
     */
    public function getMessagesAsString($messageDelimiter = '  ', $elementDelimiter = ', ')
    {
        $errors = array();
        foreach ($this->getMessages() as $elementName => $errorArray) {
            $errors[] = Inflector::humanize($elementName) . ': ' . join($messageDelimiter, $errorArray);
        }
        return join($elementDelimiter, $errors);
    }
    
    /**
     * Specify whether or not to automatically apply Omeka-specific decorators
     * and styles prior to rendering the form.
     *
     * @param mixed $flag A boolean or boolean-equivalent.
     * @return void
     */
    public function setAutoApplyOmekaStyles($flag)
    {
        $this->_autoApplyOmekaStyles = (boolean)$flag;
    }
    
    /**
     * Apply Omeka default styles (if requested) just before rendering.
     *
     * @param Zend_View_Interface $view
     * @return string
     */
    public function render(Zend_View_Interface $view = null)
    {        
        if ($this->_autoApplyOmekaStyles) {
            $this->applyOmekaStyles();
        }
        return parent::render($view);
    }
    
    /**
     * Add a specific class name to an element.
     *
     * @param Zend_Form_Element $element
     * @param string $className
     * @return void
     */
    private function _addClassNameToElement(Zend_Form_Element $element, $className)
    {
        $existingClassName = $element->getAttrib('class');
        $newClassName = (strpos($existingClassName, $className) !== false) ? $existingClassName : "$existingClassName $className";
        $element->setAttrib('class', $newClassName);
    }
}
