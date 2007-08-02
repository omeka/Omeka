<?php 
/**
 * This will test the theme functions 
 * @copyright CHNM,  3 May, 2007
 * @package Omeka
 **/

require_once HELPERS;
require_once CONTROLLER_DIR.DIRECTORY_SEPARATOR.'UsersController.php'; 
require_once 'Zend/Auth.php';
require_once 'Kea/Auth/Token.php';
Mock::generate('Kea_Acl');
Mock::generate('Zend_Auth');
Mock::generate('Kea_Auth_Token');

class UserThemeFunctionsTestCase extends OmekaTestCase
{

	private function login($role='super')
	{
		if($role == 'super') {
			$user = $this->getTable('User')->find(1);
		}else {
			$user = $this->getTable('User')->find(2);
		}
		
		$mockAuth = new MockZend_Auth();
		$mockToken = new MockKea_Auth_Token();
		
		$mockAuth->setReturnValue('getToken',$mockToken);
		$mockToken->setReturnValue('getIdentity',$user);
		$mockAuth->setReturnValue('isLoggedIn',true);
		
		Zend::register('auth',$mockAuth);
		return $user;		
	}
	
	private function logout()
	{
		$auth = Zend::Registry('auth');
		
	}
	
	public function testKeaLoggedIn()
	{
		$user = $this->login();
		$this->assertEqual($user->id, Kea::loggedIn()->id);		
	}
	
	public function testHasPermission()
	{
		$acl = new MockKea_Acl();
		
		$user = $this->login('super');
		$acl->setReturnValue('isAllowed',true,array('super','Items','edit'));
		
		Zend::register('acl',$acl);	
		
		$this->assertTrue(has_permission('Items','edit') );
		$this->assertFalse(has_permission('Items','show') );
		
		$this->assertTrue(has_permission('super'));
		$this->assertFalse(has_permission('researcher'));
	}
	
	public function testSrcFunction()
	{
		$hard_path = dirname(dirname(__FILE__));
		$web_path = WEB_ROOT.DIRECTORY_SEPARATOR.'application';
		Zend::register('theme_path',$hard_path);
		Zend::register('theme_web',$web_path);
		
		ob_start();
		src('setup.sql','tests',null);
		$src = ob_get_clean();
		$this->assertEqual($web_path.DIRECTORY_SEPARATOR.'tests/setup.sql',$src);
		ob_flush();
		
		Zend::register('theme_path',dirname(__FILE__));
		src('setup.sql',null);
		$src = ob_get_clean();
		$this->assertEqual($web_path.DIRECTORY_SEPARATOR.'setup.sql',$src);
		ob_flush();
		
		Zend::register('theme_path','foobar');
		$this->expectException(new Exception('Cannot find bar/foo.php'));
		echo src('foo', 'bar', 'php', false);
	}
	
	public function testJsFunction()
	{
		$hard_path = dirname(__FILE__);
		$web_path = WEB_ROOT.DIRECTORY_SEPARATOR.'application';
		Zend::register('theme_path',$hard_path);
		Zend::register('theme_web',$web_path);
		
		ob_start();
		js('test',null);
		$js = ob_get_clean();
		$this->assertEqual('<script type="text/javascript" src="'.$web_path.'/test.js"></script>',$this->stripSpace($js));
	}
	
	public function testCssFunction() {}
	
	public function testImgFunction() {}
	
	public function testCommonFunction() {}
	
	public function testHeadFunction() {}
	
	public function testFootFunction() {}

	public function testErrorFunction() {}
	
	public function testTagCloudFunction() {}
	
	public function testUriFunction() {}
	
	public function testFlashFunction() {}
	
	public function testNavFunction() {}
	
	public function testIsCurrentFunction() {}
	
	public function testPluginFunction() {}

	public function testPluginHeaderFunction() {}
	
	public function testOmekaRequestFunction() {
		$user = $this->login();
		
		$acl = new MockKea_Acl();
		
		//isAllowed will always return true b/c the User is a super user
		$acl->setReturnValue('isAllowed',true,array('*','*','*'));
		
		$acl = Zend::register('acl',$acl);
		
		require_once CONTROLLER_DIR.DIRECTORY_SEPARATOR.'ItemsController.php';
		$controller = 'Items';
		$action = 'show';
		$params = array('id'=>1);
		$return = 'item';
		
		$item = _make_omeka_request($controller,$action,$params,$return);
		$this->assertEqual(1,$item->id);
	}
	
	public function testModelTotalFunction() {}
		
	public function testSettingsFunction() {}
	
	public function testDisplayEmptyFunction() {}
	
	public function testArchiveImageFunction() {}
		
	public function tearDown()
	{
		Zend::__unsetRegistry();
	}
}