<?php

// Change the 4 variables below
$to = $_POST['mailToEmail'];
$fromName = $_POST['mailFromName'];
$fromEmail = $_POST['mailFromEmail'];
$subject = $fromName.' wants to share something from the Hurricane Digital Memory Bank';
$message = stripslashes($_POST['mailMessage']).'
----------------------------------
'.$_POST['mailFromName'].' invites you to add a story, photo, or observation to the Hurricane Digital Memory Bank (link to:http://www.hurricanearchive.org).

The Hurricane Digital Memory Bank, http://www.hurricanearchive.org uses electronic media to collect, preserve, and present the stories and digital record of Hurricanes Katrina and Rita. The University of New Orleans and George Mason University’s Center for History and New Media, in partnership with the Smithsonian Institution’s National Museum of American History and other partners, organized this project.';

$referringPage = $_link->to( 'thankyou' );

// No need to edit below unless you really want to. It's using a simple php mail() function. Use your own if you want
	if ( isset($_POST['sendEmail']) )
	{

	$headers = "From: ".stripslashes($fromName)." <".$fromEmail.">\n";
//	if ($_POST['selfCC']) $headers .= 'Cc: '. $_POST['posEmail'] . "\r\n";
	$mailit = mail($to,$subject,$message,$headers);
		if ( @$mailit ) {
		header('Location: '.$referringPage.'?success=true');
		}
		else {
		header('Location: '.$referringPage.'?success=error');
		}
	}
?>
