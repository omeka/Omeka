<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Decorator for buttons (usually actions) for the save panel in Omeka_Form_Admin
 * 
 * @package Omeka\Form\Decorator
 */

class Omeka_Form_Decorator_SavePanelAction extends Zend_Form_Decorator_Abstract
{
    
    protected $content;    
    
    public function getContent()
    {
        return $this->content;    
    }
    
    /**
     * Checks if a record was passed to the Omeka_Form_Admin form. returns it if it has been
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
    
    /**
     * Checks if the Omeka_Form_Admin should display a link to a record's public page
     */
    
    public function hasPublicPage()
    {
        return $this->_options['hasPublicPage'];          
    }
    
    /**
     * Render html for the save panel buttons
     * 
     * @param string $content
     * @return string
     */
    
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
    
        $html = "<input id='save-changes' class='submit big green button' type='submit' value='" . __('Save Changes') . "' name='submit' />";
        if($record) {
            if($this->hasPublicPage() && $record->exists()) {
                set_theme_base_url('public');
                $publicPageUrl = record_url($record, 'show');
                revert_theme_base_url();
                $html .= "<a href='$publicPageUrl' class='big blue button' target='_blank'>" . __('View Public Page') . "</a>";
            }
            if (is_allowed($record, 'delete')) {
                $recordDeleteConfirm = record_url($record, 'delete-confirm');
                $html .= "<a href='$recordDeleteConfirm' class='big red button delete-confirm'>" . __('Delete') . "</a>";
            }

        }
        //used by SavePanelHook to locate where to insert hook content
        $html .= "<div id='button-field-line'></div>";
        return $html;
    }
}
