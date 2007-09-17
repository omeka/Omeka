<?php 
//Need to add descriptions for the metafields

$typeSql = "UPDATE types SET description = ? WHERE name = ? LIMIT 1;";

$mfSql = "UPDATE `metafields` SET `description` = ? WHERE `name` = ? LIMIT 1";

$typeUpdates = array();

$typeUpdates[0] = array('A resource containing textual messages and binary attachments sent electronically from one person to another or one person to many people.', 'Email');

$mfUpdates = array();

$mfUpdates[0] = array('The main body of the email, including all replied and forwarded text and headers.', 'Email Body');
$mfUpdates[1] = array('The content of the subject line of the email.', 'Subject Line');
$mfUpdates[2] = array('The name and email address of the person sending the email.', 'From');
$mfUpdates[3] = array('The name(s) and email address(es) of the person to whom the email was sent.', 'To');
$mfUpdates[4] = array('The name(s) and email address(es) of the person to whom the email was carbon copied.', 'CC');
$mfUpdates[5] = array('The name(s) and email address(es) of the person to whom the email was blind carbon copied.', 'BCC');
$mfUpdates[6] = array('The number of attachments to the email.', 'Number of Attachments');
$mfUpdates[7] = array('Length of time involved (seconds, minutes, hours, days, class periods, etc.)', 'Duration');
$mfUpdates[8] = array('Type/rate of compression for moving image file (i.e. MPEG-4)', 'Compression');
$mfUpdates[9] = array('Name (or names) of the person who produced the video.', 'Producer');
$mfUpdates[10] = array('Name (or names) of the person who directed the video.', 'Director');
$mfUpdates[11] = array('Rate at which bits are transferred (i.e. 96 kbit/s would be FM quality audio)', 'Bit Rate/Frequency');
$mfUpdates[12] = array('A summary of an interview given for different time stamps throughout the interview', 'Time Summary');
$mfUpdates[13] = array('If the image is of an object, state the type of object, such as painting, sculpture, paper, photo, and additional data', 'Original Format');
$mfUpdates[14] = array('The actual physical size of the original image.', 'Physical Dimensions');
$mfUpdates[15] = array('Names of individuals or groups participating in the event.', 'Participants');

foreach ($typeUpdates as $up) {
	$this->query($typeSql, $up);
}

foreach ($mfUpdates as $up) {
	$this->query($mfSql, $up);
}
 
?>
