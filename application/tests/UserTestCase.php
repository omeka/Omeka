<?php 
/**
* 
*/
class UserTestCase extends OmekaTestCase
{
	public function setUp()
	{
		return parent::setUp();
	}
	
	public function testUserFormValidatesCorrectly()
	{
		$this->setUpLiveDb();
		
		//Make sure there is already an entity with this email address 'asdfasdf@omeka.org'
		$e = new Entity;
		$e->first_name = 'foo';
		$e->last_name = 'bar';
		$e->email = 'asdfasdf@omeka.org';
		$e->type = 'Person';
		
		$this->assertTrue($e->isValid());
		
		$e->save();
		
		$u = new User;
		
		//This should validate b/c the email address is used, but not by another user
		$form = array('username'=>'whatever', 'first_name'=>'Alge', 'last_name'=>'Crumpler', 'email'=>'asdfasdf@omeka.org', 'role'=>'contributor');
		
		$u->saveForm($form);
		
		$this->assertTrue($u->isValid());
	}
}
 
?>
