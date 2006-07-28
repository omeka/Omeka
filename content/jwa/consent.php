<?php
	$__c->public()->submitContribution();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Contact JWA | Katrina's Jewish Voices</title>
<?php include ('inc/metalinks.php'); ?>

<script type="text/javascript" src="<?php echo $_link->in( 'functionAddEvent.js', 'j/ajaxcontact' ); ?>"></script>
<script type="text/javascript" src="<?php echo $_link->in( 'contact.js', 'j/ajaxcontact' ); ?>"></script>
<script type="text/javascript" src="<?php echo $_link->in( 'xmlHttp.js', 'j/ajaxcontact' ); ?>"></script>
</head>

<body id="about" class="consent">
<a class="hide" href="#content">Skip to Content</a>
<div id="wrap">
	<div id="content">
		<div id="primary">
		<h3>Consent To Contribute Form</h3>

		<p>You are being asked to contribute to Katrina&#8217;s Jewish Voices, an online collecting project of the Jewish Women&#8217;s Archive (JWA) to create a permanent digital record of the Jewish experiences of Hurricane Katrina and recollections about the Jewish communities of New Orleans and the Gulf Coast.  Your participation in this project will help create a vital resource for historians of the American Jewish experience, as well as for scholars, researchers and others interested in exploring the ways that individuals and communities responded to this vast humanitarian crisis.</p>
		
		<?php if( !self::$_session->getUser() ): ?>
		<h3>Creation of a private user account</h3>
		
		<p>In addition to submitting your contribution, a user account will be generated based on the information you have provided on the previous forms. This account will allow you to submit more objects without entering the same information more than once, tag objects, and create collections of favorites. The details of this account will be sent to you at the email address you have provided.</p>
		<?php endif; ?>

		<h3>Terms of Consent:</h3>
		<ol class="terms-of-consent">
		<li>You must be 13 years of age or older to submit material(s) to Katrina&#8217;s Jewish Voices.</li>

		<li>Your submission of material constitutes your permission for, and consent to, its dissemination and use by JWA and persons acting under its permission or authority in connection with Katrina&#8217;s Jewish Voices in all existing and future media in perpetuity.  If you have so indicated on the contribution form, your material may be displayed publicly on the Katrina&#8217;s Jewish Voices website (with or without your name, depending on what you have indicated). Objects on the public website may be downloaded, printed and used for non-commercial, educational purposes with proper attribution as stipulated in the Terms of Use section. If you have stipulated that you do not want your contribution to be publicly available, it will become part of the Katrina&#8217;s Jewish Voices archive, but it will only be available to researchers approved by JWA.</li>

		<li>The material you submit must have been created by you, must be wholly original, and must not be copied from or based, in whole or in part, upon any other photographic, literary, or other material, except to the extent that (i) such material is in the public domain or (ii) you have obtained express written permission from the author or other copyright owner to submit such material for use and dissemination in connection with Katrina&#8217;s Jewish Voices. Please note that electronic materials (e.g., emails, blog entries, website postings, digital photos) are not in the public domain and are copyright protected, unless the author/creator expressly relinquishes copyright.  If at any time JWA is notified by a third party that the materials submitted by you may violate the rights of others, JWA may take appropriate action to prevent further access to or other use of such materials.</li>

		<li>Copyrights for materials in the Katrina&#8217;s Jewish Voices archive are retained by the original creators or their transferees. Copyright in the archive as a whole (i.e., the collective work), is and will continue to be owned by JWA.</li>


		<li>By submitting material to Katrina&#8217;s Jewish Voices you release, discharge, and agree to hold harmless JWA and persons acting under its permission or authority, including a public library or archive to which the collection might be donated for purposes of longterm preservation, from any claims or liability arising out of JWA&#8217;s use of the material, including, without limitation, claims for violation of privacy, copyright infringement, defamation, libel, or misrepresentation.</li> 

		<li>JWA has no obligation to use any materials submitted by you as part of Katrina&#8217;s Jewish Voices.</li>


		<li>You will be sent via email an electronic acknowledgment of your contribution to Katrina&#8217;s Jewish Voices.  JWA cannot return any material you submit to us.</li>

		<li>JWA will not share your email address or any of the other personal information accompanying your submission with any third parties, except as required by law or as provided in the privacy policy published on the Katrina&#8217;s Jewish Voices website.</li>
		</ol>


		<h3>Please select your consent below:</h3>
		<form method="post" action="<?php echo $_link->to('consent'); ?>">
		<?php
			$_form->radio( 'object_contributor_consent',
							array(	'yes'		=> 'I Agree.  Please include my contribution.',
									'unsure'	=> 'I am not sure if my contribution meets the legal requirements.  I understand that JWA may contact me for more information.',
									'restrict'	=> 'I would like to restrict access to my contribution.  Please contact me.',
									'no'		=> 'I do not agree.  Please cancel my submission.' ) );
		?>
		<input type="submit" name="submit_object_contributor_consent" value="Continue with submission &gt;&gt;"/>
		</form>

		</div>
	</div>
<?php include("inc/footer.php"); ?>