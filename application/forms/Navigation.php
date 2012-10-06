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
    const FORM_ELEMENT_ID = 'navigation_form';
    const HIDDEN_ELEMENT_ID = 'navigation_hidden';
    const SELECT_HOMEPAGE_ELEMENT_ID = 'navigation_homepage_select';
    const HOMEPAGE_URI_OPTION_NAME = 'homepage_uri';
    
    const HOMEPAGE_SELECT_DISPLAY_ELEMENT_ID = 'homepage_select_display';
        
    private $_nav;
    
    public function init()
    {
        parent::init();
        $this->setAttrib('id', self::FORM_ELEMENT_ID);
        
        $this->_nav = new Omeka_Navigation();
        $this->_nav->loadAsOption(Omeka_Navigation::PUBLIC_NAVIGATION_MAIN_OPTION_NAME);        
        $this->_nav->addPagesFromFilter('public_navigation_main');
        $this->_initElements();
    }
    
    public function displayCheckboxesFieldset()
    {        
        $html = '<fieldset id="fieldset-navigation_main_checkbox_display">';
        $html .= '<ul id="navigation_main_list">';
        
        $checkboxCount = 0;
        foreach($this->_nav as $page) {
            $html .= $this->_displayCheckboxesForNavPage($page, $checkboxCount);
        }
        
        $html .= '</ul>';
        $html .= '</fieldset>';
        return $html;
    }
    
    protected function _initElements() 
    {
        $this->clearElements();
        $this->_addHiddenElement();
        $this->_addHomepageSelectElement();
    }
        
    protected function _addHiddenElement() 
    {
        $this->addElement('hidden', self::HIDDEN_ELEMENT_ID, array('value' => ''));
    }
    
    
    protected function _displayCheckboxesForNavPage(Zend_Navigation_Page $page, &$checkboxCount)
    {        
        $checkboxCount++;
         
        $checkboxId = 'navigation_main_nav_checkboxes_' . $checkboxCount;                
        $checkboxValue = $this->_getPageHiddenInfo($page);
        $checkboxChecked = $page->isVisible() ? 'checked="checked"' : '';
        $checkboxClasses = array();
        if ($page->can_delete) {
            $checkboxClasses[] = 'can_delete_nav_link';
        }
        $checkboxClass = implode(' ', $checkboxClasses);        
        
        $html = '<li>';
        
        $html .= '<div class="navigation_main_link">';
        $html .= '<div class="navigation_main_link_header">';
        $html .= '<input type="checkbox" name="' 
                 . $checkboxId 
                 . '" id="' 
                 . $checkboxId 
                 . '" value="' . html_escape($checkboxValue) 
                 . '" ' 
                 . $checkboxChecked 
                 . ' class="' 
                 . $checkboxClass 
                 . '">';
        $html .= '<a href="' . $page->getHref() . '">' . __($page->getLabel()) . '</a>';
        $html .= '</div>';
        $html .= '<div class="navigation_main_link_body">';
        $html .= '<div><label class="navigation_main_link_label_label">Label</label><input type="text" class="navigation_main_link_label" /></div>';
        $html .= '<div><label class="navigation_main_link_uri_label">URI</label><input type="text" class="navigation_main_link_uri" /></div>';
        $html .= '<div class="navigation_main_link_buttons"></div>';
        $html .= '</div>';
        $html .= '</div>';
        if ($page->hasChildren()) {
            $html .= '<ul>';
            foreach($page as $childPage) {
                $html .= $this->_displayCheckboxesForNavPage($childPage, $checkboxCount);
            }
            $html .= '</ul>';
        }
        $html .= '</li>';
        
        return $html;
    }
    
    protected function _addHomepageSelectElement()
    {
        $elementIds = array();

        $pageLinks = array();
        $pageLinks['/'] = '[Default]'; // Add the default homepage link option 
        
        $iterator = new RecursiveIteratorIterator($this->_nav, RecursiveIteratorIterator::SELF_FIRST);
        foreach($iterator as $page) {
            $pageLinks[$page->getHref()] = $page->getLabel();
        }
        
        $this->addElement('select', self::SELECT_HOMEPAGE_ELEMENT_ID, array(
            'label' => __('Select a Homepage'),
            'multiOptions' => $pageLinks,
            'value' => get_option(self::HOMEPAGE_URI_OPTION_NAME),
            'registerInArrayValidator' => false,
            'decorators' =>  array(
                    'ViewHelper',
                    array('Description', array('escape' => false, 'tag' => false)),
                    array('HtmlTag', array('tag' => 'div')),
                    array('Label'),
                    'Errors',)
        ));
        $elementIds[] = self::SELECT_HOMEPAGE_ELEMENT_ID;
        
        $this->addDisplayGroup(
            $elementIds,
            self::HOMEPAGE_SELECT_DISPLAY_ELEMENT_ID, 
            array('class' => 'field')
        );
    }
    
    public function saveFromPost() 
    {
        // Save the homepage uri
        $this->_saveHomepageFromPost();
        
        // Save the navigation from post                 
        $this->_saveNavigationFromPost();
        
        // Reset the form elements to display the updated navigation
        $this->_initElements();
    }
    
    public function _saveNavigationFromPost() 
    {           
        // update the navigation from the hidden element value in the post data
        $nav = new Omeka_Navigation();
        $nav->loadAsOption(Omeka_Navigation::PUBLIC_NAVIGATION_MAIN_OPTION_NAME);
                            
        if ($pageLinks = $this->getValue(self::HIDDEN_ELEMENT_ID) ) {            
                
            if ($pageLinks = json_decode($pageLinks, true)) {
                                                                
                // add and update the pages in the navigation
                $pageOrder = 0;
                $pageUids = array();
                $pages = array();
                $parentPageIds = array();
                $pageIdsToPageUids = array();
                foreach($pageLinks as $pageLink) {                    
                    $pageOrder++;
                    // add or update the page in the navigation
                    $pageUid = $nav->createPageUid($pageLink['uri']);                    
                    if (!($page = $nav->getPageByUid($pageUid))) {
                        $page = new Omeka_Navigation_Page_Uri();
                        $page->setHref($pageLink['uri']); // this sets both the uri and the fragment
                        $page->set('uid', $pageUid);
                    }
                    $page->setLabel($pageLink['label']);
                    $page->set('can_delete', (bool)$pageLink['can_delete']);
                    $page->setVisible($pageLink['visible']);
                    $page->setOrder($pageOrder);
                    $parentPageIds[] = $pageLink['parent_id'];                                        
                    $pageUids[] = $page->uid;
                    $pages[] = $page;
                    
                    $pageIdsToPageUids[strval($pageLink['id'])] = $page->uid;
                }
                                
                // structure the parent/child relationships
                // this assumes that the $pages are in a flattened hierarchical order           
                for($i = 0; $i < $pageOrder; $i++) {
                    $page = $pages[$i];
                    $page->removePages(); // remove old children pages
                    $parentPageId = $parentPageIds[$i];
                                        
                    if ($parentPageId === null) {
                         // add a page that lacks a parent
                        echo 'a' . '<br/>';
                        $nav->addPage($page);
                    } else {    
                        // add a child page to its parent page
                        // we assume that all parents already exist in the navigation
                        $parentPageUid = $pageIdsToPageUids[strval($parentPageId)];
                        if (!($parentPage = $nav->getPageByUid($parentPageUid))) {
                            throw RuntimeException(__("Cannot find parent navigation page."));
                        } else {
                            echo 'b' . '<br/>';
                            $parentPage->addPage($page);
                        }
                    }
                }
                                
                // remove expired pages from navigation
                $expiredPages = array();
                $iterator = new RecursiveIteratorIterator($nav,
                                    RecursiveIteratorIterator::SELF_FIRST);
                foreach($iterator as $page) {
                    if (!in_array($page->uid, $pageUids)) {
                        $expiredPages[] = $page;
                    }
                }
                foreach($expiredPages as $expiredPage) {
                    $nav->removePageRecursive($expiredPage);
                }
            }
        }

        $nav->saveAsOption(Omeka_Navigation::PUBLIC_NAVIGATION_MAIN_OPTION_NAME);        
        $this->_nav = $nav;
    }
    
    protected function _saveHomepageFromPost()
    {
        $homepageUri = $this->getValue(self::SELECT_HOMEPAGE_ELEMENT_ID);
        set_option(self::HOMEPAGE_URI_OPTION_NAME, $homepageUri); 
    }
    
    protected function _getPageHiddenInfo(Zend_Navigation_Page $page) 
    {
        $hiddenInfo = array(
          'can_delete' =>  (bool)$page->can_delete,
          'uri' => $page->getHref(),
          'label' => $page->getLabel(),
          'visible' => $page->isVisible(),
        );
        return json_encode($hiddenInfo);
    }
}
