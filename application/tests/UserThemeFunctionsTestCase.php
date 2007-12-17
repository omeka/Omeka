<?php 
/**
 * This will test the theme functions 
 * @copyright CHNM,  3 May, 2007
 * @package Omeka
 **/

require_once HELPERS;
require_once CONTROLLER_DIR.DIRECTORY_SEPARATOR.'UsersController.php'; 
require_once 'Zend/Auth.php';
require_once 'Omeka/Auth/Token.php';
Mock::generate('Omeka_Acl');
Mock::generate('Zend_Auth');
Mock::generate('Omeka_Auth_Token');

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
		$mockToken = new MockOmeka_Auth_Token();
		
		$mockAuth->setReturnValue('getToken',$mockToken);
		$mockToken->setReturnValue('getIdentity',$user);
		$mockAuth->setReturnValue('hasIdentity',true);
		
		Zend_Registry::set('auth',$mockAuth);
		return $user;		
	}
	
	private function logout()
	{
		$auth = Zend_Registry::get('auth');
	}
	
	public function testOmekaLoggedIn()
	{
		$user = $this->login();
		$this->assertEqual($user->id, Omeka::loggedIn()->id);		
	}
	
	public function testHasPermission()
	{
		$acl = new MockOmeka_Acl();
		
		$user = $this->login('super');
		$acl->setReturnValue('isAllowed',true,array('super','Items','edit'));
		
		Zend_Registry::set('acl',$acl);	
		
		$this->assertTrue(has_permission('Items','edit') );
		$this->assertFalse(has_permission('Items','show') );
		
		$this->assertTrue(has_permission('super'));
		$this->assertFalse(has_permission('researcher'));
	}
	
	public function testJsFunction()
	{
		$hard_path = dirname(__FILE__);
		$web_path = WEB_ROOT.DIRECTORY_SEPARATOR.'application';
		Zend_Registry::set('theme_path',$hard_path);
		Zend_Registry::set('theme_web',$web_path);
		
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
		
		$acl = new MockOmeka_Acl();
		
		//isAllowed will always return true b/c the User is a super user
		$acl->setReturnValue('isAllowed',true,array('*','*','*'));
		
		$acl = Zend_Registry::set('acl',$acl);
		
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