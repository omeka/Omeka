<?php

class Omeka_Form_Decorator_SavePanelHook extends Zend_Form_Decorator_Abstract
{    
    public function render($content)
    {
        $prependHtml = fire_plugin_hooks('admin_append_to_panel_buttons');
        $appendHtml = fire_plugin_hooks('admin_append_to_panel_fields');
        return $prependHtml . $content . $appendHtml;
    }    
}