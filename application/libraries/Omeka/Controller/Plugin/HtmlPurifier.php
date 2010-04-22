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

    protected $_purifier = null;
    
    /**
     * @param HTMLPurifier $purifier
     **/
    public function __construct($purifier)
    {
        $this->_purifier = $purifier;
    }
        
    /**
     * Determine whether or not to filter form submissions for various controllers.
     * 
     * @param Zend_Controller_Request_Abstract $request
     * @return void
     **/
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $controllerName = $request->getControllerName();
        
        // To process the items form, implement a 'filterItemsForm' method
        if ($this->isFormSubmission($request)) {
            $filterMethodName = 'filter' . ucwords($controllerName) . 'Form';
            if (method_exists($this, $filterMethodName)) {                
                $this->$filterMethodName($request);
            }            
        }
        
        // Let plugins hook into this to process form submissions in their own way.
        if ($request->isPost()) {
            fire_plugin_hook('html_purifier_form_submission', $request, $this->_purifier);
        }
        
        // No processing for users form, since it's already properly filtered by User::filterInput()
        // No processing for tags form, none of the tags should be HTML.
        // The only input on the tags form is the 'new_tag' field on the edit page.
        // No processing on the item-types form since there are no HTML fields.
    }
    
    /**
     * Determine whether or not the request contains a form submission to either
     * the 'add' or 'edit' actions.
     * 
     * @param Zend_Controller_Request_Abstract $request
     * @return boolean
     **/
    public function isFormSubmission($request)
    {
        return in_array($request->getActionName(), array('add', 'edit')) and $request->isPost();
    }
            
    /**
     * Title = Plain text.
     * Description = HTML.
     * 
     **/
    public function filterCollectionsForm($request)
    {
        $post = $request->getPost();
        $post['description'] = $this->_purifier->purify($post['description']);
        $request->setPost($post);
    }
    
    
    /**
     * Filter the 'Elements' array of the POST.
     * 
     * @param Zend_Controller_Request_Abstract $request
     * @return void
     **/    
    public function filterItemsForm($request)
    {
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
                    $post['Elements'][$elementId][$index]['text'] = $this->_purifier->purify($values['text']);
                }
            }
        }
        
        // Also strip HTML out of the tags field.
        // $post['tags'] = $this->_purifier->purify($post['tags']);
        $post['tags'] = strip_tags($post['tags']);        
        
        $request->setPost($post);
    }   
}