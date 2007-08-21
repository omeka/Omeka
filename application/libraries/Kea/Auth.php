<?php 
require_once 'Zend/Auth.php';
/**
* Wrapper for caching of Zend_Auth calls
*/
class Kea_Auth extends Zend_Auth
{
	public function isLoggedIn()
	{
		if(Zend::isRegistered('isLoggedIn')) {
			return Zend::Registry( 'isLoggedIn' );
		}
		
		$loggedIn = parent::isLoggedIn();
		
		Zend::register('isLoggedIn', $loggedIn);
		
		return $loggedIn;
	}
}
 
?>