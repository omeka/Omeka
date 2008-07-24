<?php
class MiscellaneousTestCase extends PHPUnit_Framework_TestCase
{
	public function testValidatorErrorsConvertsToNiceErrorMsg()
	{
		$errors = new Omeka_Validator_Errors(array('slug'=>'Needs to be unique', 'title'=>'foobar'));
		$msg =  (string) $errors;
		
		$this->assertEquals("Slug: Needs to be unique\nTitle: foobar", $msg);
	}
}