<?php
/**
 *
 * Copyright 2006:
 * George Mason University
 * Center for History and New Media,
 * State of Virginia 
 *
 * LICENSE
 *
 * This source file is subject to the GNU Public License that
 * is bundled with this package in the file GPL.txt, and the
 * specific license found in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL: 
 * http://www.gnu.org/licenses/gpl.txt
 * If you did not receive a copy of the GPL or local license and are unable to
 * obtain it through the world-wide-web, please send an email 
 * to chnm@gmu.edu so we can send you a copy immediately.
 *
 * This software is licensed under the GPL license by the Center
 * For History and New Media, at George Mason University, except 
 * where other free software licenses apply.
 * The source code may only be reused or redistributed if the
 * copyright notice and licensing information above are retained,
 * and other included Zend and Cake licenses, are preserved. 
 * 
 * @author Nate Agrin
 * @contributors Josh Greenburg, Kris Kelly, Dan Stillman
 * @license http://www.gnu.org/licenses/gpl.txt GNU Public License
 */
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
		
		$message = "Your account for the ".SITE_TITLE." archive has been created.\n  Please login using your user name and password below.\n\n Username: ".$this->getUsername()." \n Password: ".$this->nonsha1_password." \n\n\n ".SITE_TITLE." Administrator";
		$title = "Your account information for the ".SITE_TITLE." Archive";
		$header = 'From: ' . "\n" . 'X-Mailer: PHP/' . phpversion();

		mail( $this->getEmail(), $title, $message, $header);
		return true;
	}
	
	public function isSuper()
	{
		return ( $this->getPermissions() == 1 );
	}
	
	public function isAdmin()
	{
		return ( $this->getPermissions() <= 10 );
	}
	
	public function isResearcher()
	{
		return ( $this->getPermissions() <= 20 );
	}
	
	public function isPrivResearcher()
	{
		return ( $this->getPermissions() <= 15 );
	}

	public function isPublic()
	{
		return ( $this->getPermissions() <= 100 );
	}
}

?>