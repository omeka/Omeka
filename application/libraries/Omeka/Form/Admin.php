<?php

/**
 * A Zend_Form subclass to set up a record editing form for the Omeka 2.0 admin user interface
 * 
 * 
 */

class Omeka_Form_Admin extends Omeka_Form
{
    protected $_editDisplayGroup;
    
    protected $_saveDisplayGroup;
    
    protected $_saveDisplayGroupActionDecorator;
    
    protected $_record;
    
    protected $_hasPublicPage = true;
        
    protected $_editGroupCssClass = 'seven columns alpha';
    
    protected $_saveGroupCssClass = 'three columns omega panel';
    
    
    
    
    public function init()
    {
        parent::init();
        
        //instead of extending Zend_Form_DisplayGroup, setting up here so css classes can be options directly
        //on instantiating the form. If those classes should never change, then this probably should go to 
        //Omeka extensions of Zend_Form_DisplayGroup

        $this->_editDisplayGroup = new Zend_Form_DisplayGroup('edit-form', $this->getPluginLoader(self::DECORATOR));
        $this->_saveDisplayGroup = new Zend_Form_DisplayGroup('save', $this->getPluginLoader(self::DECORATOR));
        
        $this->addDisplayGroups(array($this->_editDisplayGroup, $this->_saveDisplayGroup));
        
        //create the decorators with CSS classes set up via options 
        $editDecorator = new Zend_Form_Decorator_HtmlTag(array('tag'=>'div', 'class'=>$this->_editGroupCssClass));
        $saveDecorator = new Zend_Form_Decorator_HtmlTag(array('tag'=>'div', 'id'=>'save', 'class'=>$this->_saveGroupCssClass));
        $this->_saveDisplayGroupActionDecorator = new Omeka_Form_Decorator_SavePanelAction();
        if($this->_record) {
            $this->_saveDisplayGroupActionDecorator->setOption('record', $this->_record);
            $this->setHasPublicPage();          
        }
        
        $savePanelHookDecorator = new Omeka_Form_Decorator_SavePanelHook();
        //Pro tip: order of adding decorators matters! if reversed, group elements would appear after the div!
        $this->_editDisplayGroup->setDecorators(array('FormElements', $editDecorator));
        $this->_saveDisplayGroup->setDecorators(array($this->_saveDisplayGroupActionDecorator, 'FormElements', $savePanelHookDecorator,  $saveDecorator));        
    }
    
    /**
     * Add an element to the edit area
     * 
     * @see Zend_Form::addElement
     * @param Zend_Form_Element|string $element
     * @param string|null $name
     * @param array|null $options
     */
    
    public function addElementToEditGroup($element, $name, $options = null)
    {     
        return $this->addElementToDisplayGroup('edit', $element, $name, $options );
    }

    /**
     * Add an element to the save panel
     *
     * @see Zend_Form::addElement
     * @param Zend_Form_Element|string $element
     * @param string|null $name
     * @param array|null $options
     */
    
    public function addElementToSaveGroup($element, $name = null, $options = null)
    {
        return $this->addElementToDisplayGroup('save', $element, $name, $options );
    }
    
    protected function addElementToDisplayGroup($group, $element, $name = null, $options = null)    
    {
        
        if(is_string($element) && is_null($name)) {
            throw new Zend_Form_Exception('To add directly to a part of the admin edit page, you must give your element a name');
        }
        
        $this->addElement($element, $name, $options);
        
        $element = $this->getElement($name);

        switch($group) {
            case 'save':
                $this->_saveDisplayGroup->addElement($element);                
                $element->setDecorators($this->getSaveGroupDefaultElementDecorators());                
                break;
                
            case 'edit':
                $this->_editDisplayGroup->addElement($element);
                break;
        }     
        return $this;           
    }
    
    public function getSaveGroupDefaultElementDecorators()
    {
        return array(
                'ViewHelper',
                array('Description', array('tag' => 'p', 'class' => 'explanation')),
                'Errors',
                array(array('InputsTag' => 'HtmlTag'), array('tag' => 'div', 'class' => 'inputs')),
                array('Label', array('tag' => 'div', 'tagClass' => 'two columns alpha')),
                array(array('FieldTag' => 'HtmlTag'), array('tag' => 'div', 'class' => 'field'))
        );        
        
        
    }
    
    public function setEditGroupCssClass($cssClass)
    {
        $this->_editGroupCssClass = $cssClass;
    }
    
    public function setSaveGroupCssClass($cssClass)
    {
        $this->_saveGroupCssClass = $cssClass;
    }
    
    public function setRecord($record)
    {
        $this->_record = $record;
    }
    
    public function setHasPublicPage($value = true)
    {
        $this->_saveDisplayGroupActionDecorator->setOption('hasPublicPage', $value);
    }
    
}