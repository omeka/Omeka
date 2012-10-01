<?php

abstract class Omeka_Form_Element_AbstractAdmin extends Zend_Form_Element_Xhtml
{
    protected $_record;
    
    public function init()
    {
        parent::init();
        $this->setDefaultDecorators();
    }
    
   
    public function setRecord($record)
    {
        $this->_record = $record;
    }
    
    public function setDefaultDecorators()
    {
        $decorators = $this->getDefaultDecorators();        
        $this->setDecorators($decorators);
    }
    
    abstract protected function getDefaultDecorators();
    
    
}