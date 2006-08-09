<?php

class User extends Kea_Domain_Model
{
	public $user_id;
	public $user_username;
	public $user_password;
	public $user_first_name;
	public $user_last_name;
	public $user_email;
	public $user_institution;
	public $user_permission_id = 50;	// If by some random error a user is created they are public.
	public $user_active;
	public $contributor_id;
	
	private $nonsha1_password;
	
	protected $validate = array(	'user_username'			=> array( '/[a-zA-Z0-9_@.]{4,30}/', 'Usernames can only contain letters and underscores, or be in email format, and must be between 4 and 30 characters long.' ),
								'user_email'			=> array( '/^[a-zA-Z]([.]?([[:alnum:]_-]+)*)?@([[:alnum:]\-_]+\.)+[a-zA-Z]{2,4}$/', 'The email provided is invalid.'),
								'user_permission_id'	=> array( '/[1-100]/', 'Users must be designated a permission level.') );
	
	public function getUsername()
	{
		return $this->user_username;
	}
	
	public function getUserFirstName()
	{
		return $this->user_first_name;
	}
	
	public function getUserLastName()
	{
		return $this->user_last_name;
	}
	
	public function getPermissions()
	{
		return $this->user_permission_id;
	}
	
	public function getInstitution()
	{
		return $this->user_institution;
	}
	
	public function getEmail()
	{
		return $this->user_email;
	}
	
	/* Generate password. (i.e. jachudru, cupheki) */
	// http://www.zend.com/codex.php?id=215&single=1
	public function setRandomPassword($length)
	{
	    $vowels = array('a', 'e', 'i', 'o', 'u', '1', '2', '3', '4', '5', '6');
	    $cons = array('b', 'c', 'd', 'g', 'h', 'j', 'k', 'l', 'm', 'n', 'p', 'r', 's', 't', 'u', 'v', 'w', 'tr',
	    'cr', 'br', 'fr', 'th', 'dr', 'ch', 'ph', 'wr', 'st', 'sp', 'sw', 'pr', 'sl', 'cl');

	    $num_vowels = count($vowels);
	    $num_cons = count($cons);
		
		$password = '';
	    while(strlen($password) < $length){
	        $password .= $cons[mt_rand(0, $num_cons - 1)] . $vowels[mt_rand(0, $num_vowels - 1)];
	    }
		$this->user_password = sha1( $password );
		$this->nonsha1_password = $password;
		return $password;
	}
	
	public function isUnique()
	{
		$mapper = $this->mapper();
		$email = $mapper->unique( 'user_email', $this->user_email );
		$username = $mapper->unique( 'user_username', $this->user_username );
		if( $email && $username )
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function emailDetails()
	{
		if( !$this->nonsha1_password )
		{
			return false;
		}
		
		$message = "Your account for the Katrina's Jewish Voice's archive has been created.\n  Please login using your user name and password below.\n\n Username: ".$this->getUsername()." \n Password: ".$this->nonsha1_password." \n\n\n Katrina Jewish Voice Administrator";
		$title = "Your account information for the Katrina's Jewish Voices Archive";
		$header = 'From: webmaster@jwa.org' . "\n" . 'X-Mailer: PHP/' . phpversion();

		mail( $this->getEmail(), $title, $message, $header);
		return true;
	}
	
	public function isContributor()
	{
		if( !empty( $this->contributor_id ) )
		{
			return true;
		}
		return false;
	}
	
	public function getContributor()
	{
		$cont_mapper = new Contributor_Mapper;
		$contributors = $cont_mapper->find()
									->where( 'contributor_id = ?', $this->contributor_id )
									->execute();
		return $contributors->getObjectAt(0);
	}
	
	public static function newPublicUserFromContributor( $contributor )
	{
		$user = new self;
		$user->contributor_id = $contributor->getId();
		$user->user_email = $contributor->contributor_email;
		$user->user_username = $user->user_email;
		$user->setRandomPassword( 9 );
		$user->user_first_name = $contributor->contributor_first_name;
		$user->user_last_name = $contributor->contributor_last_name;
		$user->user_active = 1;
		$user->user_permission_id = 100;
		$user->save();
		$user->emailDetails();
		return $user;
	}
	
}

?>