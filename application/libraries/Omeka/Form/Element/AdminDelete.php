<?php

class Omeka_Form_Element_AdminDelete extends Omeka_Form_Element_AbstractAdmin
{

    protected function getDefaultDecorators()
    {

        $decorators = array();
        $decorators[] =  new Omeka_Form_Decorator_Link(array(              
                'class'=>'big red button',
                'href'=> record_url($this->_record, 'delete-confirm'),
                'content' => 'Delete'

        ));
        return $decorators;
    }
}