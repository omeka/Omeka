<?php
header ("HTTP/1.0 403 Forbidden"); 
head(array('bodyclass'=>'error403 primary', 'title'=>'Forbidden')); 
?>
<h1>403: Forbidden</h1>

<div id="primary" class="file-forbidden">
<?php echo flash(); ?>

<p>Sorry, because of your current user level (&quot;<?php echo html_escape(current_user()->role); ?>&quot;), you don't have permission to access this page. If you think you should have access to this page, contact the <a href="mailto:<?php echo settings('administrator_email'); ?>">site administrator</a>.</p>
</div>
<?php foot(); ?>