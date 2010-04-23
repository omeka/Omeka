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

    protected static $_purifier = null;
         
    public static function getHtmlPurifier()
    {
        if (!self::$_purifier) {
            self::$_purifier = self::createHtmlPurifier();
        }
        return self::$_purifier;
    }
    
    /**
     * @param HTMLPurifier $purifier
     **/
    public static function setHtmlPurifier($purifier)
    {
        self::$_purifier = $purifier;
    }
    
    public static function createHtmlPurifier($allowedHtmlElements=null, $allowedHtmlAttributes=null)
    {
        if ($allowedHtmlElements === null || $allowedHtmlAttributes === null) {
            $config = Omeka_Context::getInstance()->getConfig();

            // Get the allowed HTML elements from the configuration file
            if ($allowedHtmlElements === null) {
                $allowedHtmlElements = $config->htmlpurifier->allowedhtmlelements;
            }

            // Get the allowed HTML attributes from the configuration file
            if ($allowedHtmlAttributes === null) {
                $allowedHtmlAttributes = $config->htmlpurifier->allowedhtmlattributes;
            }
        }

        // Require the HTML Purfier autoloader.
        require_once 'htmlpurifier-3.1.1-lite/library/HTMLPurifier.auto.php';        
        $htmlPurifierConfig = HTMLPurifier_Config::createDefault();
        
        // Set the encoding to UTF-8
        $htmlPurifierConfig->set('Core', 'Encoding', 'UTF-8');

        // Allow HTML tags. Setting this as NULL allows a subest of TinyMCE's 
        // valid_elements whitelist. Setting this as an empty string disallows 
        // all HTML elements.
        $htmlPurifierConfig->set('HTML', 'AllowedElements', $allowedHtmlElements);
        $htmlPurifierConfig->set('HTML', 'AllowedAttributes', $allowedHtmlAttributes);

        // Disable caching.
        $htmlPurifierConfig->set('Cache', 'DefinitionImpl', null);

        // Get the purifier as a singleton.
        $htmlPurifier = HTMLPurifier::instance($htmlPurifierConfig);

        // Set this in the registry so that other plugins can get to it.
        Zend_Registry::set('html_purifier', $htmlPurifier);
                
        return $htmlPurifier;
    }
        
    /**
     * Determine whether or not to filter form submissions for various controllers.
     * 
     * @param Zend_Controller_Request_Abstract $request
     * @return void
     **/
    public function routeStartup(Zend_Controller_Request_Abstract $request)
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
        $config = Omeka_Context::getInstance()->getConfig();
        if (!$config->htmlpurifier->enabled) {
            return;
        }
        
        // Don't purify if there is no purifier
        $purifier = self::getHtmlPurifier();
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
     * the 'add' or 'edit' actions.
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
            $purifier = Omeka_Controller_Plugin_HtmlPurifier::getHtmlPurifier();
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
            $purifier = Omeka_Controller_Plugin_HtmlPurifier::getHtmlPurifier();
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
            $purifier = Omeka_Controller_Plugin_HtmlPurifier::getHtmlPurifier();
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
            $purifier = Omeka_Controller_Plugin_HtmlPurifier::getHtmlPurifier();
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
}