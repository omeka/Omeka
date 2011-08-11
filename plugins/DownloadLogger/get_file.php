<?php
error_reporting(E_ALL ^ E_NOTICE);
ini_set("display_errors", 'on');

include '../../paths.php';
require_once '../../application/libraries/Omeka/Context.php';
require_once '../../application/libraries/Zend/Application.php';
require_once '../../application/libraries/Omeka/Core.php';
require_once '../../application/models/File.php';
require_once '../../application/libraries/Omeka/Validator/Exception.php';
require_once '../../application/libraries/Zend/Application/Exception.php';


$file_name = $_REQUEST['fn'];
if (empty($file_name))
    throw new Omeka_Validator_Exception('Incorrect file name');

$dl = new DownloadLogger();
$file = $dl->getFile($file_name);
if ($file == NULL)
    throw new Omeka_Validator_Exception('Could not find file');
    
$file_name = '../../archive/files/'.$file_name;
if (!file_exists($file_name))
    throw new Omeka_Validator_Exception('Incorrect file name: '.$file_name);
   
if ($dl->log($file, getRemoteIP(), getGuestName(getRemoteIP())) == NULL)
    throw new Omeka_Validator_Exception('Can not log file download');

//header ( "Location: $file_name?tp=dirrect_download&t=".time() );
//exit;

header ( 'Cache-control: max-age=31536000' );
header ( 'Expires: Mon, 01 Jan 1990 05:00:00 GMT' );
if (($file_size = @filesize ( $file_name )) === FALSE)
   	throw new Zend_Application_Exception($file->original_filename.' file size failed', 101);
header ( 'Content-Length: '.$file_size );
header ( 'Content-Type: '.$file->mime_browser.'; name="'.$file->original_filename.'"' );
header ( 'Content-Disposition:attachment; filename="'.$file->original_filename.'"' );
header ( 'Content-Transfer-Encoding: binary' );
//if (@readfile ($file_name) === FALSE)
//    throw new Zend_Application_Exception($file->original_filename.' file reading failed', 101);
$fh = fopen($file_name, 'r');
if (!$fh)
	throw new Zend_Application_Exception($file->original_filename.' file reading failed', 101);
while (ftell($fh)<$file_size)
{
	echo fread($fh, 1024);
}
fclose($fh);
exit ();


/**
 * Get remote ip address if user behind nginx
 */
function getRemoteIP()
{
	if (array_key_exists('HTTP_X_REAL_IP', $_SERVER))
		return $_SERVER['HTTP_X_REAL_IP'];
	return $_SERVER['REMOTE_ADDR'];
}


function getGuestName($ip)
{
// get user name from TrafficPanel
//    $data = file_get_contents("http://192.168.1.201:8585/cgi-bin/get_ip_data.pl?ip=$ip");
//    if (!empty($data))
//    {
//    	$data = explode("|", $data);
//    	return $data[0];
//    }
    return "Anonymous [$ip]";
}
