<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * @package Omeka
 * @copyright Center for History and New Media, 2009
 **/
class Omeka_Controller_Plugin_HtmlPurifierTest extends PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {        
        $htmlPurifier = $this->_getHtmlPurifier();
        $htmlPurifierPlugin = new Omeka_Controller_Plugin_HtmlPurifier($htmlPurifier);
        $this->assertEquals($htmlPurifier, $htmlPurifierPlugin->getHtmlPurifier());
    }
    
    public function testGetHtmlPurifier()
    {
        $htmlPurifier = null;
        $htmlPurifierPlugin = new Omeka_Controller_Plugin_HtmlPurifier($htmlPurifier);
        $this->assertEquals($htmlPurifier, $htmlPurifierPlugin->getHtmlPurifier());
        
        $htmlPurifier = $this->_getHtmlPurifier();
        $htmlPurifierPlugin = new Omeka_Controller_Plugin_HtmlPurifier($htmlPurifier);
        $this->assertEquals($htmlPurifier, $htmlPurifierPlugin->getHtmlPurifier());
    }
    
    public function testSetHtmlPurifier()
    {
        $htmlPurifier = null;
        $htmlPurifierPlugin = new Omeka_Controller_Plugin_HtmlPurifier($htmlPurifier);
        $this->assertEquals($htmlPurifier, $htmlPurifierPlugin->getHtmlPurifier());
        
        $htmlPurifier = $this->_getHtmlPurifier();
        $htmlPurifierPlugin->setHtmlPurifier($htmlPurifier);
        $this->assertEquals($htmlPurifier, $htmlPurifierPlugin->getHtmlPurifier());
    }
    
    public function testIsFormSubmission()
    {
        $formActions = array('add', 'edit');
        foreach($formActions as $formAction) {
            $request = new Zend_Controller_Request_HttpTestCase();
            $request->setActionName($formAction);
            $request->setMethod('POST');

            $htmlPurifierPlugin = $this->_getHtmlPurifierPlugin();
            $this->assertTrue($htmlPurifierPlugin->isFormSubmission($request));
        }
    }
    
    public function testFilterCollectionsFormForUnallowedTagInDescription()
    {
        $dirtyHtml = '<p>Bob</p>';
        $cleanHtml = 'Bob';
        
        // Create a request with dirty html for the collection description post variable
        $request = new Zend_Controller_Request_HttpTestCase();
        $post = array('description'=>$dirtyHtml);
        $request->setPost($post);
        
        // Html purify the request
        $htmlPurifierPlugin = $this->_getHtmlPurifierPlugin();
        $htmlPurifierPlugin->filterCollectionsForm($request);
        
        // Make sure the description post variable is clean
        $post = $request->getPost();
        $this->assertEquals($cleanHtml, $post['description']);
    }
    
    public function testFilterCollectionsFormForAllowedTagInDescription()
    {
        $dirtyHtml = '<p>Bob</p>';
        $cleanHtml = '<p>Bob</p>';
        
        // Create a request with dirty html for the collection description post variable
        $request = new Zend_Controller_Request_HttpTestCase();
        $post = array('description'=>$dirtyHtml);
        $request->setPost($post);
        
        // Html purify the request
        $htmlPurifierPlugin = $this->_getHtmlPurifierPlugin('p');
        $htmlPurifierPlugin->filterCollectionsForm($request);
        
        // Make sure the description post variable is clean
        $post = $request->getPost();
        $this->assertEquals($cleanHtml, $post['description']);
    }
    
    public function testFilterCollectionsFormForAllowedAndUnAllowedTagsInDescription()
    {
        $dirtyHtml = '<p><strong>Bob</strong> is <em>dead</em>.</p><br />';
        $cleanHtml = '<p>Bob is dead.</p><br />';
        
        // Create a request with dirty html for the collection description post variable
        $request = new Zend_Controller_Request_HttpTestCase();
        $post = array('description'=>$dirtyHtml);
        $request->setPost($post);
        
        // Html purify the request
        $htmlPurifierPlugin = $this->_getHtmlPurifierPlugin('p,br');
        $htmlPurifierPlugin->filterCollectionsForm($request);
        
        // Make sure the description post variable is clean
        $post = $request->getPost();
        $this->assertEquals($cleanHtml, $post['description']);
    }
    
    public function testFilterItemsFormForUnallowedTag()
    {
        $dirtyHtml = '<p>Bob</p>';
        $cleanHtml = 'Bob';
        
        // Create a request with dirty html for the collection description post variable
        $request = new Zend_Controller_Request_HttpTestCase();
        
        // post looks like Elements[element_id][index] = array([text], [html])
        $post = array('Elements'=> array(
            0 => array(
                        array('text' => $dirtyHtml, 'html' => true),
                        array('text' => $dirtyHtml, 'html' => false)
                 ),
            1 => array(
                        array('text' => $dirtyHtml, 'html' => false),
                        array('text' => $dirtyHtml, 'html' => true)
                 )
        ));
                            
        $request->setPost($post);
        
        // Html purify the request
        $htmlPurifierPlugin = $this->_getHtmlPurifierPlugin();
        $htmlPurifierPlugin->filterItemsForm($request);
        
        // Make sure the description post variable is clean
        $post = $request->getPost();
        $this->assertEquals($cleanHtml, $post['Elements'][0][0]['text']);
        $this->assertEquals($dirtyHtml, $post['Elements'][0][1]['text']);
        $this->assertEquals($dirtyHtml, $post['Elements'][1][0]['text']);
        $this->assertEquals($cleanHtml, $post['Elements'][1][1]['text']);
    }
    
    public function testFilterItemsFormForAnAllowedTag()
    {
        $dirtyHtml = '<p>Bob</p>';
        $cleanHtml = '<p>Bob</p>';
        
        // Create a request with dirty html for the collection description post variable
        $request = new Zend_Controller_Request_HttpTestCase();
        
        // post looks like Elements[element_id][index] = array([text], [html])
        $post = array('Elements'=> array(
            0 => array(
                        array('text' => $dirtyHtml, 'html' => true),
                        array('text' => $dirtyHtml, 'html' => false)
                 ),
            1 => array(
                        array('text' => $dirtyHtml, 'html' => false),
                        array('text' => $dirtyHtml, 'html' => true)
                 )
        ));
                            
        $request->setPost($post);
        
        // Html purify the request
        $htmlPurifierPlugin = $this->_getHtmlPurifierPlugin('p');
        $htmlPurifierPlugin->filterItemsForm($request);
        
        // Make sure the description post variable is clean
        $post = $request->getPost();
        $this->assertEquals($cleanHtml, $post['Elements'][0][0]['text']);
        $this->assertEquals($dirtyHtml, $post['Elements'][0][1]['text']);
        $this->assertEquals($dirtyHtml, $post['Elements'][1][0]['text']);
        $this->assertEquals($cleanHtml, $post['Elements'][1][1]['text']);
    }
    
    public function testFilterItemsFormForAllowedAndUnAllowedTags()
    {
        $dirtyHtml = '<p><strong>Bob</strong> is <em>dead</em>.</p><br />';
        $cleanHtml = '<p>Bob is dead.</p><br />';
        
        // Create a request with dirty html for the collection description post variable
        $request = new Zend_Controller_Request_HttpTestCase();
        
        // post looks like Elements[element_id][index] = array([text], [html])
        $post = array('Elements'=> array(
            0 => array(
                        array('text' => $dirtyHtml, 'html' => true),
                        array('text' => $dirtyHtml, 'html' => false)
                 ),
            1 => array(
                        array('text' => $dirtyHtml, 'html' => false),
                        array('text' => $dirtyHtml, 'html' => true)
                 )
        ));
                            
        $request->setPost($post);
        
        // Html purify the request
        $htmlPurifierPlugin = $this->_getHtmlPurifierPlugin('p,br');
        $htmlPurifierPlugin->filterItemsForm($request);
        
        // Make sure the description post variable is clean
        $post = $request->getPost();
        $this->assertEquals($cleanHtml, $post['Elements'][0][0]['text']);
        $this->assertEquals($dirtyHtml, $post['Elements'][0][1]['text']);
        $this->assertEquals($dirtyHtml, $post['Elements'][1][0]['text']);
        $this->assertEquals($cleanHtml, $post['Elements'][1][1]['text']);
    }
    
    protected function _getHtmlPurifierPlugin($allowedHtmlTags='')
    {
        $htmlPurifier =  $this->_getHtmlPurifier($allowedHtmlTags);
        $htmlPurifierPlugin = new Omeka_Controller_Plugin_HtmlPurifier($htmlPurifier);
        return $htmlPurifierPlugin;
    }
    
    protected function _getHtmlPurifier($allowedHtmlTags='')
    {   
        // Require the HTML Purfier autoloader.
        require_once 'htmlpurifier-3.1.1-lite/library/HTMLPurifier.auto.php';        
        $htmlPurifierConfig = HTMLPurifier_Config::createDefault();

        // Allow HTML tags. Setting this as NULL allows a subest of TinyMCE's 
        // valid_elements whitelist. Setting this as an empty string disallows 
        // all HTML elements.
        $htmlPurifierConfig->set('HTML', 'Allowed', $allowedHtmlTags);

        // Disable caching.
        $htmlPurifierConfig->set('Cache', 'DefinitionImpl', null);

        // Get the purifier as a singleton.
        $htmlPurifier = HTMLPurifier::instance($htmlPurifierConfig);
        
        return $htmlPurifier;
    }        
}
