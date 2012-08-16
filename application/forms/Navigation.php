<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * Navigation form.
 *
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2012
 */
class Omeka_Form_Navigation extends Omeka_Form
{
    const HIDDEN_ELEMENT_ID = 'navigation_hidden';
    
    private $_nav;
    
    public function init()
    {
        parent::init();
        $this->setAttrib('id', 'navigation_form');
    }
    
    public function setNavigation(Omeka_Navigation $nav) 
    {
        $this->_nav = $nav;
        $this->_initElements();
    }
    
    private function _initElements() 
    {
        $this->clearElements();
        
        $this->addCheckboxElementsFromNav($this->_nav);
        $this->addHiddenElementFromNav($this->_nav);
        
        $this->addElement('submit', 'navigation_submit', array(
            'label' => __('Save Changes'),
            'class' => 'big green button'
        ));
    }
    
    public function addCheckboxElementsFromNav(Omeka_Navigation $nav) 
    {   
        $checkboxCount = 0;
        foreach($nav as $page) {
            if (!$page->hasChildren()) {
                $checkboxCount++;
                $pageClasses = array();
                if ($page->can_delete) {
                    $pageClasses[] = 'can_delete_nav_link';
                }
                
                $checkboxId = 'navigation_main_nav_checkboxes_' . $checkboxCount;                
                $checkboxDesc = '<a href="' . $page->getHref() . '">' . __($page->getLabel()) . '</a>';
                $this->addElement('checkbox', $checkboxId, array(
                    'checked' => $page->isActive(),
                    'description' => $checkboxDesc,
                    'checkedValue' => $this->_getPageId($page),
                    'class' => $pageClasses,
                    'decorators' =>  array(
                            'ViewHelper',
                            array('Description', array('escape' => false, 'tag' => false)),
                            array('HtmlTag', array('tag' => 'dd')),
                            array('Label', array('tag' => '')),
                            'Errors',)
                ));
            }
        }
    }
    
    public function addHiddenElementFromNav(Omeka_Navigation $nav) 
    {
        $this->addElement('hidden', self::HIDDEN_ELEMENT_ID, array('value' => ''));
    }
    
    public function addPagesToNavFromHiddenElementValue(Omeka_Navigation $nav) 
    {
        if ($pageLinks = $this->getValue(self::HIDDEN_ELEMENT_ID) ) {
            if ($pageLinks = json_decode($pageLinks, true)) {
                foreach($pageLinks as $pageLink) {

                    // parse the linkdata for the page from the hidden element text
                    $linkIdParts = explode('|', $pageLink['id'], 3);
                    $linkData = array();
                    $linkData['can_delete'] = (bool)$linkIdParts[0];
                    $linkData['uri'] = $linkIdParts[1];
                    $linkData['label'] = $linkIdParts[2];
                    $linkData['active'] = $pageLink['active'];

                    // add the page to the navigation
                    $nav->addPageFromLinkData($linkData);
                }
            }
        }
    }
    
    private function _getPageId(Zend_Navigation_Page $page) 
    {
        return (int)$page->can_delete . '|' . $page->getHref() . '|' . $page->getLabel();
    }
}
