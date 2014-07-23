<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Navigation form.
 * 
 * @package Omeka\Form
 */
class Omeka_Form_Navigation extends Omeka_Form
{
    const FORM_ELEMENT_ID = 'navigation_form';
    const HIDDEN_ELEMENT_ID = 'navigation_hidden';
    const SELECT_HOMEPAGE_ELEMENT_ID = 'navigation_homepage_select';
    const HOMEPAGE_URI_OPTION_NAME = 'homepage_uri';
    const HOMEPAGE_SELECT_DISPLAY_ELEMENT_ID = 'homepage_select_display';
        
    private $_nav;  // The Omeka_Navigaton object
    
    /**
     * Initializes the form.  Loads the navigation object from the saved main navigation object.  
     *
     */
    public function init()
    {
        parent::init();
        $this->setAttrib('id', self::FORM_ELEMENT_ID);
        $this->_nav = new Omeka_Navigation();
        $this->_nav->loadAsOption(Omeka_Navigation::PUBLIC_NAVIGATION_MAIN_OPTION_NAME);        
        $this->_nav->addPagesFromFilter(Omeka_Navigation::PUBLIC_NAVIGATION_MAIN_FILTER_NAME);
        $this->_initElements();
    }
    
    /**
     * Returns the html for the wrapper containing the navigation link checkboxes 
     *
     * @return String The html for the wrapper containing the navigation link checkboxes
     */
    public function displayNavigationLinks()
    {        
        $html = '<ul id="navigation_main_list">';
        $pageCount = 0;
        foreach($this->_nav as $page) {
            $html .= $this->_displayNavigationPageLink($page, $pageCount);
        }
        $html .= '</ul>';
        return $html;
    }
    
    /**
     * Saves the navigation and homepage from the form post data 
     *
     */
    public function saveFromPost() 
    {   
        // Save the navigation from post                 
        $this->_saveNavigationFromPost();
        // Save the homepage uri
        $this->_saveHomepageFromPost();
        // Reset the form elements to display the updated navigation
        $this->_initElements();
    }
    
    /**
     * Initializes the form elements. 
     *
     */
    protected function _initElements() 
    {
        $this->clearElements();
        $this->_addHiddenElement();
        $this->_addHomepageSelectElement();
        $this->addElement('hash', 'navigation_csrf',
            array(
                'decorators' => array('ViewHelper'),
                'timeout' => 3600
            )
        );
    }
    
    /**
     * Adds the hidden element to the form. 
     *
     */    
    protected function _addHiddenElement() 
    {
        $this->addElement('hidden', 
            self::HIDDEN_ELEMENT_ID, 
            array(
                'value' => '',
                'decorators' =>  array(
                    'ViewHelper',
                    array('Description', array('escape' => false, 'tag' => false)),
                    array('HtmlTag', array('tag' => 'div')),
                    array('Label'),
                    'Errors',)
        ));
    }
    
    /**
     * Returns the html for a navigation page link and its sublinks. 
     *
     * @param Zend_Navigation_Page $page The navigation page
     * @param int $pageCount The number of pages added so far to the form
     * @return String The html for a navigation page link and its sublinks
     */
    protected function _displayNavigationPageLink(Zend_Navigation_Page $page, &$pageCount)
    {        
        $pageCount++;
        $checkboxId = 'main_nav_checkboxes_' . $pageCount;                
        $checkboxValue = $this->_getPageHiddenInfo($page);
        $checkboxChecked = $page->isVisible() ? 'checked="checked"' : '';
        $checkboxClasses = array();
        if ($page->can_delete) {
            $checkboxClasses[] = 'can_delete_nav_link';
        }
        $checkboxClass = implode(' ', $checkboxClasses);
        $html = '<li>';
        $html .= '<div class="main_link">';
        $html .= '<div class="sortable-item">';
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
        $html .= html_escape($page->getLabel());
        $html .= '</div>';
        $html .= '<div class="drawer-contents">';
        $html .= '<label>' . __('Label') . '</label><input type="text" class="navigation-label" />';
        $html .= '<label>' . __('URL') . '</label><input type="text" class="navigation-uri" />';
        $html .= '<div class="main_link_buttons"></div>';
        $html .= '</div>';
        $html .= '</div>';
        if ($page->hasChildren()) {
            $html .= '<ul>';
            foreach($page as $childPage) {
                $html .= $this->_displayNavigationPageLink($childPage, $pageCount);
            }
            $html .= '</ul>';
        }
        $html .= '</li>';
        return $html;
    }
    
