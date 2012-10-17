<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * @package Omeka\Form\Decorator
 */
class Omeka_Form_Decorator_SavePanelHook extends Zend_Form_Decorator_Abstract
{    

    public function render($content)
    {        

        $type = $this->getRecordType();
        $pluralType = Inflector::pluralize($type);
        $record = $this->getRecord();

        //hooks echo the content, so stuff the hook results into an output buffer
        //then put that ob content into a variable
        ob_start();
        fire_plugin_hook("admin_append_to_" . $pluralType . "_panel_buttons", array($type=>$record));        
        $buttonsHtml = ob_get_contents();
        ob_end_clean();
        
        ob_start();
        fire_plugin_hook("admin_append_to_" . $pluralType . "_panel_fields", array($type=>$record));
        $fieldsHtml = ob_get_contents();
        ob_end_clean();
        
        //the fields start with the first <div class="field">, so replace the first instance
        //with the buttonsHTML
        $pos = strpos($content, '<div class="field">' );
        $html = substr_replace($content , $buttonsHtml . '<div class="field">', $pos, 19 );
        return $html . $fieldsHtml;
    }    
    
    public function getRecordType()
    {
        if(isset($this->_options['recordType'])) {
            return $this->_options['recordType'];
        }
        return false;
        
    }
    
    public function getRecord()
    {
        if(isset($this->_options['record'])) {
            return $this->_options['record'];
        }
        return false;
        
    }
    
}