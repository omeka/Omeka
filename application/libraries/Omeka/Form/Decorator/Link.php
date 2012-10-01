<?php

class Omeka_Form_Decorator_Link extends Zend_Form_Decorator_HtmlTag
{
    protected $content;
    
    public function getContent()
    {
        
        
    }
    public function render($content)
    {        
        
        $noAttribs = $this->getOption('noAttribs');
        
        $content = $this->getOption('content');
        $this->removeOption('content');
        $this->removeOption('noAttribs');
        $this->removeOption('openOnly');
        $this->removeOption('closeOnly');
        
        $attribs = null;
        if (!$noAttribs) {
            $attribs = $this->getOptions();
        }
        
        $html = $this->_getOpenTag('a', $attribs);
        $html .= $content;
        $html .= $this->_getCloseTag('a');
        return $html;
    }
    
    
    
}