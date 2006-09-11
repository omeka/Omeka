<?php

class UsersController extends Kea_Action_Controller
{	
	public function __construct()
	{
		$this->attachBeforeFilter(
			new RequireLogin( array( '_add' => '10' ) )
		);
	}
	
	protected function _findById( $id = null)
	{
		if( !$id )
		{
			$id = self::$_request->getProperty( 'id' ) ?
					self::$_request->getProperty( 'id' ) : 
						(isset( self::$_route['pass'][0] ) ?
						self::$_route['pass'][0] :
						0);	
		}
		
		$mapper = new User_Mapper();
/*		$user = $mapper->find()
					  ->where( 'user_id = ?', $id )
					  ->execute();
					*/
		$stmt = $mapper->select()->where( 'user_id = ?', $id );
		$result = $mapper->query( $stmt );
		if( $result->num_rows == 1 ) {
			return $mapper->load( $result );
		}			
	}

	protected function _findByEmail( $email )
	{


		$mapper = new User_Mapper();
/*		$user = $mapper->find()
					  ->where( 'user_id = ?', $id )
					  ->execute();
					*/
		$stmt = $mapper->select()->where( 'user_email = ?', $email );
		$result = $mapper->query( $stmt );
		if( $result->num_rows > 0 ) {
			return $mapper->load( $result );
		}			
	}

	private function commitForm()
	{
		$user = new User( self::$_request->getProperty( 'user' ) );
		if( $this->validates( $user ) ) {
			return $user->save();
		}
		return false;
	}

	protected function _all( $type = 'object' , $sort = null)
	{
		$mapper = new User_Mapper;
		switch( $type ) {
			case( 'object' ):
				if (@$sort == 'alpha') {
					$select = $mapper->find()
									->order( array( 'users.user_last_name' => 'ASC' ) );
					return $mapper->findObjects($select);
				}
				else {
					return $mapper->allObjects();
				}
			break;
			case( 'array' ):
			if (@$sort == 'alpha') {
				$select = $mapper->find()
								->order( array( 'users.user_last_name' => 'ASC' ) );
				return $mapper->findArray($select);
			}
			else {
				return $mapper->allArray();
			}
			break;
		}
		return false;
	}	
	protected function _edit()
	{
		if( !self::$_request->getProperty( 'user_edit' ) )
		{
			return false;
		}
		
		$user = self::$_session->getUser();
		$user->user_first_name = self::$_request->getProperty( 'user_first_name' );
		$user->user_last_name = self::$_request->getProperty( 'user_last_name' );
		$user->user_institution = self::$_request->getProperty( 'user_institution' );
		
		// Update associated contributor
		$contributor = $user->getContributor();
		$contributor->contributor_first_name = $user->user_first_name;
		$contributor->contributor_last_name = $user->user_last_name;
		$contributor->save();
		
		
		if (self::$_request->getProperty( 'oldPassword' )):
			if (sha1(self::$_request->getProperty( 'oldPassword' )) == $user->user_password):
				$user->user_password = sha1(self::$_request->getProperty( 'newPassword' ));
			endif;
		endif;
		
		$user->save();
	}

	protected function _adminEdit()
	{
		if( self::$_request->getProperty( 'user_edit' ) ) {
			
			if( $this->commitForm() ) {
				$this->redirect( BASE_URI . DS . 'users' . DS . 'all');
				return;
			} 	
		} else {
			return $this->findById();
		}
	}
	
	final protected function _login()
	{
		if( self::$_request->getProperty( 'user_login' ) ) {
			$username = self::$_request->getProperty( 'username' );
			$password = self::$_request->getProperty( 'password' );

			$mapper = new User_Mapper;
			try {
				$user = $mapper->login( $username, $password ) ;
				self::$_session->loginUser( $user );
				//if (self::$_session->getUser()) exit;
			} catch ( Kea_Exception $e ) {
				return 'This username and password don\'t match our records. Please try again.';
			}
		}
	}
	
