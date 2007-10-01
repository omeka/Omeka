<?php 
require_once 'Zend/Auth.php';
/**
* Wrapper for caching of Zend_Auth calls
*/
class Omeka_Auth extends Zend_Auth
{
	public function hasIdentity()
	{
		if(Zend_Registry::isRegistered('hasIdentity')) {
			return Zend_Registry::get( 'hasIdentity' );
		}
		
		$loggedIn = parent::hasIdentity();
		
		Zend_Registry::set('hasIdentity', $loggedIn);
		
		return $loggedIn;
	}
}
 
?>
