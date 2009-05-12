<?php head(); ?>
<div id="primary">
<h2 class="instruction">Omeka is now upgrading itself.  Please refresh your screen once this page finishes loading.</h2>

<?php if(!$success): ?>
    <p class="error">Omeka encountered an error when upgrading your installation:</p>
	
	<?php foreach( $errors as $num => $error ): ?>
	   <p class="error_text"><?php echo html_escape($error); ?></p>
	<?php endforeach; ?>

<?php endif; ?>	
<ul class="migrate">
<?php foreach( $output as $num => $text ): ?>
    <li>
    <?php foreach( $text as $out ): ?>
        <p><?php echo html_escape($out); ?></p>
    <?php endforeach; ?>
    </li>
<?php endforeach; ?>    
</ul>
<p><?php echo link_to_admin_home_page('Return to Dashboard'); ?></p>
</div>
<?php foot(); ?>