	final protected function _logout()
	{
		self::$_session->logoutUser();
		$location = self::$_session->getSavedLocation();
		return $location;
	}
	
	public function _add()
	{
		if( self::$_request->getProperty( 'user_add' ) ) {
			$user = new User( self::$_request->getProperty( 'user' ) );

			if( !$user->isUnique() )
			{
				self::$_session->flash( 'The username or email address you have chosen is already taken.');
				return $user;
			}
			
			if (!@$user->user_permission_id) $user->user_permission_id = 50;
			if (!@$user->user_contributor_id) $user->user_contributor_id = NULL;

			if( $this->validates( $user ) ) {
				$password = $user->setRandomPassword(10);
				$user->user_active = 1;
				$user->save();
				
				$message = "Welcome!\n\nYour account for the ".SITE_TITLE." archive has been created. Please login using your user name and password below.\n\n     Username: ".$user->getUsername()."\n\n     Password: $password\n\nTo login, please return to the ".SITE_TITLE." website,".SITE_BASE_URL."(or use any other page on the site).\n\nBe aware that we log you out after 15 minutes of inactivity to help protect people using shared computers (at libraries, for instance).\n\n".SITE_TITLE." Administrator";
				$title = "Your account information for the ".SITE_TITLE." Archive";
				$header = 'From: DONOTREPLY@hurricanearchive.org' . "\n" . 'X-Mailer: PHP/' . phpversion();

				mail( $user->getEmail(), $title, $message, $header);
				
				$this->redirect( BASE_URI . DS . 'users' . DS . 'all' );
				return false;
			}
			else {
				self::$_session->flash('User could not be created.');
				return $user;				
			}
			return $user;
		}
		return new User();
	}
	
	protected function _delete()
	{
		if( $id = self::$_request->getProperty( 'user_id' ) ) {
			$mapper = new User_Mapper;
			$mapper->delete( $id );
			
			$this->redirect( BASE_URI . DS . 'users' . DS . 'all' );
		}
	}
	
	public function _addPublicUser()
	{
		if( self::$_request->getProperty( 'user_add' ) ) {
			$user = new User( self::$_request->getProperty( 'user' ) );
			if( !$user->isUnique() )
			{
				self::$_session->flash( 'The username or email address you have chosen is already taken.');
				return;
			}
			
			$user->user_permission_id = 50;
			$user->user_contributor_id = NULL;
			if( $this->validates( $user ) ) {
				$password = $user->setRandomPassword(10);
				$user->user_active = 1;
				$user->save();
				
				$message = "Your account for the ".SITE_TITLE." archive has been created.\n  Please login using your user name and password below.\n\n Username: ".$user->getUsername()." \n Password: $password \n\n  Be aware that we log you out after 15 minutes of inactivity to help protect people using shared computers (at libraries, for instance).\n".SITE_TITLE." Administrator";
				$title = "Your account information for the ".SITE_TITLE." Archive";
				$header = 'From: DONOTREPLY@hurricanearchive.org' . "\n" . 'X-Mailer: PHP/' . phpversion();

				mail( $user->getEmail(), $title, $message, $header);
				
				//$this->redirect( BASE_URI . DS . 'users' . DS . 'all' );
				return 'Your account has been successfully created. Please check your e-mail for further instructions.';
			}
			else {
				echo 'no user created'; exit;				
			}
			return $user;
		}
	}
	
