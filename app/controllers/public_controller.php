<?php

class PublicController extends Kea_Action_Controller
{
	// this gets called by the contribute form
	protected function _getConsent()
	{
		if( self::$_request->getProperty( 'contribute_submit' ) )
		{
			// If the contribute form doesn't validate it will go back to the
			// contribute form.  If it does, it will create some objects, etc
			// and move on to the consent form.
			if( $data = $this->validateForm( self::$_request->getProperties() ) )
			{
				$adapter = Kea_DB_Adapter::instance();
				$adapter->beginTransaction();
				
				$params = self::$_request->getProperties();
				
				// Get the object, contributor and user from the validateForm method
				list( $object, $contributor, $user ) = $data;
				
				// If there is a contributor but its id isn't set, it's new so save it.
				if( empty( $contributor->contributor_id ) )
				{					
					$contributor->save();
				}

				if( !$user )
				{
					$user = User::newPublicUserFromContributor( $contributor );
					self::$_session->setValue( 'contributed_user', $user );
				}
				// If there's a user logged in but the user has no contributor_id, associate the one we just made with the user
				else
				{
					$user->contributor_id = $contributor->getId();
					$user->save();
				}
				
				// Set the consent to unknown until we get to the consent form
				$object->object_contributor_consent = 'unknown';

				// Set the object's contributor_id to the newly created contributor id.
				$object->contributor_id = $contributor->getId();

				// Set the object's user_id to the new user id
				$object->user_id = $user->getId();

				if( $params[ 'object_creator' ] == 'yes' )
				{
					$object->creator_id = $contributor->getId();
					$object->object_creator_other = 'NULL';
				}
				else
				{
					$object->creator_id = 'NULL';
				}

				// Save the object
				$object->save();

				//Handle the object type
				if( isset( $params[ 'online_story_text' ] ) )
				{
					$m = new Metatext;
					// Hard code this to the metafield number
					$m->metafield_id = 5;
					$m->object_id = $object->getId();
					$m->metatext_text = $params[ 'online_story_text' ];
					$m->save();

					$object->category_id = 8;
					$object->save();
				}

				if( isset( $params[ 'MAX_FILE_SIZE' ] ) )
				{
					$files = File::add( $object->getId(), $contributor->getId(), 'objectfile' );
					self::$_session->setValue( 'contributed_files', $files );
					$object->category_id = 7;
					$object->save();
				}

				$tags = new Tags( $params[ 'object_tags' ] );
				$tags->object_id = $object->object_id;
				$tags->user_id = $user->getId();

				$tags->save();

				$location = new Location( $params[ 'Location' ] );

				// Set the location data's object_id to the object_id
				if( !empty( $location->address ) ||
					!empty( $location->zipcode ) ||
					!empty( $location->cleanAddress ) ||
					!empty( $location->latitude ) ||
					!empty( $location->longitude ) )
				{
					$location->object_id = $object->getId();

					// Save the location data
					$location->save();	
				}
				$adapter->commit();
				
				self::$_session->setValue( 'contributed_object', $object );
				$this->redirect( BASE_URI . DS . 'consent' );
				return;
			}
		}
		return;
	}
	
	// this is called by the consent form
	protected function _submitContribution()
	{		
		
		if( $object_consent = self::$_request->getProperty( 'object_contributor_consent' ) )
		{
			$object = self::$_session->getValue( 'contributed_object' );
			$files = self::$_session->getValue( 'contributed_files' );
			
			if( $object_consent == 'no' )
			{
				self::$_session->unsetValue( 'contributed_object' );
				if( $files )
				{
					foreach( $files as $file )
					{
						File::delete( $file->getId() );
					}
				}
				
				$object->delete();
				$this->redirect( BASE_URI . DS . 'contribute' );
				return;
			}
			
			if( self::$_session->getValue( 'contributed_user' ) )
			{
				self::$_session->loginUser( self::$_session->getValue( 'contributed_user' ) );
				self::$_session->unsetValue( 'contributed_user' );
			}
			
			$object->object_contributor_consent = $object_consent;
			$object->save();
			
			
			// Send e-mail
			$message = "Thank you for your contribution to Katrina's Jewish Voices. Your contribution has been accepted and will be preserved in the digital archive. For your records, the permanent URL for your contribution is noted at the end of this email. Please note that contributions may not appear immediately on the website while they await processing by project staff. Please pass word of Katrina's Jewish Voices along to family, friends, and associates - it is important to save as much of the historical record as possible.

			Katrina's Jewish Voices
			An Online Collecting Project of the Jewish Women's Archive
			138 Harvard Street
			Brookline, MA 02446
			617-383-6754
			www.jwa.org

Contribution URL (pending review by project staff): http://".$_SERVER['SERVER_NAME'] . substr($_SERVER['PHP_SELF'] , 0, strrpos($_SERVER['PHP_SELF'], '/')) . DS .'object' . DS .self::$_session->getValue( 'contributed_object' )->object_id;
			$title = "Your Katrina's Jewish Voices Contribution";
			$header = 'From: webmaster@jwa.org' . "\n" . 'X-Mailer: PHP/' . phpversion();

			mail( self::$_session->getUser()->getEmail(), $title, $message, $header);

			self::$_session->unsetValue( 'contributed_object' );
			if( self::$_session->getValue( 'contributed_files' ) )
			{
				self::$_session->unsetValue( 'contributed_files' );
			}

			$this->redirect( BASE_URI . DS . 'thanks' );
			return;

		}
	}
	
