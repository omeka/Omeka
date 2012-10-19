<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * This ZF controller plugin allows the HtmlPurifier to filter the existing 
 * forms (items, collections, users, etc.) so that fields that are allowed to 
 * contain HTML are properly filtered.
 * 
 * Note that this will not operate on any of the plugins.
 * 
 * @package Omeka\Controller\Plugin
 */
class Omeka_Controller_Plugin_HtmlPurifier extends Zend_Controller_Plugin_Abstract
{        
    /**
     * Add the HtmlPurifier options if needed.
     * 
     * @param Zend_Controller_Request_Abstract $request
     * @return void
     **/
    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
        $this->_setupHtmlPurifierOptions();
    }
    
    /**
     * Determine whether or not to filter form submissions for various controllers.
     * 
     * @param Zend_Controller_Request_Abstract $request
     * @return void
     **/
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {        
        // Don't purify if the request is not a post
        if (!$request->isPost()) {
            return;
        }
        
        // Don't purify if the post is empty
        $post = $request->getPost();
        if (empty($post)) {
            return;
        }

        // Don't purify if the purifier is not enabled
        if (get_option('html_purifier_is_enabled') != '1') {
            return;
        }
        
        // Don't purify if there is no purifier        
        $htmlPurifierFilter = new Omeka_Filter_HtmlPurifier();
        $purifier = Omeka_Filter_HtmlPurifier::getHtmlPurifier();
        if (!$purifier) {
            return;
        }        
                
        // To process the items form, implement a 'filterItemsForm' method
        if ($this->isFormSubmission($request)) {
            $controllerName = $request->getControllerName();
            $filterMethodName = 'filter' . ucwords($controllerName) . 'Form';
            if (method_exists($this, $filterMethodName)) {                
                $this->$filterMethodName($request, $htmlPurifierFilter);
            }            
        }
        
        // Let plugins hook into this to process form submissions in their own way.
        fire_plugin_hook('html_purifier_form_submission', array('purifier' => $purifier));
        
        // No processing for users form, since it's already properly filtered by 
        // User::filterPostData(). No processing for tags form, none of the tags 
        // should be HTML. The only input on the tags form is the 'new_tag' 
        // field on the edit page. No processing on the item-types form since 
        // there are no HTML fields.
    }
    
    /**
     * Determine whether or not the request contains a form submission to either
     * the 'add', 'edit', or 'config' actions.
     * 
     * @param Zend_Controller_Request_Abstract $request
     * @return boolean
     **/
    public function isFormSubmission($request)
    {
        return in_array($request->getActionName(), array('add', 'edit', 'config')) and $request->isPost();
    }
            
    /**
     * Filter the Collections form post, including the 'Elements' array of the POST.
     *
     * @param Zend_Controller_Request_Abstract $request
     * @param Omeka_Filter_HtmlPurifier $htmlPurifierFilter
     * @return void
     **/
    public function filterCollectionsForm($request, $htmlPurifierFilter=null)
    {   
        if ($htmlPurifierFilter === null) {
            $htmlPurifierFilter = new Omeka_Filter_HtmlPurifier();
        }
        
        $post = $request->getPost();
        $post = $this->_filterElementsFromPost($post, $htmlPurifierFilter);
                
        $request->setPost($post);
    }
    
    /**
    * Purify all of the data in the theme settings
    *
    * @param Zend_Controller_Request_Abstract $request
    * @param Omeka_Filter_HtmlPurifier $htmlPurifierFilter
    * @return void    
    **/
    public function filterThemesForm($request, $htmlPurifierFilter=null)
    {
        if ($htmlPurifierFilter === null) {
            $htmlPurifierFilter = new Omeka_Filter_HtmlPurifier();
        }
        
        $post = $request->getPost();
        $post = $this->_purifyArray($post, $htmlPurifierFilter);
        $request->setPost($post);
    }
    
    /**
    * Recurisvely purify an array
    *
    * @param array An unpurified array of string or array values
    * @param Omeka_Filter_HtmlPurifier $htmlPurifierFilter
    * @return array A purified array of string or array values
    **/
    protected function _purifyArray($dataArray = array(), $htmlPurifierFilter=null)
    {
        if ($htmlPurifierFilter === null) {
            $htmlPurifierFilter = new Omeka_Filter_HtmlPurifier();
        }
        
        foreach($dataArray as $k => $v) {
            if (is_array($v)) {
                $dataArray[$k] = $this->_purifyArray($v, $htmlPurifierFilter);
            } else if (is_string($v)) {
                $dataArray[$k] = $htmlPurifierFilter->filter($v);
            }
        }
        return $dataArray;
    }
    
    /**
     * Filter the Items form post, including the 'Elements' array of the POST.
     * 
     * @param Zend_Controller_Request_Abstract $request
     * @param Omeka_Filter_HtmlPurifier $htmlPurifierFilter
     * @return void
     **/    
    public function filterItemsForm($request, $htmlPurifierFilter=null)
    {
        if ($htmlPurifierFilter === null) {
            $htmlPurifierFilter = new Omeka_Filter_HtmlPurifier();
        }
                
        $post = $request->getPost();
        $post = $this->_filterElementsFromPost($post, $htmlPurifierFilter);
        
        // Also strip HTML out of the tags field.
        $post['tags'] = strip_tags($post['tags']);        
        
        $request->setPost($post);
    }
    
    /**
     * Filter the 'Elements' array of the POST.
     * 
     * @param Zend_Controller_Request_Abstract $post
     * @param Omeka_Filter_HtmlPurifier $htmlPurifierFilter
     * @return void
     **/
    protected function _filterElementsFromPost($post, $htmlPurifierFilter=null)
    {
        if ($htmlPurifierFilter === null) {
            $htmlPurifierFilter = new Omeka_Filter_HtmlPurifier();
        }
        
        // Post looks like Elements[element_id][index] = array([text], [html])
        // 
        // In some cases it doesn't look like that, for example the date field 
        // has month, year, day.
        // 
        // What we do in this case is just not do anything if there is no text field
        // alongside the html field.
        foreach ($post['Elements'] as $elementId => $texts) {
            foreach ($texts as $index => $values) {
                if (array_key_exists('text', $values)) {
                    if (array_key_exists('html', $values) && (boolean)$values['html']) {
                        $post['Elements'][$elementId][$index]['text'] = $htmlPurifierFilter->filter($values['text']);
                    }
                }
            }
        }
        return $post;
    }
    
    protected function _setupHtmlPurifierOptions()
    {
        if (get_option('html_purifier_is_enabled') === null) {
            set_option('html_purifier_is_enabled', '1');
        }
        
        if (get_option('html_purifier_allowed_html_elements') === null) {
            set_option('html_purifier_allowed_html_elements', implode(',', Omeka_Filter_HtmlPurifier::getDefaultAllowedHtmlElements()));
        }
        
        if (get_option('html_purifier_allowed_html_attributes') === null) {
            set_option('html_purifier_allowed_html_attributes', implode(',', Omeka_Filter_HtmlPurifier::getDefaultAllowedHtmlAttributes()));
        }
    } 
}