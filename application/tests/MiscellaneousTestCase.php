<?php
class MiscellaneousTestCase extends UnitTestCase
{
	public function testValidatorErrorsConvertsToNiceErrorMsg()
	{
		$errors = new Omeka_Validator_Errors(array('slug'=>'Needs to be unique', 'title'=>'foobar'));
		$msg =  (string) $errors;
		
		$this->assertEqual("Slug: Needs to be unique\nTitle: foobar", $msg);
	}
}