	private function validateForm( $params )
	{
			// Get the object being contributed
			$object = new Object( $params['Object'] );
			
			//print_r($_FILES['objectfile']['name']); exit;

			$date = $params['date'];
			$day = (int) $date['day'];
			$month = (int) $date['month'];
			$year = (int) $date['year'];
			
			if( !empty( $day ) || !empty( $month ) || !empty( $year ) )
			{
				$object->object_date = date( 'Y-m-d H:i:s', mktime( null, null, null, $month, $day, $year ) );
			}
			
			$object->object_contributor_consent = 'no';
			
			$object->object_status = 'notyet';
			
			$this->validates( $object );
			
			// Check to see if they have added a title
			if( empty( $object->object_title ) )
			{
				$this->addError( 'Object', 'empty_object_title', 'You must enter a title.' );
			}
			
			// Check to see if they have added a story or some files
			//if( empty( $params['online_story_text'] ) && empty( $params['MAX_FILE_SIZE'] ) )
			if( empty( $params['online_story_text'] ) && empty( $_FILES['objectfile']['name'][0] ) )
			{
				$this->addError( 'Object', 'empty_object_story_text', 'You must enter a story.' );
				$this->addError( 'Object', 'empty_object_file', 'You must select a file.' );
			}
			
			// Check to see if they've chosen a type to contribute at all
			if( empty( $params['MAX_FILE_SIZE'] ) && empty( $params['story_form'] ) )
			{
				$this->addError( 'Object', 'empty_object_type', 'You must choose a type of contribution.' );
			}
			
			
			// Contributor issues
			// First, is the user logged in, and a contributor?
			if( self::$_session->getUser() && self::$_session->getUser()->isContributor() )
			{
				$contributor = self::$_session->getUser()->getContributor();
				$user = self::$_session->getUser();
			}
			// Next, is the user logged in but not a contributor?
			elseif( self::$_session->getUser() )
			{
				$user = self::$_session->getUser();

				$contributor = new Contributor( $params['Contributor'] );
				$contributor->contributor_email = $user->user_email;

				switch( $params[ 'contributor_contact_consent' ] )
				{
					case( 'yes' ):
						$contributor->contributor_contact_consent = 'yes';
					break;
					case( false ):
						$contributor->contributor_contact_consent = 'no';
					break;
				}
			}
			// Finally, the user is neither logged in nor a contributor,
			// so we are creating it from the form data
			else
			{
				$contributor = new Contributor( $params['Contributor' ] );

				if( $contributor->contributor_email != $params[ 'contributor_email_check' ] )
				{
					$this->addError( 'Contributor', 'email_check', 'The emails you provided do not match.' );
				}

				switch( $params[ 'contributor_contact_consent' ] )
				{
					case( 'yes' ):
						$contributor->contributor_contact_consent = 'yes';
					break;
					case( false ):
						$contributor->contributor_contact_consent = 'no';
					break;
				}

				if( empty( $contributor->contributor_birth_year ) )
				{
					$contributor->contributor_birth_year = 'NULL';
				}

				if( empty( $contributor->contributor_gender ) )
				{
					$contributor->contributor_gender = 'unknown';
				}

				// If the user is not logged in, but there exists a user of the same email / last name
				// redirect and have them log in
				if( !$contributor->uniqueEmail() )
				{
					self::$_session->setValue( 'contribute_form_need_login', $_REQUEST );
					$this->redirect( BASE_URI . DS . 'login' );
					return;
				}
				
				$user = false;
			}

			$this->validates( $contributor );

			if( count( $this->validationErrors ) > 0 )
			{
				return false;
			}
			return array( $object, $contributor, $user );
	}

}

?>