<?php 
/**
* Use this to verify that specific levels of users have proper permissions
*/
class PermissionsTestCase extends OmekaTestCase
{
	public function setUp()
	{
		parent::setUp();
		
		//Load in the hardcoded ACL setup
		include CORE_DIR . DIRECTORY_SEPARATOR . 'acl.php';
		
		$this->acl = $acl;
	}
	
	protected function setCurrentUser(User $user) {
		Zend_Registry::set('logged_in_user', $user);
	}
	
	private function getUser($role)
	{
		$user = new User;
		$user->role = $role;
		
		return $user;
	}
	
	public function testCanMakeItemsPublic()
	{
		$acl = $this->acl;
		
		//Initial user is a super user
		$super = Omeka::loggedIn();
		
		$this->assertTrue($acl->checkUserPermission('Items','makePublic'));
		
		//Contributors cannot make items public
		$contributor = $this->getUser('contributor');
		$this->setCurrentUser($contributor);
				
		$this->assertFalse($acl->checkUserPermission('Items','makePublic'));
		
		//Admin can make items public
		$this->setCurrentUser($this->getUser('admin'));
		$this->assertTrue($acl->checkUserPermission('Items', 'makePublic'));
	}
}
 
?>
