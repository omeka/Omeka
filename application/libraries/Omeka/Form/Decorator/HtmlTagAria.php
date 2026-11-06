<?php

class Omeka_Form_Decorator_HtmlTagAria extends Zend_Form_Decorator_HtmlTag
{

    protected function _htmlAttribs(array $attribs)
    {
        $element = $this->getElement();
        if ($element && $element->getLabel()) {
            $attribs['role'] = 'group';
            $attribs['aria-labelledby'] = $element->getId() . '-label';
        }
        return parent::_htmlAttribs($attribs);
    }
}

?>