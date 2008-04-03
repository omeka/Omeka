<?php
require_once '../paths.php';

try {
    $warning = array();
    
    $errors = array();
    
    //Check whether correct version of PHP is available
	if(version_compare("5.2.0", phpversion(), ">=")) {
	    $errors[] = array(
	        'header'=>'Incorrect version of PHP',
	        'msg'=>'Omeka requires PHP5.2 or greater to be installed.  <a href="http://www.php.net/manual/en/migration5.php">Instructions</a> for upgrading are on the PHP website.</a>');
	}

	//Check whether archive directories are writable
	if (!is_writable(FULLSIZE_DIR) || !is_writable(FILES_DIR) || !is_writable(THUMBNAIL_DIR) || !is_writable(SQUARE_THUMBNAIL_DIR)) {
	    $errors[] = array(
	        'header'=>'Archive directories not writable',
	        'msg'=>'The archive/ directory and its sub-directories must be writable by your web server before installing Omeka.');
	}
    
    //Check for the .htaccess file
	if(!file_exists(BASE_DIR.DIRECTORY_SEPARATOR.'.htaccess')) {
	    $errors[] = array(
	        'header'=>'Missing .htaccess File',
            'msg'=>'Omeka&apos;s .htaccess file is missing.  Please make sure this and any other hidden files, such as the .htaccess file in the admin/ directory, have been uploaded correctly and try again.');
	}
	
	//Register Globals should be turned off
	if((int) ini_get('register_globals')) {
	    $warning[] = array(
	        'header'=>'&quot;Register Globals&quot; is enabled', 
	        'msg'=>"Having PHP's 'register_globals' setting enabled represents a security risk to your Omeka installation.  Also, having this setting enabled might indicate that Omeka's .htaccess file is not being properly parsed by Apache, which can cause any number of strange errors.  It is recommended (but not required) that you disable register_globals for your Omeka installation.");
	}
    
    //Verify that mysqli is installed and available
    if(!function_exists('mysqli_get_server_version')) {
        $errors[] = array(
            'header'=>'Mysqli extension is not installed',
            'msg'=>'The mysqli PHP extension is required for Omeka to run.  Please check with your server administrator to enable this extension and then try again.');
    }
    
    //Verify that mod_rewrite is enabled (NOT WORK YET)
    $modRewriteUrl = WEB_ROOT.'/checkModRewrite.html';
   
    //We are trying to retrieve this URL    
    if( ini_get('allow_url_fopen') and !($modRewrite = @file_get_contents($modRewriteUrl)) ) {
        $errors[] = array(
            'header'=>'mod_rewrite is not enabled',
            'msg'=>"Apache's mod_rewrite extension must be enabled for Omeka to work properly.  Please enable mod_rewrite and try again.");
    }
    //If we can't use the http wrapper for file_get_contents(), warn that we were unable to check for mod_rewrite
    elseif(!ini_get('allow_url_fopen')) {
        $warning[] = array(
            'header'=>"Unable to check for mod_rewrite",
            'msg'=>'Unable to verify that mod_rewrite is enabled on your server.  mod_rewrite is an Apache extension that is required for Omeka to work properly.  Omeka is unable to check because your php.ini &quot;allow_url_fopen&quot; setting has been disabled.  You can manually verify that Omeka mod_rewrite by checking to see that the following URL works in your browser: '.$modRewriteUrl);
    }
    
function format_errors($errors, $status='error')
{
    $out = '';
    foreach ($errors as $error) {
        $header = ($status == 'error') ? "Error: " : "Warning: ";
        $out .= '<h1>' . $header . $error['header'] . '</h1>' . '<p>' . $error['msg'] . '</p>';
    }
    
    return $out;
}
	
	if(!empty($errors)) {
	    die(format_errors($errors, 'error'));
	}
	elseif(!empty($warning)) {
	    echo format_errors($warning, 'warning');
	}
	
} catch (Exception $e) {
    die($e->getMessage());
}