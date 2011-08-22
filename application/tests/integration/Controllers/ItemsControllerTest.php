<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2011
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Tests for the ItemsController.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Omeka_Controller_ItemsControllerTest extends Omeka_Test_AppTestCase
{   
    const XSS_QUERY_STRING = '"><script>alert(11639)</script>';

    public function setUp()
    {
        parent::setUp();
        $this->_authenticateUser($this->_getDefaultUser());
        self::dbChanged(false);
    }

    public function dispatch($url, $callback = null)
    {
        if ($callback) {
            $callback = (array)$callback;
            foreach ($callback as $c) {
                $this->$c();
            }
        }
        return parent::dispatch($url);
    }

    /**
     * Data provider for ItemsController routes.
     */
    public static function routes()
    {
        return array(
            array('/items/add', 'items', 'add'),
            array('/items/edit/1', 'items', 'edit'),
            array('/items/advanced-search', 'items', 'advanced-search'),
            array('/items/modify-tags/1', 
                  'items', 
                  'modify-tags', 
                  array('_makePost')
            ),
            array('/items/tags', 'items', 'tags'),
            array('/items/tag-form?id=1', 
                  'items', 
                  'tag-form', 
                  array('_makeXmlHttpRequest')
            )
        );
    }

    /**
     * @dataProvider routes
     */
    public function testRouting($url, $controller, $action, $callback = null)
    {
        $this->dispatch($url, $callback);
        $this->assertController($controller);
        $this->assertAction($action);
    }

    public static function formPresence()
    {
        return array(
            array('/items/browse', 'simple-search'),
            array('/items/add', 'item-form'),
            array('/items/edit/1', 'item-form'),
            array('/items/advanced-search', 'advanced-search-form'),
            array('/items/browse', 'items-browse'),
        );
    }

    /**
     * @dataProvider formPresence
     */
    public function testFormPresence($url, $formId, $callback = null)
    {
        $this->dispatch($url, $callback);
        $this->assertQuery("form#$formId", (string)$this->response->getBody());
    }

    public static function ajaxRequired()
    {
        return array(
            array('/items/element-form'),
            array('/items/change-type'),
            array('/items/tag-form'),
        );
    }

    /**
     * Test that certain actions must only be accessed via XMLHttpRequest.
     *
     * @dataProvider ajaxRequired
     */
    public function testActionsDisallowedWithoutXmlHttpRequest($url)
    {
        $this->dispatch($url);
        $this->assertController('error');
        $this->assertAction('not-found');
    }

    public static function postRequired()
    {
        return array(
            array('/items/element-form', '_makeXmlHttpRequest'),
            array('/items/modify-tags'),
            array('/items/delete/1'),
            array('/items/change-type', '_makeXmlHttpRequest'),
            array('/items/batch-edit-save'),
        );
    }

    /**
     * @dataProvider postRequired
     */
    public function testActionsRequiringPost($url, $callback = null)
    {
        $this->dispatch($url, $callback);
        $this->assertController('error');
        $this->assertAction('method-not-allowed');
    }

    public static function ajaxPartials()
    {
        return array(
            array('/items/advanced-search'),
        );
    }

    /**
     * Test that AJAX actions do not render html or body tags.
     *
     * @dataProvider ajaxPartials
     */
    //public function testAjaxActionsRenderPartials($url)
    //{
        //$this->_makeXmlHttpRequest();
        //$this->dispatch($url);
        //$dom = new DOMDocument();
        //$dom->loadHTML($this->response->getBody());
        //var_dump($dom->documentElement);exit;
        ////$xml = new SimpleXmlElement($this->response->getBody());
        //$this->assertNotQuery("form", $this->response->getBody());
        ////$this->assertNotQuery("body");
    //}

    public function testAdvancedSearchXSSInjection()
    {
        $url = '/items/advanced-search?' . http_build_query(array(
            'search' => self::XSS_QUERY_STRING,
            'advanced' => array(
                array('element_id' => self::XSS_QUERY_STRING,
                      'type' => self::XSS_QUERY_STRING,
                      'terms' => self::XSS_QUERY_STRING,
                )
            ),
            'range' => self::XSS_QUERY_STRING,
            'collection' => self::XSS_QUERY_STRING,
            'type' => self::XSS_QUERY_STRING,
            'user' => self::XSS_QUERY_STRING,
            'tags' => self::XSS_QUERY_STRING,
            'public' => self::XSS_QUERY_STRING,
            'featured' => self::XSS_QUERY_STRING,
            'submit_search' => self::XSS_QUERY_STRING,
        ));
        $this->dispatch($url);
        $this->assertController('items');
        $this->assertAction('advanced-search');
        $this->assertNotContains(self::XSS_QUERY_STRING, $this->response->getBody());
    }

    public function testBrowse()
    {
        $this->dispatch('/items/browse');
        $this->assertQueryContentContains("table .title", Installer_Test::TEST_ITEM_TITLE);
    }

    public function testModifyTags()
    {
        $this->request->setPost(array('tags' => 'foobar',
                                      'id' => 1));
        $this->request->setMethod('POST');
        $this->dispatch('/items/modify-tags');
        $item = $this->db->getTable('Item')->find(1);
        $this->assertEquals("foobar", $item->Tags[0]->name);
    }

    public function testElementFormXmlHttpRequest()
    {
        $this->_makeXmlHttpRequest();
        $this->_makePost(array(
            'element_id' => 1,
            'item_id' => 1,
            'Elements' => array(
                1 => array(
                    array(
                        'text' => 'foo',
                        'html' => '1',
                    )
                )
            )
        ));
        $this->dispatch('/items/element-form');
        $this->assertQueryContentContains("textarea#Elements-1-0-text", "foo");
    }

    public function testDelete()
    {
        self::dbChanged(true);
        $hash = new Zend_Form_Element_Hash('confirm_delete_hash');
        $hash->initCsrfToken();
        $this->_makePost(array(
            'confirm_delete_hash' => $hash->getHash()
        ));
        $this->dispatch('/items/delete/1');
        $this->assertEquals(0, $this->db->getTable('Item')->count());
        $this->assertRedirectTo('/items/browse');
    }

    /**
     * @expectedException Omeka_Controller_Exception_404
     */
    public function testDeleteWithoutHash()
    {
        self::dbChanged(true);
        $this->request->setMethod('POST');
        $this->dispatch('/items/delete/1');
    }

    public function testChangeTypeXmlHttpRequest()
    {
        $this->_makeXmlHttpRequest();
        $this->_makePost(array(
            'item_id' => 1,
            'type_id' => 1,
        ));
        $this->dispatch('/items/change-type');
        $this->assertNotRedirect();
    }

    public function testTagFormXmlHttpRequest()
    {
        $this->_makeXmlHttpRequest();
        $this->dispatch('/items/tag-form?id=1');
        $this->assertNotRedirect();
    }

    protected function _addOneItem()
    {
        self::dbChanged(true);
        $item = insert_item();
        $this->assertTrue($item->exists());
        return $item;
    }

    protected function _makeXmlHttpRequest()
    {
        $this->request->setHeader('X_REQUESTED_WITH', 'XMLHttpRequest');
        $this->assertTrue($this->request->isXmlHttpRequest());
    }

    protected function _makePost($post = null)
    {
        $this->request->setMethod('POST');
        if ($post) {
            $this->request->setPost($post);
        }
    }

}
