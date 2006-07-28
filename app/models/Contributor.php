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
	public $contributor_jewish;
	public $contributor_religious_id;
	public $contributor_religious_id_other;
	public $contributor_location_during;
	public $contributor_location_evacuation;
	public $contributor_location_current;
	public $contributor_location_between;
	public $contributor_return;
	public $contributor_family_members;
	public $contributor_former_resident;
	public $contributor_community_evacuees;
	public $contributor_participate;
	public $contributor_other_relationship;
	public $contributor_residence;
	public $contributor_location_participate;
	
	protected $validate		=	array(	'contributor_first_name'		=> array( '/(\w)+/', 'Please provide a first name.' ),
										'contributor_last_name'			=> array( '/(\w)+/', 'Please provide a last name.' ),
										'contributor_contact_consent'	=> array( '/^(yes)$|^(no)$|^(unknown)$/', 'Contributors must be assigned valid contact consent [yes, no, or unknown].'),
										'contributor_email'				=> array( '/^([[:alnum:]][-a-zA-Z0-9_%\.]*)?[[:alnum:]]@[[:alnum:]][-a-zA-Z0-9%\.]*\.[[:alpha:]]{2,}$/', 'The email address you provided is not valid.' ) );

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

}

?>