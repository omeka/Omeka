<?php
/**
 * This ZF controller plugin allows the HtmlPurifier to filter the existing 
 * forms (items, collections, users, etc.) so that fields that are allowed to 
 * contain HTML are properly filtered.
 * 
 * Note that this will not operate on any of the plugins.
 *
 * @package HtmlPurifier
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2010
 **/
class Omeka_Controller_Plugin_HtmlPurifier extends Zend_Controller_Plugin_Abstract
{        
    /**
     * Determine whether or not to filter form submissions for various controllers.
     * 
     * @param Zend_Controller_Request_Abstract $request
     * @return void
     **/
    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
        $this->_setupHtmlPurifierOptions();
           
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
        $purifier = Omeka_Filter_HtmlPurifier::getHtmlPurifier();
        if (!$purifier) {
            return;
        }
        
        // To process the items form, implement a 'filterItemsForm' method
        if ($this->isFormSubmission($request)) {
            $controllerName = $request->getControllerName();
            $filterMethodName = 'filter' . ucwords($controllerName) . 'Form';
            if (method_exists($this, $filterMethodName)) {                
                $this->$filterMethodName($request, $purifier);
            }            
        }
        
        // Let plugins hook into this to process form submissions in their own way.
        fire_plugin_hook('html_purifier_form_submission', $request, $purifier);
        
        // No processing for users form, since it's already properly filtered by User::filterInput()
        // No processing for tags form, none of the tags should be HTML.
        // The only input on the tags form is the 'new_tag' field on the edit page.
        // No processing on the item-types form since there are no HTML fields.
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
     * Title = Plain text.
     * Description = HTML.
     * 
     **/
    public function filterCollectionsForm($request, $purifier=null)
    {
        if ($purifier === null) {
            $purifier = Omeka_Filter_HtmlPurifier::getHtmlPurifier();
        }        
        $post = $request->getPost();
        $post['description'] = $purifier->purify($post['description']);
        $request->setPost($post);
    }
    
    /**
    * Purify all of the data in the theme settings
    **/
    public function filterThemesForm($request, $purifier=null)
    {
        if ($purifier === null) {
            $purifier = Omeka_Filter_HtmlPurifier::getHtmlPurifier();
        }
        
        $post = $request->getPost();
        $post = $this->_purifyArray($post, $purifier);
        $request->setPost($post);
    }
    
    /**
    * Recurisvely purify an array
    **/
    protected function _purifyArray($dataArray, $purifier=null)
    {
        if ($purifier === null) {
            $purifier = Omeka_Filter_HtmlPurifier::getHtmlPurifier();
        }
        
        foreach($dataArray as $k => $v) {
            if (is_array($v)) {
                $dataArray[$k] = $this->_purifyArray($v, $purifier);
            } else if (is_string($v)) {
                $dataArray[$k] = $purifier->purify($v);
            }
        }
        return $dataArray;
    }
    
    /**
     * Filter the 'Elements' array of the POST.
     * 
     * @param Zend_Controller_Request_Abstract $request
     * @return void
     **/    
    public function filterItemsForm($request, $purifier=null)
    {
        if ($purifier === null) {
            $purifier = Omeka_Filter_HtmlPurifier::getHtmlPurifier();
        }
        
        // Post looks like Elements[element_id][index] = array([text], [html])
        // 
        // In some cases it doesn't look like that, for example the date field 
        // has month, year, day.
        // 
        // What we do in this case is just not do anything if there is no text field
        // alongside the html field.
        
        $post = $request->getPost();
                
        foreach ($post['Elements'] as $elementId => $texts) {
            
            foreach ($texts as $index => $values) {
                if (!array_key_exists('text', $values)) {
                    break;
                }

                if (!array_key_exists('html', $values)) {
                    throw new Exception('What are you talking about?  You need the "html" field if you want HtmlPurifier to work correctly.');
                }
                
                if ((boolean)$values['html']) {
                    $post['Elements'][$elementId][$index]['text'] = $purifier->purify($values['text']);
                }
            }
        }
        
        // Also strip HTML out of the tags field.
        $post['tags'] = strip_tags($post['tags']);        
        
        $request->setPost($post);
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