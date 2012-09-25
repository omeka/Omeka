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

    const MAIN_NAV_CHECKBOX_DISPLAY_ELEMENT_ID = 'navigation_main_checkbox_display';
    
    const NEW_NAV_LINK_DISPLAY_ELEMENT_ID = 'new_nav_link_display';
    const NEW_NAV_LINK_LABEL_ELEMENT_ID = 'new_nav_link_label';
    const NEW_NAV_LINK_URI_ELEMENT_ID = 'new_nav_link_uri';
    const NEW_NAV_LINK_BUTTON_ELEMENT_ID = 'new_nav_link_button';
    
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
    
    protected function _initElements() 
    {
        $this->clearElements();
        $this->_addNavElements($this->_nav);
        $this->_addNewNavigationLink();
        $this->_addHomepageSelectElement($this->_nav);
    }
        
    protected function _addNavElements(Omeka_Navigation $nav) 
    {   
        $elementIds = array();
        $checkboxCount = 0;
        foreach($nav as $page) {            
            if (!$page->hasChildren()) {
                $checkboxCount++;
                $pageClasses = array();
                if ($page->can_delete) {
                    $pageClasses[] = 'can_delete_nav_link';
                }
                
                $checkboxId = 'navigation_main_nav_checkboxes_' . $checkboxCount;                
                $elementIds[] = $checkboxId;
                $checkboxDesc = '<a href="' . $page->getHref() . '">' . __($page->getLabel()) . '</a>';
                $this->addElement('checkbox', $checkboxId, array(
                    'checked' => $page->isVisible(),
                    'description' => $checkboxDesc,
                    'checkedValue' => $this->_getPageHiddenInfo($page),
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
        
        $this->addElement('hidden', self::HIDDEN_ELEMENT_ID, array('value' => ''));
        $elementIds[] = self::HIDDEN_ELEMENT_ID;
        
        $desc = '<p>Check the links you would like to display in the main navigation.<br/> You can click and drag the links into your preferred display order.</p>';
        $this->addDisplayGroup(
            $elementIds,
            self::MAIN_NAV_CHECKBOX_DISPLAY_ELEMENT_ID,
            array(
                'legend' => 'Main Navigation',
                'description' => $desc,
        ));
                
        $this->getDisplayGroup(self::MAIN_NAV_CHECKBOX_DISPLAY_ELEMENT_ID)->setDecorators(
            array(
                array('Description', array('escape' => false, 'tag' => false)),
                'FormElements',
                'Fieldset', 
            )
        );
    }
    
    
    protected function _addHomepageSelectElement(Zend_Navigation $nav)
    {
        $elementIds = array();
        
        $pageLinks = array();
        $pageLinks['/'] = '[Default]'; // Add the default homepage link option 
        foreach($nav as $page) {
            if (!$page->hasChildren()) {                
                $pageLinks[$page->getHref()] = $page->getLabel();
            }
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
            self::HOMEPAGE_SELECT_DISPLAY_ELEMENT_ID);
    }
    
    protected function _addNewNavigationLink()
    {   
        $elementIds = array();
        
        $this->addElement('text', self::NEW_NAV_LINK_LABEL_ELEMENT_ID, array(
            'value' => '',
            'label' => __('New Link Label'),
        ));
        $elementIds[] = self::NEW_NAV_LINK_LABEL_ELEMENT_ID;
        
        $this->addElement('text', self::NEW_NAV_LINK_URI_ELEMENT_ID, array(
            'value' => '',
            'label' => __('New Link URI'),
        ));
        $elementIds[] = self::NEW_NAV_LINK_URI_ELEMENT_ID;
        
        $this->addElement('html', self::NEW_NAV_LINK_BUTTON_ELEMENT_ID, array(
            'value' => '<a href="" id="new_nav_link_button_link" class="blue button">' . __('Add Link') . '</a>',
        ));
        $elementIds[] = self::NEW_NAV_LINK_BUTTON_ELEMENT_ID;
        
        $this->addDisplayGroup(
            $elementIds,
            self::NEW_NAV_LINK_DISPLAY_ELEMENT_ID,
            array(
                'legend' => __('Add Link'),
        ));
        
        $this->getDisplayGroup(self::NEW_NAV_LINK_DISPLAY_ELEMENT_ID)->setDecorators(
            array(
                array('Description', array('escape' => false, 'tag' => false)),
                'FormElements',
                'Fieldset', 
            )
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
                foreach($pageLinks as $pageLink) {
                    $pageOrder++;

                    // add or update the page in the navigation
                    $pageUid = $nav->createPageUid($pageLink['uri']);                    
                    if (!($page = $nav->getPageByUid($pageUid))) {
                        $page = new Omeka_Navigation_Page_Uri();
                        $page->setHref($pageLink['uri']); // this sets both the uri and the fragment
                        $nav->addPage($page);
                    }
                    $page->setLabel($pageLink['label']);
                    $page->set('can_delete', (bool)$pageLink['can_delete']);
                    $page->setVisible($pageLink['visible']);
                    $page->setOrder($pageOrder);
                    $pageUids[] = $page->uid;
                }
                
                // remove expired pages from navigation
                $expiredPages = array();
                foreach($nav as $page) {
                    if (!in_array($page->uid, $pageUids)) {
                        $expiredPages[] = $page;
                    }
                }
                foreach($expiredPages as $expiredPage) {
                    $nav->removePage($expiredPage);
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
