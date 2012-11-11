<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * A Zend_Form subclass to set up a record editing form for the Omeka 2.0 admin 
 * user interface
 * 
 * @package Omeka\Form
 */

class Omeka_Form_Admin extends Omeka_Form
{
    protected $_editDisplayGroup;
    
    protected $_saveDisplayGroup;
    
    protected $_saveDisplayGroupActionDecorator;
    
    protected $_record;
    
    protected $_type;
    
    protected $_hasPublicPage = false;
        
    protected $_editGroupCssClass = 'seven columns alpha';
    
    protected $_saveGroupCssClass = 'three columns omega panel';
    
    
    
    
    public function init()
    {        
        parent::init();

        if(empty($this->_type)) {
            throw new Zend_Form_Exception("A type (often the record type) must be given to use Omeka_Form_Admin");
        }
        //instead of extending Zend_Form_DisplayGroup, setting up here so css classes can be options directly
        //on instantiating the form. If those classes should never change, then this probably should go to 
        //Omeka extensions of Zend_Form_DisplayGroup

        $this->_editDisplayGroup = new Zend_Form_DisplayGroup('edit-form', $this->getPluginLoader(self::DECORATOR));
        $this->_saveDisplayGroup = new Zend_Form_DisplayGroup('save', $this->getPluginLoader(self::DECORATOR));
        
        $this->addDisplayGroups(array($this->_editDisplayGroup, $this->_saveDisplayGroup));
        
        //create the decorators with CSS classes set up via options 
        $editDecorator = new Zend_Form_Decorator_HtmlTag(array('tag'=>'section', 'class'=>$this->_editGroupCssClass));
        $saveDecorator = new Zend_Form_Decorator_HtmlTag(array('tag'=>'section', 'id'=>'save', 'class'=>$this->_saveGroupCssClass));
        
                
        $hookDecoratorOptions = array('type'=>$this->_type, 'hasPublicPage'=>$this->_hasPublicPage);
        $this->_saveDisplayGroupActionDecorator = new Omeka_Form_Decorator_SavePanelAction($hookDecoratorOptions);
        if($this->_record) {
            $this->_saveDisplayGroupActionDecorator->setOption('record', $this->_record);
            $hookDecoratorOptions['record'] = $this->_record;
        }        
        $savePanelHookDecorator = new Omeka_Form_Decorator_SavePanelHook($hookDecoratorOptions);
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

    /**
     * Generalizes creating and adding new elements to one of the display groups
     * 
     * You can pass in either an Zend_Form_Element you have already created, or pass
     * parameters as you would to Zend_Form::addElement
     * 
     * @param string $group Either 'save' or 'edit'
     * @param Zend_Form_Element $element The element to add to the display group
     * @param string $name
     * @param array $options
     * @throws Zend_Form_Exception
     * @return Omeka_Form_Admin
     */
    
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
    
    /**
     * Get the decorators for the save display group
     * 
     * @return array The default decorators for the save display group
     */
    
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
    
    /**
     * Set the class for the edit display group.
     * 
     * You can alter the default css class for the edit group panel by passing in an
     * option for 'editGroupCssClass' when you create an instance of Omeka_Form_Admin.
     * This should be done very sparingly, as the default class is the best match to
     * existing admin theme look and feel
     * 
     * @param string $cssClass
     */
    
    public function setEditGroupCssClass($cssClass)
    {
        $this->_editGroupCssClass = $cssClass;
    }

    /**
     * Set the class for the save display group.
     *
     * You can alter the default css class for the save group panel by passing in an
     * option for 'editGroupCssClass' when you create an instance of Omeka_Form_Admin.
     * This should be done very sparingly, as the default class is the best match to
     * existing admin theme look and feel
     *
     * @param string $cssClass
     */
    
    
    public function setSaveGroupCssClass($cssClass)
    {
        $this->_saveGroupCssClass = $cssClass;
    }
    
    /**
     * Set the record type of the object being edited (e.g., 'item')
     * 
     * Pass in the recordType as part of the options array when you create an instance
     * 
     * @param string $type
     */
    
    public function setType($type)
    {
        $this->_type = $type;
    }
    
    /**
     * Set the record (if one exists) for the object being edited
     * 
     * Passing the record object as part of the options when you create the form
     * will automatically add 'Edit' and 'Delete' buttons to the save panel
     * 
     * @param Omeka_Record_AbstractRecord $record
     */
    
    public function setRecord($record)
    {
        $this->_record = $record;
    }
    
    /**
     * 
     * Set whether the save panel should display a link to the record's public page if it exists
     * 
     * By default, a link to a record's public page is available if it exists. Pass false as the
     * value of hasPublicPage in the options array to suppress this behavior.
     * 
     * 
     * @param bool $value true
     */
    
    
    public function setHasPublicPage($value = false)
    {
        $this->_hasPublicPage = $value;
    }    
}