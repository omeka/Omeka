<?php

class Contributor extends Kea_Domain_Model
{
	public $contributor_id;
	public $contributor_first_name;
	public $contributor_middle_name;
	public $contributor_last_name;
	public $contributor_email;
	public $contributor_phone;
	public $contributor_birth_year;
	public $contributor_gender;
	public $contributor_race;
	public $contributor_race_other;
	public $contributor_contact_consent;
	public $contributor_fax;
	public $contributor_address;
	public $contributor_city;
	public $contributor_state;
	public $contributor_zipcode;
	public $contributor_occupation;
	public $contributor_institution;
	public $contributor_ip_address;
	
	protected $validate		=	array(//	'contributor_first_name'		=> array( '/(\w)+/', 'Please provide a first name.' ),
									//	'contributor_last_name'			=> array( '/(\w)+/', 'Please provide a last name.' ),
									//	'contributor_email'				=> array( '/^([[:alnum:]][-a-zA-Z0-9_%\.]*)?[[:alnum:]]@[[:alnum:]][-a-zA-Z0-9%\.]*\.[[:alpha:]]{2,}$/', 'The email address you provided is not valid.' ),
									//	'contributor_contact_consent'	=> array( '/^(yes)$|^(no)$|^(unknown)$/', 'Contributors must be assigned valid contact consent [yes, no, or unknown].') 
									);

	public function __construct( $array = null )
	{
		if(isset($array['contributor_full_name']))
		{
			$this->parseName($array['contributor_full_name']);
		}
		parent::__construct($array);
	}
	
	public static function findIDBy( $col, $val )
	{
		$inst = new self;
		return $inst->mapper()->find( "contributor_id" )->where( "$col = ?", $val )->execute()->contributor_id;
	}

	public function uniqueEmail()
	{
		$mapper = $this->mapper();
		$res = $mapper->find()
						->where( 'contributor_email = ?', $this->contributor_email )
						->execute();
		if( $res->total() > 0 )
		{
			return false;
		}
		return true;
	}

	public function uniqueNameEmail()
	{
		$mapper = $this->mapper();
		$res = $mapper->find()
						->where( 'contributor_email = ?', $this->contributor_email )
						->where( 'contributor_first_name = ?', $this->contributor_first_name )
						->where( 'contributor_last_name = ?', $this->contributor_last_name )
						->execute();
		if( $res->total() > 0 )
		{
			return false;
		}
		return true;
	}
	
	public function parseName($fullname)
	{
		//	Parse the name of the contributor

		$parts = explode(",", $fullname);

		//If there is extra stuff in the name

			$name = explode(" ", trim($parts[0]));

			if(isset($parts[1]) ) {
				if(count($name) > 2) {

					$first = $name[0];
					unset($name[0]);
					$middle = @$name[1];
					unset($name[1]);
					$last = implode(" ", array_values($name) ) . ", " . @$parts[1];
				}
				else {
					$first = $name[0];
					unset($name[0]);
					$middle = 'NULL';
					$last = $name[1].", ".$parts[1];
				}
			}
			else {
				if(count($name) > 2) {
					$first = $name[0];
					unset($name[0]);
					$middle = @$name[1];
					unset($name[1]);
					$last = implode(" ", array_values($name) );
				}
				else {
					$first = $name[0];
					$middle = 'NULL';
					$last = @$name[1];
				}
			}
			$this->contributor_first_name = ($first) ? $first : 'NULL';
			$this->contributor_middle_name = ($middle) ? $middle : 'NULL';
			$this->contributor_last_name = ($last) ? $last : 'NULL';
	}
	
	public function isInstitution()
	{
		if ( property_exists(get_class($this), 'contributor_institution') )
		{
			if( !empty($this->contributor_institution) )
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	
	public function getName()
	{
		if( $this->isInstitution() )
		{
			return $this->contributor_institution;
		}
		else
		{
			return $this->contributor_first_name . ' ' . $this->contributor_last_name;
		}
	}
}

?>