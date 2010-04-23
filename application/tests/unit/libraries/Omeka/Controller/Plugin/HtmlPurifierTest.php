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
class Omeka_Controller_Plugin_HtmlPurifierTest extends Omeka_Test_AppTestCase
{
    protected function _getHtmlPurifierPlugin($allowedHtmlElements='', $allowedHtmlAttributes='')
    {
        $htmlPurifier =  $this->_getHtmlPurifier($allowedHtmlElements, $allowedHtmlAttributes);
        $htmlPurifierPlugin = new Omeka_Controller_Plugin_HtmlPurifier();
        return $htmlPurifierPlugin;
    }
    
    protected function _getHtmlPurifier($allowedHtmlElements='', $allowedHtmlAttributes='')
    {                   
        $htmlPurifier = Omeka_Controller_Plugin_HtmlPurifier::createHtmlPurifier($allowedHtmlElements, $allowedHtmlAttributes);
        Omeka_Controller_Plugin_HtmlPurifier::setHtmlPurifier($htmlPurifier);
        return $htmlPurifier;
    }
    
    public function testConstructor()
    {        
        $htmlPurifier = $this->_getHtmlPurifier();
        $htmlPurifierPlugin = new Omeka_Controller_Plugin_HtmlPurifier($htmlPurifier);
        $this->assertEquals($htmlPurifier, $htmlPurifierPlugin->getHtmlPurifier());
    }
    
    public function testGetHtmlPurifier()
    {
        $htmlPurifier = $this->_getHtmlPurifier();
        $htmlPurifierPlugin = new Omeka_Controller_Plugin_HtmlPurifier();
        $this->assertEquals($htmlPurifier, $htmlPurifierPlugin->getHtmlPurifier());
        
        $htmlPurifier = $this->_getHtmlPurifier('p,strong');
        $htmlPurifierPlugin = new Omeka_Controller_Plugin_HtmlPurifier();
        $this->assertEquals($htmlPurifier, $htmlPurifierPlugin->getHtmlPurifier());
        
        $htmlPurifier = $this->_getHtmlPurifier(null,'*.class');
        $htmlPurifierPlugin = new Omeka_Controller_Plugin_HtmlPurifier();
        $this->assertEquals($htmlPurifier, $htmlPurifierPlugin->getHtmlPurifier());
        
        $htmlPurifier = $this->_getHtmlPurifier('p,strong','*.class');
        $htmlPurifierPlugin = new Omeka_Controller_Plugin_HtmlPurifier();
        $this->assertEquals($htmlPurifier, $htmlPurifierPlugin->getHtmlPurifier());
    }
    
    public function testSetHtmlPurifier()
    {
        $htmlPurifier = $this->_getHtmlPurifier();
        Omeka_Controller_Plugin_HtmlPurifier::setHtmlPurifier($htmlPurifier);
        $this->assertEquals($htmlPurifier, Omeka_Controller_Plugin_HtmlPurifier::getHtmlPurifier());
        
        $htmlPurifier = $this->_getHtmlPurifier('p,strong');
        Omeka_Controller_Plugin_HtmlPurifier::setHtmlPurifier($htmlPurifier);
        $this->assertEquals($htmlPurifier, Omeka_Controller_Plugin_HtmlPurifier::getHtmlPurifier());
        
        $htmlPurifier = $this->_getHtmlPurifier(null,'*.class');
        Omeka_Controller_Plugin_HtmlPurifier::setHtmlPurifier($htmlPurifier);
        $this->assertEquals($htmlPurifier, Omeka_Controller_Plugin_HtmlPurifier::getHtmlPurifier());
        
        $htmlPurifier = $this->_getHtmlPurifier('p,strong','*.class');
        Omeka_Controller_Plugin_HtmlPurifier::setHtmlPurifier($htmlPurifier);
        $this->assertEquals($htmlPurifier, Omeka_Controller_Plugin_HtmlPurifier::getHtmlPurifier());
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
    
    public function testFilterCollectionsFormForUnallowedElementInDescription()
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
    
    public function testFilterCollectionsFormForAllowedElementInDescription()
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
    
    public function testFilterCollectionsFormForAllowedAndUnAllowedElementsInDescription()
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

    public function testFilterItemsFormForUnallowedElement()
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


    public function testFilterItemsFormForAnAllowedElement()
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

    public function testFilterItemsFormForAllowedAndUnallowedElements()
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

    public function testFilterThemesFormForAllowedAndUnallowedElements()
    {
        $dirtyHtml = '<p><strong>Bob</strong> is <em>dead</em>.</p><br />';
        $cleanHtml = '<p>Bob is dead.</p><br />';

        // Create a request with dirty html for the collection description post variable
        $request = new Zend_Controller_Request_HttpTestCase();

        // post can be any nested array of strings
        $post = array(
            'whatever' => $dirtyHtml,
            'NestedArray'=> array(
                0 => array(
                        array('text' => $dirtyHtml),
                        array('text' => $dirtyHtml)
                 ),
                1 => array(
                        array('text' => $dirtyHtml),
                        array('text' => $dirtyHtml)
                 )
            ),
            'whatever2' => $dirtyHtml
        );

        $request->setPost($post);

        // Html purify the request
        $htmlPurifierPlugin = $this->_getHtmlPurifierPlugin('p,br');
        $htmlPurifierPlugin->filterThemesForm($request);

        // Make sure the description post variable is clean
        $post = $request->getPost();
        $this->assertEquals($cleanHtml, $post['whatever']);
        $this->assertEquals($cleanHtml, $post['NestedArray'][0][0]['text']);
        $this->assertEquals($cleanHtml, $post['NestedArray'][0][1]['text']);
        $this->assertEquals($cleanHtml, $post['NestedArray'][1][0]['text']);
        $this->assertEquals($cleanHtml, $post['NestedArray'][1][1]['text']);
        $this->assertEquals($cleanHtml, $post['whatever2']);
    }
}