    /**
     * Adds the homepage select element to the form 
     *
     */
    protected function _addHomepageSelectElement()
    {
        $elementIds = array();
        $pageLinks = array();
        $pageLinks['/'] = __('[Default]'); // Add the default homepage link option
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
        
    /**
     * Saves the main navigation object from the form post data 
     *
     */
    protected function _saveNavigationFromPost() 
    {           
        $nav = $this->_getNavigationFromPost();
        $nav->saveAsOption(Omeka_Navigation::PUBLIC_NAVIGATION_MAIN_OPTION_NAME);        
        $this->_nav = $nav;
    }
    
    /**
     * Returns the navigation object from the post data
     *
     * @return Omeka_Navigation The navigation object from the post data
     */
    protected function _getNavigationFromPost()
    {
        $nav = new Omeka_Navigation();
        $nav->loadAsOption(Omeka_Navigation::PUBLIC_NAVIGATION_MAIN_OPTION_NAME);
        $pageUids = array();
        if ($pageLinks = $this->getValue(self::HIDDEN_ELEMENT_ID)) {            
            if ($pageLinks = json_decode($pageLinks, true)) {
                 // add and update the pages in the navigation
                $pageOrder = 0;
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
                    if ($parentPageId === null 
                        || !array_key_exists($parentPageId, $pageIdsToPageUids)
                    ) {
                        // add a page that lacks a parent
                        $nav->addPage($page);
                    } else {    
                        // add a child page to its parent page
                        // we assume that all parents already exist in the navigation
                        $parentPageUid = $pageIdsToPageUids[$parentPageId];
                        if (!($parentPage = $nav->getPageByUid($parentPageUid))) {
                            throw new RuntimeException(__("Cannot find parent navigation page."));
                        } else {
                            $parentPage->addPage($page);
                        }
                    }
                }
            }
        }
        // prune the remaining expired pages from navigation
        $otherPages = $nav->getOtherPages($pageUids);                
        $expiredPages = array();
        foreach($otherPages as $otherPage) {
            $nav->prunePage($otherPage);
        }
        return $nav;
    }
    
    /**
     * Saves the homepage from the form post data 
     *
     */
    protected function _saveHomepageFromPost()
    {
        $homepageUri = $this->getValue(self::SELECT_HOMEPAGE_ELEMENT_ID);
        // make sure the homepageUri still exists in the navigation
        $pageExists = false;
        $iterator = new RecursiveIteratorIterator($this->_nav, RecursiveIteratorIterator::SELF_FIRST);
        foreach($iterator as $page) {
            if ($page->getHref() == $homepageUri) {
                $pageExists = true;
                break;
            }
        }
        if (!$pageExists) {
            $homepageUri = '/';
        }
        set_option(self::HOMEPAGE_URI_OPTION_NAME, $homepageUri);
    }
    
    /**
     * Returns JSON with the hidden info for a navigation page link. 
     *
     * @param Zend_Navigation_Page $page The navigation page
     * @return String JSON with the hidden info for a navigation page link. 
     */
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
    
    /**
     * Validate the form
     *
     * @param  array $data
     * @return boolean
     */
    public function isValid($data)
    {   
        if (!parent::isValid($data)) {
            return false;
        }
        $hasErrors = false;
        $missingLabel = false;
        $missingURI = false;
        $duplicateURI = false;
        $uids = array();
        if ($pageLinks = $this->getValue(self::HIDDEN_ELEMENT_ID) ) {
            if ($pageLinks = json_decode($pageLinks, true)) {                                                                 
                foreach($pageLinks as $pageLink) {
                    if (!$missingLabel && trim($pageLink['label']) == '') {
                        $this->addError('All navigation links must have both labels.');
                        $hasErrors = true;
                        $missingLabel = true;
                    }
                    if (!$missingURI && trim($pageLink['uri']) == '') {
                        $this->addError(__('All navigation links must have URIs.'));
                        $hasErrors = true;
                        $missingURI = true;
                    }
                    if (trim($pageLink['uri']) != '') {
                        try {
                            $page = new Omeka_Navigation_Page_Uri();                    
                            $page->setHref($pageLink['uri']);
                            $uid = $this->_nav->createPageUid($page->getHref());
                            if (!in_array($uid, $uids)) {
                                $uids[] = $uid;
                            } else {
                                if (!$duplicateURI) {
                                    $this->addError(__('All navigation links must have different URIs.'));
                                    $duplicateURI = true;
                                    $hasErrors = true;
                                }
                            }
                        } catch (Omeka_Navigation_Page_Uri_Exception $e) {
                            $this->addError(__('Invalid URI for "%s" navigation link:  "%s"', $pageLink['label'],  $pageLink['uri']));
                            $hasErrors = true;
                        }
                    }
                }
            }
        }
        $hasErrors = $hasErrors || $this->_postHasDeletedUndeletablePage();
        return !$hasErrors;
    }
    
    /**
     * Returns whether the post is attempting to delete an undeletable page
     *
     * @return boolean
     */
    protected function _postHasDeletedUndeletablePage()
    {        
        // get undeleteable page uids from new navigation
        $nav = $this->_getNavigationFromPost();
        $iterator = new RecursiveIteratorIterator($nav, RecursiveIteratorIterator::SELF_FIRST);
        $newUndeleteableUids = array();
        foreach($iterator as $page) {
            if ($page->can_delete == false) {
                $newUndeleteableUids[] = $page->uid;
            }
        }
        // make sure every undeleteable page uid from old navigation is in the list of new undeleteable page uids
        $nav = Omeka_Navigation::createNavigationFromFilter('public_navigation_main');
        $iterator = new RecursiveIteratorIterator($nav, RecursiveIteratorIterator::SELF_FIRST);
        foreach($iterator as $page) {
            if ($page->can_delete == false) {
                if (!in_array($page->uid, $newUndeleteableUids)) {                   
                    $this->addError(__('Navigation links that have undeleteable sublinks cannot be deleted.'));
                    return true;
                }
            }
        }
        return false;
    }
}
