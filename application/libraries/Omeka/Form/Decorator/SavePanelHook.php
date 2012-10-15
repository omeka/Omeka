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
        $prependHtml = fire_plugin_hooks('admin_append_to_panel_buttons');
        $appendHtml = fire_plugin_hooks('admin_append_to_panel_fields');
        return $prependHtml . $content . $appendHtml;
    }    
}