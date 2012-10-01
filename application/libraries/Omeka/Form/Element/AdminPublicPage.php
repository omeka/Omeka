<?php

class Omeka_Form_Element_AdminPublicPage extends Omeka_Form_Element_AbstractAdmin
{


    protected function getDefaultDecorators()
    {
        //$decorators = parent::getDefaultDecorators();
        $decorators = array();
        set_theme_base_url('public');
        $decorators[] =  new Omeka_Form_Decorator_Link(array('content'=>'View Public Page',                
                'class'=>'big blue button',
                'target'=>'_blank',
                'href'=> record_url($this->_record, 'show')

        ));
        revert_theme_base_url();
        
        return $decorators;
    }
}