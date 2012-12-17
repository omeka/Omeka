<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

class Models_Mixin_PublicFeaturedTest extends Omeka_Test_AppTestCase
{   
    public function setUp()
    {
       parent::setUp();
       $this->_authenticateUser($this->_getDefaultUser()); // login as admin
    }
    
    public function testConstructor()
    {
        $item = new Item();
        $mixin = new Mixin_PublicFeatured($item);
        $this->assertInstanceOf('Mixin_PublicFeatured', $mixin);
        $this->assertFalse($mixin->isPublic());
        $this->assertFalse($mixin->isFeatured());        
    }
    
    public function testFireHookMakeRecordPublic()
    {
        $item = new Item();
        $mixin = new Mixin_PublicFeatured($item);
        $this->assertFalse($mixin->isPublic());

        $mock = $this->getMock('stdClass', array('myCallBack'));
        $mock->expects($this->once())
             ->method('myCallBack')
             ->will($this->returnValue(true));
        
        $hookName = 'make_item_public';
        $callback = array($mock, 'myCallBack');
        $plugin = '__global__';
        get_plugin_broker()->addHook($hookName, $callback, $plugin);
        
        $this->assertEquals(0, $item->public);
        $item->public = true;
        $item->save();
    }
    
    public function testFireHookMakeRecordFeatured()
    {
        $item = new Item();
        $mixin = new Mixin_PublicFeatured($item);
        $this->assertFalse($mixin->isFeatured());
        
        $mock = $this->getMock('stdClass', array('myCallBack'));
        $mock->expects($this->once())
             ->method('myCallBack')
             ->will($this->returnValue(true));
        
        $hookName = 'make_item_featured';
        $callback = array($mock, 'myCallBack');
        $plugin = '__global__';
        get_plugin_broker()->addHook($hookName, $callback, $plugin);
        
        $this->assertEquals(0, $item->featured);
        $item->featured = true;
        $item->save();
    }
    
    public function testFireHookMakeRecordPublicFeatured()
    {
        $item = new Item();
        $mixin = new Mixin_PublicFeatured($item);
        $this->assertFalse($mixin->isPublic());
        $this->assertFalse($mixin->isFeatured());

        $mockA = $this->getMock('stdClass', array('myCallBackA'));
        $mockA->expects($this->once())
             ->method('myCallBackA')
             ->will($this->returnValue(true));
             
        $hookName = 'make_item_public';
        $callback = array($mockA, 'myCallBackA');
        $plugin = '__global__';
        get_plugin_broker()->addHook($hookName, $callback, $plugin);
        
        $mockB = $this->getMock('stdClass', array('myCallBackB'));
        $mockB->expects($this->once())
             ->method('myCallBackB')
             ->will($this->returnValue(true));
             
        $hookName = 'make_item_featured';
        $callback = array($mockB, 'myCallBackB');
        $plugin = '__global__';
        get_plugin_broker()->addHook($hookName, $callback, $plugin);
        
        $this->assertEquals(0, $item->public);
        $this->assertEquals(0, $item->featured);
        $item->public = true;
        $item->featured = true;
        $item->save();
    }
    
    public function testFireHookMakeRecordNotPublic()
    {
        $item = new Item();
        $mixin = new Mixin_PublicFeatured($item);
        $this->assertFalse($mixin->isPublic());
        $item->public = true;
        $item->save();
        $this->assertTrue($mixin->isPublic());
                        
        $mock = $this->getMock('stdClass', array('myCallBack'));
        $mock->expects($this->once())
             ->method('myCallBack')
             ->will($this->returnValue(true));
        
        $hookName = 'make_item_not_public';
        $callback = array($mock, 'myCallBack');
        $plugin = '__global__';
        get_plugin_broker()->addHook($hookName, $callback, $plugin);
        
        $this->assertEquals(1, $item->public);
        $item->public = false;
        $item->save();
    }
    
    public function testFireHookMakeRecordNotFeatured()
    {
        $item = new Item();
        $mixin = new Mixin_PublicFeatured($item);
        $this->assertFalse($mixin->isFeatured());
        $item->featured = true;
        $item->save();
        $this->assertTrue($mixin->isFeatured());
                        
        $mock = $this->getMock('stdClass', array('myCallBack'));
        $mock->expects($this->once())
             ->method('myCallBack')
             ->will($this->returnValue(true));
        
        $hookName = 'make_item_not_featured';
        $callback = array($mock, 'myCallBack');
        $plugin = '__global__';
        get_plugin_broker()->addHook($hookName, $callback, $plugin);
        
        $this->assertEquals(1, $item->featured);
        $item->featured = false;
        $item->save();
    }
    
    public function testFireHookMakeRecordNotPublicNotFeatured()
    {
        $item = new Item();
        $mixin = new Mixin_PublicFeatured($item);
        $this->assertFalse($mixin->isPublic());
        $this->assertFalse($mixin->isFeatured());
        $item->public = true;
        $item->featured = true;
        $item->save();
        $this->assertTrue($mixin->isPublic());
        $this->assertTrue($mixin->isFeatured());
                        
        $mockA = $this->getMock('stdClass', array('myCallBackA'));
        $mockA->expects($this->once())
              ->method('myCallBackA')
              ->will($this->returnValue(true));
        
        $hookName = 'make_item_not_public';
        $callback = array($mockA, 'myCallBackA');
        $plugin = '__global__';
        get_plugin_broker()->addHook($hookName, $callback, $plugin);
        
        $mockB = $this->getMock('stdClass', array('myCallBackB'));
        $mockB->expects($this->once())
              ->method('myCallBackB')
              ->will($this->returnValue(true));
        
        $hookName = 'make_item_not_featured';
        $callback = array($mockB, 'myCallBackB');
        $plugin = '__global__';
        get_plugin_broker()->addHook($hookName, $callback, $plugin);
        
        $this->assertEquals(1, $item->public);        
        $this->assertEquals(1, $item->featured);
        $item->public = false;
        $item->featured = false;
        $item->save();
    }
}
