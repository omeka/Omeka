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
class Omeka_Form_Decorator_SavePanelAction extends Zend_Form_Decorator_Abstract
{
    
    protected $content;    
    
    public function getContent()
    {
        return $this->content;
    
    }
    
    public function getRecord()
    {
        if(isset($this->_options['record'])) {
            return $this->_options['record'];
        }
        return false;
        
    }
    
    public function hasPublicPage()
    {
        return $this->_options['hasPublicPage'];
        
    }
    public function render($content)
    {
    
        $noAttribs = $this->getOption('noAttribs');
        $record = $this->getRecord();
        $content = $this->getOption('content');
        $this->removeOption('content');
        $this->removeOption('noAttribs');
        $this->removeOption('openOnly');
        $this->removeOption('closeOnly');
        $this->removeOption('record');
    
        $attribs = null;
        if (!$noAttribs) {
            $attribs = $this->getOptions();
        }
    
        $html = "<input id='save-changes' class='submit big green button' type='submit' value='Save Changes' name='submit' />";
        if($record) {
            if($this->hasPublicPage()) {
                set_theme_base_url('public');
                $publicPageUrl = record_url($record, 'show');
                revert_theme_base_url();
                $html .= "<a href='$publicPageUrl' class='big blue button' target='_blank'>View Public Page</a>";
            }
            $recordDeleteConfirm = record_url($record, 'delete-confirm');
            $html .= "<a href='$recordDeleteConfirm' class='big red button'>Delete</a>";

        }
        return $html;
    }    
    
    
}