	public function _mailNewPassword( $user_email = null)
	{
		if (!$user_email):
			return 'Sorry, please enter an e-mail address';
		else:
			$user = $this->findByEmail($user_email);
			if ($user):
	
				// Create new password
				$new = $user->setRandomPassword(10);
				
				// Save new password
				$mapper = new User_Mapper;
				$sql = "UPDATE users SET user_password = SHA1('$new') WHERE user_id = '$user->user_id'";
				if( $mapper->query( $sql ) ) {
					
					// Send message
					$message = "Your password for the ".SITE_TITLE." archive has been reset.\n  Please login using your user name and password below.\n\n Username: ".$user->getUsername()." \n Password: $new \n\n\n ".SITE_TITLE." Administrator";
					$title = "Your account information for the ".SITE_TITLE." Archive";
					$header = 'From: DONOTREPLY@hurricanearchive.org' . "\n" . 'X-Mailer: PHP/' . phpversion();
					mail( $user->getEmail(), $title, $message, $header);
					

					// Return message
					return 'A new password has been sent to your e-mail address.';
				} else {
					throw new Kea_DB_Mapper_Exception( self::$_adapter->error() );
				}
			else :
				return 'Sorry, couldn\'t find that e-mail address in our records.';
			endif;
		endif;
		
	}
	
	protected function _changePassword( )
	{

		if( self::$_request->getProperty( 'change_password' ) ) {
			
			
			$new1 = self::$_request->getProperty( 'new_password_1' );
			$new2 = self::$_request->getProperty( 'new_password_2' );

			// Superuser doesn't have to enter an old password
			if (self::$_session->isSuper()):
				$id = self::$_request->getProperty( 'user_id' );
				//$user = $this->findById($id);
				$old = null;
			else:
				$old = self::$_request->getProperty( 'old_password' );		
				if( empty( $new1 ) || empty( $new2 ) || empty( $old ) ) {
					self::$_session->flash( 'You must enter the information in all fields on the form.' );
					return;
				}
			endif;

			if( $new1 !== $new2 ) {
				self::$_session->flash('The new passwords do not match.');
				return;
			}
			
			if ($this->doChangePassword( $id, $old, $new1 )):
				
				// Send message
				$user = $this->findById($id);
				$message = "Your password for the ".SITE_TITLE." archive has been reset.\n  Please login using your user name and password below.\n\n Username: ".$user->getUsername()." \n Password: $new1 \n\n\n ".SITE_TITLE." Administrator";
				$title = "Your account information for the ".SITE_TITLE." Archive";
				$header = 'From: DONOTREPLY@hurricanearchive.org' . "\n" . 'X-Mailer: PHP/' . phpversion();
				mail( $user->getEmail(), $title, $message, $header);
			endif;
			
		} else {
				return $this->findById();
		}
	}
	
	private function doChangePassword( $user_id, $old, $new )
	{
		$mapper = new User_Mapper;
			
		// Superuser doesn't have to enter an old password
		if (!self::$_session->isSuper()):		
			$select = $mapper->select();
			$select->from( 'users', 'user_id' )
					->where( 'user_id = ?', $user_id )
					->where( 'user_password = SHA1( ? )', $old );

			$result = self::$_adapter->fetchOne( $select );

			if( $result != $user_id ) {
				self::$_session->flash('Incorrect old password.');
				return false;
			}
		endif;
		
		$sql = "UPDATE users SET user_password = SHA1('$new') WHERE user_id = '$user_id'";
		if( $mapper->query( $sql ) ) {
			return true;
		} else {
			self::$_session->flash( self::$_adapter->error() );
			return false;
		}
	}
	
	
	
	
	
	// Needs work [JMG]
	protected function _requireLogin($redirect = false)
	{
		// If you're not logged in you have to sign in
		if( !self::$_session->getUser() ) {
			issetor( self::$_route['directory'], null );
			if ($redirect) {
				$this->redirect( WEB_ROOT.DS.$redirect.DS );
			}
			elseif (!self::$_route['template'] == 'login') {
				$this->redirect( WEB_ROOT.DS.'login'.DS );
			}
		}
		
		// If you are an admin, don't visit the login page dummy
		if( self::$_route['template'] == 'login'
			&& self::$_session->getUser() ) {
				$this->redirect( WEB_ROOT.DS );
		}
	}
	

}

?>