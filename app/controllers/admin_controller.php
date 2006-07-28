<?php

class AdminController extends Kea_Action_Controller
{
	protected function _protect()
	{
		// If you're not an admin you have to sign in
		if( !self::$_session->getUser()
			|| self::$_session->getUser()->getPermissions() > 30 ) {
			issetor( self::$_route['directory'], null );
			if( self::$_route['template'] != 'login' || self::$_route['directory'] != null ) {
				$this->redirect( WEB_ROOT . ADMIN_THEME_DIR . DS . 'login' );
			}
		}
		
		// If you are an admin, don't visit the login page dummy
		if( self::$_route['template'] == 'login'
			&& self::$_session->getUser()
			&& self::$_session->getUser()->getPermissions() <= 30 ) {
				$this->redirect( WEB_ROOT . ADMIN_THEME_DIR . DS );
		}
	}
}

?>

