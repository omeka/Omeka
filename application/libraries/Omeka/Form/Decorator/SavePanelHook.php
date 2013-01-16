<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Decorator to add hooks into the admin save panel created via Omeka_Form_Admin
 * 
 * @package Omeka\Form\Decorator
 */
class Omeka_Form_Decorator_SavePanelHook extends Zend_Form_Decorator_Abstract
{    

    public function render($content)
    {        
        $type = $this->getType();
        $record = $this->getRecord();
        
        //hooks echo the content, so stuff the hook results into an output buffer
        //then put that ob content into a variable
        ob_start();
        fire_plugin_hook("admin_" . $type . "_panel_buttons", array('view'=>$this, 'record'=>$record));  
        $buttonsHtml = ob_get_contents();
        ob_end_clean();
        
        ob_start();
        fire_plugin_hook("admin_" . $type . "_panel_fields", array('view'=>$this, 'record'=>$record));
        $fieldsHtml = ob_get_contents();
        ob_end_clean();
        
        //this div was supplied by ActionPanelHook to allow for this replacement
        $html = str_replace("<div id='button-field-line'></div>", $buttonsHtml, $content);
        return $html . $fieldsHtml;
    }    
    
    /**
     * Get the record type if the Omeka_Form_Admin was created with that option set
     * 
     * @return mixed false or the record
     */
    
    public function getType()
    {
        if(isset($this->_options['type'])) {
            return $this->_options['type'];
        }
        return false;
        
    }
    
    /**
     * Get the record if the Omeka_Form_Admin was created with that option set
     * 
     * @return mixed false or the record
     */
    
    public function getRecord()
    {
        if(isset($this->_options['record'])) {
            return $this->_options['record'];
        }
        return false;
        
    }
    
}