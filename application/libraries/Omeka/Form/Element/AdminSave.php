<?php

class Omeka_Form_Element_AdminSave extends Omeka_Form_Element_AbstractAdmin
{
    
    
    protected function getDefaultDecorators()
    {
        //$decorators = parent::getDefaultDecorators();
        $decorators = array();
        $decorators[] = new Zend_Form_Decorator_HtmlTag(array('tag'=>'input',
                                                           'id'=>'save-changes',
                                                           'class'=>'submit big green button',
                                                           'type'=>'submit',
                                                           'value'=>'Save Changes',
                                                           'name'=>'submit'                
                                                            ));
        
        return $decorators;
        
    }
    
}