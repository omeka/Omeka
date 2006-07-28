<?php
// Layout: default;
$obj_count = $__c->objects()->total();
$file_count = $__c->files()->total();
?>

<style type="text/css" media="screen">
/* <![CDATA[ */
	#welcome-logo {width:200px; float:left;}
	#welcome-text {padding:8px; float:left;}
/* ]]> */
</style>

<div id="sub-navigation"></div>
<br/>
<div id="welcome-logo"><img src="<?php echo $_link->in( 'jwa-logo.gif', 'images' ); ?>" /></div>
<div id="welcome-text">
	<h1>Currently archiving:</h1>
	<br/>
	<h4><?php echo $obj_count; ?> Object<?php if( $obj_count != 1 && $obj_count >= 0 ) echo 's'; ?>,</h4>
	<h4>with <?php echo $file_count; ?> associated file<?php if($file_count != 1 && $file_count >= 0) echo 's'; ?>.</h4>
</div>
<br class="clear"/>