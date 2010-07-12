<?php head(array('title'=>'Omeka Upgrading')); ?>
<div id="primary">
<h2 class="instruction">Omeka is now upgrading itself.  Please refresh your screen once this page finishes loading.</h2>

<?php if(!$success): ?>
    <p class="error">Omeka encountered an error when upgrading your installation:</p>
	<p class="error_text"><?php echo html_escape($error); ?></p>
    
    <h2 class="instruction">Please restore from your database backup and try again.
        If you continue to experience errors, you can notify us on the Omeka 
        <a href="http://omeka.org/forums/">Forums</a>.</h2>
<?php else: ?>
    <p>Omeka has upgraded successfully.</p>
    <p><?php echo link_to_admin_home_page('Return to Dashboard'); ?></p>    
<?php endif; ?>	
</div>
<?php foot(); ?>